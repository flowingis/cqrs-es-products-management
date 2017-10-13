<?php

namespace Shop\Product;


use Broadway\CommandHandling\SimpleCommandHandler;
use Shop\Product\Aggregate\Product;
use Shop\Product\Command\CreateProduct;
use Shop\Product\Command\UpdateProduct;

class ProductCommandHandler extends SimpleCommandHandler
{
    /**
     * @var \Broadway\Repository\Repository
     */
    private $repository;

    public function __construct(\Broadway\Repository\Repository $repository)
    {
        $this->repository = $repository;
    }

    public function handleCreateProduct(CreateProduct $command)
    {
        $product = Product::create($command);

        $this->repository->save($product);
    }

    public function handleUpdateProduct(UpdateProduct $command)
    {
        /** @var Product $product */
        $product = $this->repository->load($command->getProductId());

        $product->update($command);

        $this->repository->save($product);
    }
}
