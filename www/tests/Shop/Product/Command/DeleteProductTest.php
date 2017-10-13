<?php
namespace Tests\Shop\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBus;
use Broadway\EventStore\EventStore;
use Shop\Product\Command\DeleteProduct;
use Shop\Product\Event\ProductCreated;
use Shop\Product\Event\ProductDeleted;
use Shop\Product\ProductCommandHandler;
use Shop\Product\Repository;
use Shop\Product\ValueObject\ProductId;

class DeleteProductTest extends CommandHandlerScenarioTestCase
{
    /**
     * @test
     */
    public function should_delete_a_product()
    {
        $productId = new ProductId('00000000-0000-0000-0000-000000000321');
        $productCreated = new ProductCreated(
            $productId,
            '5707055029608',
            'Nome prodotto: Scaaarpe',
            'http://static.politifact.com.s3.amazonaws.com/subjects/mugs/fake.png',
            'Brand prodotto: Super Scaaaarpe',
            new \DateTimeImmutable('2017-02-14')
        );

        $deleteProduct = new DeleteProduct(
            $productId,
            new \DateTimeImmutable('2017-03-14')
        );

        $this->scenario
            ->withAggregateId($productId)
            ->given([$productCreated])
            ->when($deleteProduct)
            ->then(
                [
                    new ProductDeleted(
                        $productId,
                        new \DateTimeImmutable('2017-03-14')
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

        return new ProductCommandHandler($repository);
    }
}
