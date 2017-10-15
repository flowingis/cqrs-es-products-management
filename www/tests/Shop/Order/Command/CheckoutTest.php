<?php
namespace Tests\Shop\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBus;
use Broadway\EventStore\EventStore;
use Shop\Order\Command\Checkout;
use Shop\Order\Event\OrderConfirmed;
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
