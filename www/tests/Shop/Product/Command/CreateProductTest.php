<?php
namespace Tests\Shop\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBus;
use Broadway\EventStore\EventStore;
use Shop\Product\Command\CreateProduct;
use Shop\Product\Event\ProductCreated;
use Shop\Product\ProductCommandHandler;
use Shop\Product\Repository;

class CreateProductTest extends CommandHandlerScenarioTestCase
{
    /**
     * @test
     */
    public function should_create_a_product()
    {
        $createProduct = new CreateProduct(
            '00000000-0000-0000-0000-000000000321',
            '5707055029608',
            'Nome prodotto: Scaaarpe',
            'http://static.politifact.com.s3.amazonaws.com/subjects/mugs/fake.png',
            'Brand prodotto: Super Scaaaarpe',
            new \DateTimeImmutable('2017-02-14')
        );

        $this->scenario
            ->given([])
            ->when($createProduct)
            ->then(
                [
                    new ProductCreated(
                        $createProduct->getProductId(),
                        $createProduct->getBarcode(),
                        $createProduct->getName(),
                        $createProduct->getImageurl(),
                        $createProduct->getBrand(),
                        $createProduct->getCreatedAt()
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
