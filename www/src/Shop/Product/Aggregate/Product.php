<?php

namespace Shop\Product\Aggregate;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Shop\Product\Command\CreateProduct;
use Shop\Product\Command\UpdateProduct;
use Shop\Product\Event\ProductCreated;
use Shop\Product\Event\ProductUpdated;

class Product extends EventSourcedAggregateRoot
{
    private $id;

    public static function create(CreateProduct $command)
    {
        $product = new self();
        $product->apply(
            new ProductCreated(
                $command->getProductId(),
                $command->getBarcode(),
                $command->getName(),
                $command->getImageurl(),
                $command->getBrand(),
                $command->getCreatedAt()
            )
        );

        return $product;
    }

    public function update(UpdateProduct $command)
    {
        $this->apply(new ProductUpdated(
            $command->getProductId(),
            $command->getBarcode(),
            $command->getName(),
            $command->getImageurl(),
            $command->getBrand(),
            $command->getCreatedAt()
        ));
    }

    protected function applyProductCreated(ProductCreated $event)
    {
        $this->id = $event->getProductId();
    }

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->id;
    }
}
