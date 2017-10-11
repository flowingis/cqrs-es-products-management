<?php
namespace Tests\Shop\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\EventHandling\EventBus;
use Broadway\EventStore\EventStore;
use Shop\Product\Repository;

class CreateProductTest extends \Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase
{
    /**
     * @test
     */
    public function should_create_a_product()
    {
        
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

//        return new ProductCommandHandler($repository);
    }
}
