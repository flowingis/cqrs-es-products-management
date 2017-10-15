<?php

namespace Shop\Order;

use Broadway\EventHandling\EventBus;
use Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStore;


class Repository extends EventSourcingRepository
{
    public function __construct(
        EventStore $eventStore,
        EventBus $eventBus
    ) {
        parent::__construct(
            $eventStore,
            $eventBus,
            '\Shop\Order\Aggregate\Order',
            new PublicConstructorAggregateFactory());
    }
}
