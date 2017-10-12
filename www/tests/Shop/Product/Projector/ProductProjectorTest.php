<?php

namespace Tests\Shop\Product\ValueObject;

use Broadway\ReadModel\InMemory\InMemoryRepository;
use Broadway\ReadModel\Projector;
use Broadway\ReadModel\Testing\ProjectorScenarioTestCase;
use Shop\Product\Event\ProductCreated;
use Shop\Product\Projector\ProductProjector;
use Shop\Product\ReadModel\Product;
use Shop\Product\ValueObject\ProductId;

class ProductProjectorTest extends ProjectorScenarioTestCase
{
    /**
     * @test
     */
    public function should_create_a_product()
    {
        $productCreated = new ProductCreated(
            new ProductId('00000000-0000-0000-0000-000000000321'),
            '5707055029608',
            'Nome prodotto: Scaaarpe',
            'http://static.politifact.com.s3.amazonaws.com/subjects/mugs/fake.png',
            'Brand prodotto: Super Scaaaarpe',
            new \DateTimeImmutable('2017-02-14')
        );

        $product = new Product();

        $product->setProductId($productCreated->getProductId());
        $product->setBarcode($productCreated->getBarcode());
        $product->setName($productCreated->getName());
        $product->setImageUrl($productCreated->getImageUrl());
        $product->setBrand($productCreated->getBrand());
        $product->setCreatedAt($productCreated->getCreatedAt());

        $this->scenario
            ->given([])
            ->when($productCreated)
            ->then([$product]);
    }

    /**
     * @return Projector
     */
    protected function createProjector(InMemoryRepository $repository)
    {
        return new ProductProjector($repository);
    }
}
