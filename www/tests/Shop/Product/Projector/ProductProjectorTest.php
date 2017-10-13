<?php

namespace Tests\Shop\Product\ValueObject;

use Broadway\ReadModel\InMemory\InMemoryRepository;
use Broadway\ReadModel\Projector;
use Broadway\ReadModel\Testing\ProjectorScenarioTestCase;
use Shop\Product\Event\ProductCreated;
use Shop\Product\Event\ProductDeleted;
use Shop\Product\Event\ProductUpdated;
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

        $productUpdated = new ProductUpdated(
            $productId,
            '5707055029609',
            'Nome prodotto: Scaaarpe più belle',
            'http://static.politifact.com.s3.amazonaws.com/subjects/mugs/fake1.png',
            'Brand prodotto: Super Scaaaarpe più belle',
            new \DateTimeImmutable('2017-03-14')
        );

        $product = new Product();

        $product->setProductId($productCreated->getProductId());
        $product->setBarcode('5707055029609');
        $product->setName('Nome prodotto: Scaaarpe più belle');
        $product->setImageUrl('http://static.politifact.com.s3.amazonaws.com/subjects/mugs/fake1.png');
        $product->setBrand('Brand prodotto: Super Scaaaarpe più belle');
        $product->setCreatedAt(new \DateTimeImmutable('2017-02-14'));
        $product->setUpdatedAt(new \DateTimeImmutable('2017-03-14'));

        $this->scenario
            ->given([$productCreated])
            ->when($productUpdated)
            ->then([$product]);
    }

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

        $productDeleted = new ProductDeleted(
            $productId,
            new \DateTimeImmutable('2017-03-14')
        );

        $this->scenario
            ->given([$productCreated])
            ->when($productDeleted)
            ->then([]);
    }

    /**
     * @return Projector
     */
    protected function createProjector(InMemoryRepository $repository)
    {
        return new ProductProjector($repository);
    }
}
