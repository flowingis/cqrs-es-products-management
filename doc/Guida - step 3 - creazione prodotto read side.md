Step 3: creazione prodotto read side
====================================

Verranno aggiunte le classi
    - \Shop\Product\ReadModel\Product
    - \Shop\Product\Projector\ProductProjector
    - \Tests\Shop\Product\ValueObject\ProductProjectorTest

```
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
```

```
<?php

namespace Shop\Product\Projector;

use Broadway\ReadModel\Projector;
use Broadway\ReadModel\Repository;
use Shop\Product\Event\ProductCreated;
use Shop\Product\ReadModel\Product;

class ProductProjector extends Projector
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    protected function applyProductCreated(ProductCreated $event)
    {
        $product = new Product();

        $product->setProductId($event->getProductId());
        $product->setBarcode($event->getBarcode());
        $product->setName($event->getName());
        $product->setImageUrl($event->getImageUrl());
        $product->setBrand($event->getBrand());
        $product->setCreatedAt($event->getCreatedAt());

        $this->repository->save($product);
    }
}

```

```
<?php

namespace Shop\Product\ReadModel;

use Broadway\ReadModel\Identifiable;
use Broadway\Serializer\Serializable;
use Shop\Product\ValueObject\ProductId;

/**
 * Class User
 *
 * @package Soisy\Domain\IdentityAccess\ReadModel
 */
class Product implements Identifiable, Serializable
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var string
     */
    private $barcode;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $imageUrl;

    /**
     * @var string
     */
    private $brand;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->productId;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @param ProductId $productId
     */
    public function setProductId(ProductId $productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable $createdAt
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getBarcode(): string
    {
        return $this->barcode;
    }

    /**
     * @param string $barcode
     */
    public function setBarcode(string $barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl(string $imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     */
    public function setBrand(string $brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $product = new Product();

        $product->setProductId(new ProductId($data['productId']));
        $product->setBarcode($data['barcode']);
        $product->setName($data['name']);
        $product->setImageUrl($data['imageUrl']);
        $product->setBrand($data['brand']);
        $product->setCreatedAt(new \DateTimeImmutable($data['createdAt']));

        return $product;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'productId' => (string)$this->productId,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'imageUrl' => $this->imageUrl,
            'brand' => $this->brand,
            'createdAt' => $this->createdAt->format('Y-m-d\TH:i:s.uP')
        ];
    }
}

```