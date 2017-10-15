<?php
namespace Tests\Shop\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBus;
use Broadway\EventStore\EventStore;
use Shop\Order\Command\CreateOrder;
use Shop\Order\Event\OrderCreated;
use Shop\Order\Repository;
use Shop\Order\ValueObject\OrderId;
use Shop\Order\ValueObject\OrderItem;
use Shop\Order\OrderCommandHandler;
use Shop\Product\ValueObject\ProductId;

class CreateOrderTest extends CommandHandlerScenarioTestCase
{
    /**
     * @test
     */
    public function should_create_an_order()
    {
        $orderItem1 = new OrderItem(
            new ProductId('00000000-0000-0000-0000-000000000322'),
            5
        );
        $orderItem2 = new OrderItem(
            new ProductId('00000000-0000-0000-0000-000000000323'),
            2
        );
        $createOrder = new CreateOrder(
            new OrderId('00000000-0000-0000-0000-000000000321'),
            100,
            [$orderItem1, $orderItem2],
            new \DateTimeImmutable('2017-02-14')
        );

        $this->scenario
            ->given([])
            ->when($createOrder)
            ->then(
                [
                    new OrderCreated(
                        $createOrder->getOrderId(),
                        $createOrder->getTotalCost(),
                        $createOrder->getItems(),
                        $createOrder->getCreatedAt()
                    )
                ]
            );
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
