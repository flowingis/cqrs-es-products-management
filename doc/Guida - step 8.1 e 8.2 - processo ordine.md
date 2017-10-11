Step 8.1 e 8.2: processo ordine
=======================

Valutare se vale la pena mostrare tutto quanto il codice prima e poi provare a riscriverlo.

Per la scrittura di questa parte:

Partire dalla commit 8.1 della creazione ordine per risparmiare tempo.

Aggiungere i file:
	- \Tests\Shop\Command\CheckoutTest
    - \Shop\Order\Command\Checkout
    - \Shop\Order\Event\OrderPaymentRequested
    - \Shop\Order\OrderCommandHandler
    - \Shop\Order\Aggregate\Order
    - \Shop\Order\Exception\CheckoutDenied
    - \Shop\Order\Event\OrderCreated


```
<?php
namespace Tests\Shop\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBus;
use Broadway\EventStore\EventStore;
use Shop\Order\Command\Checkout;
use Shop\Order\Event\OrderCreated;
use Shop\Order\Event\OrderPaymentRequested;
use Shop\Order\Repository;
use Shop\Order\ValueObject\OrderId;
use Shop\Order\ValueObject\OrderItem;
use Shop\Order\OrderCommandHandler;
use Shop\Product\ValueObject\ProductId;

class CheckoutTest extends CommandHandlerScenarioTestCase
{
    /**
     * @test
     */
    public function should_checkout_an_order()
    {
        $orderId = new OrderId('00000000-0000-0000-0000-000000000321');
        $totalCost = 100;
        $checkout = new Checkout(
            $orderId,
            $totalCost,
            new \DateTimeImmutable('2017-02-14')
        );
        $orderItem1 = new OrderItem(
            new ProductId('00000000-0000-0000-0000-000000000322'),
            5
        );
        $orderItem2 = new OrderItem(
            new ProductId('00000000-0000-0000-0000-000000000323'),
            2
        );

        $this->scenario
            ->withAggregateId($orderId)
            ->given([
                new OrderCreated(
                    $orderId,
                    $totalCost,
                    [$orderItem1, $orderItem2],
                    new \DateTimeImmutable('2017-02-13')
                )
            ])
            ->when($checkout)
            ->then(
                [
                    new OrderPaymentRequested(
                        $checkout->getOrderId(),
                        $checkout->getTotalCost(),
                        $checkout->getRequestedAt()
                    )
                ]
            );
    }

    /**
     * @test
     * @expectedException \Shop\Order\Exception\CheckoutDenied
     */
    public function should_not_checkout_an_order_if_total_cost_is_not_equals_to_checkout_total_cost()
    {
        $orderId = new OrderId('00000000-0000-0000-0000-000000000321');
        $checkoutTotalCost = 100;
        $orderTotalCost = 101;
        $checkout = new Checkout(
            $orderId,
            $checkoutTotalCost,
            new \DateTimeImmutable('2017-02-14')
        );
        $orderItem1 = new OrderItem(
            new ProductId('00000000-0000-0000-0000-000000000322'),
            5
        );

        $this->scenario
            ->withAggregateId($orderId)
            ->given([
                new OrderCreated(
                    $orderId,
                    $orderTotalCost,
                    [$orderItem1],
                    new \DateTimeImmutable('2017-02-13')
                )
            ])
            ->when($checkout);
    }

    /**
     * Create a command handler for the given scenario test case.
     *
     * @param EventStore $eventStore
     * @param EventBus   $eventBus
     *
     * @return CommandHandler
     */
    protected function createCommandHandler(EventStore $eventStore, EventBus $eventBus)
    {
        $repository = new Repository($eventStore, $eventBus);

        return new OrderCommandHandler($repository);
    }
}
```

```
<?php

namespace Shop\Order\Exception;


class CheckoutDenied extends \DomainException
{

}
```

```
<?php

namespace Shop\Order\Event;


use Broadway\Serializer\Serializable;
use Shop\Order\ValueObject\OrderId;

class OrderPaymentRequested implements Serializable
{
    private $orderId;
    private $requestedAt;
    /**
     * @var
     */
    private $totalCost;

    public function __construct(OrderId $orderId, $totalCost, \DateTimeImmutable $requestedAt)
    {
        $this->orderId = $orderId;
        $this->requestedAt = $requestedAt;
        $this->totalCost = $totalCost;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    /**
     * @return mixed
     */
    public function getTotalCost()
    {
        return $this->totalCost;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            new OrderId($data['orderId']),
            $data['totalCost'],
            new \DateTimeImmutable($data['requestedAt'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'orderId'   => (string)$this->orderId,
            'totalCost' => $this->totalCost,
            'requestedAt' => $this->requestedAt->format('Y-m-d\TH:i:s.uP')
        ];
    }
}
```

Aggiungere a OrderCreated:

```
   /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return mixed
     */
    public function getTotalCost()
    {
        return $this->totalCost;
    }
```


```
<?php

namespace Shop\Order\Command;

use Shop\Order\ValueObject\OrderId;

class Checkout
{
    /**
     * @var OrderId
     */
    private $orderId;
    private $totalCost;
    /**
     * @var \DateTimeImmutable
     */
    private $requestedAt;

    public function __construct(OrderId $orderId, $totalCost, \DateTimeImmutable $requestedAt)
    {
        $this->orderId = $orderId;
        $this->totalCost = $totalCost;
        $this->requestedAt = $requestedAt;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return mixed
     */
    public function getTotalCost()
    {
        return $this->totalCost;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }
}
```

Aggiungere a Order aggregato:

```
    /**
     * @var \DateTimeImmutable
     */
    private $confirmedAt;

...

    public function checkout(Checkout $command)
    {
        if($this->confirmedAt) {
            throw new CheckoutDenied();
        }

        $this->apply(new OrderPaymentRequested(
            $command->getOrderId(),
            $command->getTotalCost(),
            $command->getRequestedAt()
        ));
    }

    protected function applyOrderCreated(OrderCreated $event)
    {
        $this->id = $event->getOrderId();
        $this->totalCost = $event->getTotalCost();
    }
```

Aggiungere a OrderCommandHandler:

```
    public function handleCheckout(Checkout $command)
    {
        /** @var Order $order */
        $order = $this->repository->load($command->getOrderId());

        $order->checkout($command);

        $this->repository->save($order);
    }
``

