<?php
namespace Tests\Shop\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBus;
use Broadway\EventStore\EventStore;
use Shop\Product\Command\UpdateProduct;
use Shop\Product\Event\ProductCreated;
use Shop\Product\Event\ProductUpdated;
use Shop\Product\ProductCommandHandler;
use Shop\Product\Repository;
use Shop\Product\ValueObject\ProductId;

class UpdateProductTest extends CommandHandlerScenarioTestCase
{
    /**
     * @test
     */
    public function should_update_a_product()
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

        $updateProduct = new UpdateProduct(
            $productId,
            '5707055029609',
            'Nome prodotto: Scaaarpe più belle',
            'http://static.politifact.com.s3.amazonaws.com/subjects/mugs/fake11.png',
            'Brand prodotto: Super Scaaaarpe più belle',
            new \DateTimeImmutable('2017-03-14')
        );

        $this->scenario
            ->withAggregateId($productId)
            ->given([$productCreated])
            ->when($updateProduct)
            ->then(
                [
                    new ProductUpdated(
                        $updateProduct->getProductId(),
                        $updateProduct->getBarcode(),
                        $updateProduct->getName(),
                        $updateProduct->getImageurl(),
                        $updateProduct->getBrand(),
                        $updateProduct->getCreatedAt()
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
