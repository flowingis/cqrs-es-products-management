Step 8.3: processo ordine
=======================


Aggiungere i file:
	- Shop/Order/Command/ConfirmOrder.php          
    - Shop/Order/Event/OrderPaymentRequested.php   
    - Shop/Order/Process/CheckoutProcess.php       
    - Shop/Payment/Command/Pay.php                 
    - Shop/Payment/Event/PaymentDone.php           
    - Shop/Payment/ValueObject/PaymentId.php       
    - tests/Shop/Order/Process/CheckoutProcessTest.php


```
<?php

namespace Tests\Shop\Order\Process;


use Broadway\CommandHandling\Testing\TraceableCommandBus;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Shop\Order\Command\ConfirmOrder;
use Shop\Order\Event\OrderPaymentRequested;
use Shop\Order\Process\CheckoutProcess;
use Shop\Order\ValueObject\OrderId;
use Shop\Payment\Command\Pay;
use Shop\Payment\Event\PaymentDone;
use Shop\Payment\ValueObject\PaymentId;
use Tests\Utils\Processor\ProcessorScenarioTestCase;

class CheckoutProcessTest extends ProcessorScenarioTestCase
{
    /**
     * @test
     */
    public function should_pay_on_order_payment_request()
    {
        $totalCost = 100;
        $this->scenario
            ->when(
                new OrderPaymentRequested(
                    new OrderId('00000000-0000-0000-0000-000000000321'),
                    $totalCost,
                    new \DateTimeImmutable('2017-06-14')
                )
            )
            ->then([
                new Pay(
                    new PaymentId('00000000-0000-0000-0000-000000000421'),
                    new OrderId('00000000-0000-0000-0000-000000000321'),
                    $totalCost,
                    new \DateTimeImmutable('2017-06-14')
                ),
            ]);
    }

    /**
     * @test
     */
    public function should_confirm_order_on_order_payment_done()
    {
        $this->scenario
            ->when(
                new PaymentDone(
                    new PaymentId('00000000-0000-0000-0000-000000000421'),
                    new OrderId('00000000-0000-0000-0000-000000000321'),
                    new \DateTimeImmutable('2017-06-14')
                )
            )
            ->then([
                new ConfirmOrder(
                    new OrderId('00000000-0000-0000-0000-000000000321'),
                    new PaymentId('00000000-0000-0000-0000-000000000421'),
                    new \DateTimeImmutable('2017-06-14')
                ),
            ]);
    }

    /**
     * @param TraceableCommandBus $traceableCommandBus
     *
     * @return mixed
     */
    protected function createProcessor(TraceableCommandBus $traceableCommandBus)
    {
        /** @var UuidGeneratorInterface $uuidGeneratorStub */
        $uuidGeneratorStub = $this->prophesize(\Broadway\UuidGenerator\UuidGeneratorInterface::class);

        $uuidGeneratorStub->generate()->willReturn('00000000-0000-0000-0000-000000000421');

        return new CheckoutProcess($traceableCommandBus, $uuidGeneratorStub->reveal());
    }
}
```

```
<?php

namespace Shop\Payment\ValueObject;


use Assert\Assertion;

class PaymentId
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * ProductId constructor.
     *
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        Assertion::uuid($uuid);
        $this->uuid = $uuid;
    }

    public function __toString()
    {
        return $this->uuid;
    }
}
```

```
<?php

namespace Shop\Payment\Event;


use Broadway\Serializer\Serializable;
use Shop\Order\ValueObject\OrderId;
use Shop\Payment\ValueObject\PaymentId;

class PaymentDone implements Serializable
{
    /**
     * @var PaymentId
     */
    private $paymentId;

    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var \DateTimeImmutable
     */
    private $doneAt;

    public function __construct(PaymentId $paymentId, OrderId $orderId, \DateTimeImmutable $doneAt)
    {
        $this->paymentId = $paymentId;
        $this->orderId = $orderId;
        $this->doneAt = $doneAt;
    }

    /**
     * @return PaymentId
     */
    public function getPaymentId()
    {
        return $this->paymentId;
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
    public function getDoneAt()
    {
        return $this->doneAt;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            new PaymentId($data['paymentId']),
            new OrderId($data['orderId']),
            new \DateTimeImmutable($data['doneAt'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'paymentId' => (string)$this->paymentId,
            'orderId' => (string)$this->orderId,
            'doneAt' => $this->doneAt->format('Y-m-d\TH:i:s.uP')
        ];
    }
}
```

```
<?php

namespace Shop\Payment\Command;


use Shop\Order\ValueObject\OrderId;
use Shop\Payment\ValueObject\PaymentId;

class Pay
{
    /**
     * @var PaymentId
     */
    private $paymentId;
    /**
     * @var OrderId
     */
    private $orderId;
    private $totalCost;
    /**
     * @var \DateTimeImmutable
     */
    private $requestedAt;

    public function __construct(PaymentId $paymentId, OrderId $orderId, $totalCost, \DateTimeImmutable $requestedAt)
    {
        $this->paymentId = $paymentId;
        $this->orderId = $orderId;
        $this->totalCost = $totalCost;
        $this->requestedAt = $requestedAt;
    }

    /**
     * @return PaymentId
     */
    public function getPaymentId()
    {
        return $this->paymentId;
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

```
<?php

namespace Shop\Order\Process;

use Broadway\Processor\Processor;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Shop\Order\Command\ConfirmOrder;
use Shop\Order\Event\OrderPaymentRequested;
use Shop\Payment\Command\Pay;
use Shop\Payment\Event\PaymentDone;
use Shop\Payment\ValueObject\PaymentId;

class CheckoutProcess extends Processor
{
    /**
     * @var \Broadway\CommandHandling\CommandBus
     */
    private $commandBus;
    /**
     * @var UuidGeneratorInterface
     */
    private $uuidGenerator;

    public function __construct(\Broadway\CommandHandling\CommandBus $commandBus, UuidGeneratorInterface $uuidGenerator)
    {
        $this->commandBus = $commandBus;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function handleOrderPaymentRequested(OrderPaymentRequested $event)
    {
        $this->commandBus->dispatch(new Pay(
            new PaymentId($this->uuidGenerator->generate()),
            $event->getOrderId(),
            $event->getTotalCost(),
            $event->getRequestedAt()
        ));
    }

    public function handlePaymentDone(PaymentDone $event)
    {
        $this->commandBus->dispatch(new ConfirmOrder(
            $event->getOrderId(),
            $event->getPaymentId(),
            $event->getDoneAt()
        ));
    }
}
```

Aggiungere a OrderPaymentRequested:

```
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
```


```
<?php

namespace Shop\Order\Command;

use Shop\Order\ValueObject\OrderId;
use Shop\Payment\ValueObject\PaymentId;

class ConfirmOrder
{
    /**
     * @var OrderId
     */
    private $orderId;
    private $paymentId;
    /**
     * @var \DateTimeImmutable
     */
    private $confirmedAt;

    public function __construct(OrderId $orderId, PaymentId $paymentId, \DateTimeImmutable $confirmedAt)
    {
        $this->orderId = $orderId;
        $this->paymentId = $paymentId;
        $this->confirmedAt = $confirmedAt;
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return PaymentId
     */
    public function getPaymentId(): PaymentId
    {
        return $this->paymentId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getConfirmedAt(): \DateTimeImmutable
    {
        return $this->confirmedAt;
    }
}
```
