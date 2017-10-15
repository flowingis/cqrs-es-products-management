<?php

namespace Shop\Order\Aggregate;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Shop\Order\Command\CreateOrder;
use Shop\Order\Event\OrderCreated;
use Shop\Product\Command\CreateProduct;
use Shop\Product\Command\DeleteProduct;
use Shop\Product\Command\UpdateProduct;
use Shop\Product\Event\ProductCreated;
use Shop\Product\Event\ProductDeleted;
use Shop\Product\Event\ProductUpdated;

class Order extends EventSourcedAggregateRoot
{
    private $id;

    public static function create(CreateOrder $command)
    {
        $product = new self();
        $product->apply(
            new OrderCreated(
                $command->getOrderId(),
                $command->getTotalCost(),
                $command->getItems(),
                $command->getCreatedAt()
            )
        );

        return $product;
    }

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
//        return $this->id;
    }
}
