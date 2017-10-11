Step 1: creazione prodotto write side
==========================

Verranno aggiunte le classi:
	- \Tests\Shop\Command\CreateProductTest
	- \Shop\Product\Command\CreateProduct
	- \Shop\Product\Event\ProductCreated
	- \Shop\Product\ProductCommandHandler
	- \Shop\Product\Aggregate\Product (aggregate)

Alla fine del giro mostrare anche come idratare l'aggregato con l'applyProductCreated.

```
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
```

```
<?php

namespace Shop\Product\Command;

class CreateProduct
{
    private $productId;
    private $barcode;
    private $name;
    private $imageUrl;
    private $brand;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    public function __construct($productId, $barcode, $name, $imageUrl, $brand, \DateTimeImmutable $createdAt)
    {
        $this->productId = $productId;
        $this->barcode = $barcode;
        $this->name = $name;
        $this->imageUrl = $imageUrl;
        $this->brand = $brand;
        $this->createdAt = $createdAt;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param string $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param mixed $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
```

```
<?php

namespace Shop\Product\Event;

use Broadway\Serializer\Serializable;

class ProductCreated implements Serializable
{
    private $productId;
    private $barcode;
    /**
     * @var string
     */
    private $name;
    private $imageUrl;
    private $brand;

    private $createdAt;

    /**
     * ProductCreated constructor.
     *
     * @param                    $productId
     * @param string             $barcode
     * @param string             $name
     * @param string             $imageUrl
     * @param string             $brand
     * @param \DateTimeImmutable $createdAt
     */
    public function __construct(
        $productId,
        string $barcode,
        string $name,
        string $imageUrl,
        string $brand,
        \DateTimeImmutable $createdAt
    ) {
        $this->productId = $productId;
        $this->barcode = $barcode;
        $this->name = $name;
        $this->imageUrl = $imageUrl;
        $this->brand = $brand;
        $this->createdAt = $createdAt;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param mixed $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param mixed $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
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
     * @param array $data
     *
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            $data['productId'],
            $data['barcode'],
            $data['name'],
            $data['imageUrl'],
            $data['brand'],
            new \DateTimeImmutable($data['createdAt'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'productId' => $this->productId,
            'name'      => $this->name,
            'barcode'   => $this->barcode,
            'imageUrl'  => $this->imageUrl,
            'brand'     => $this->brand,
            'createdAt' => $this->createdAt->format('Y-m-d\TH:i:s.uP')
        ];
    }
}
```

```
<?php

namespace Shop\Product;


use Broadway\CommandHandling\SimpleCommandHandler;
use Shop\Product\Aggregate\Product;
use Shop\Product\Command\CreateProduct;

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
}

```

```
<?php

namespace Shop\Product\Aggregate;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Shop\Product\Command\CreateProduct;
use Shop\Product\Event\ProductCreated;

class Product extends EventSourcedAggregateRoot
{
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

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        // TODO: Implement getAggregateRootId() method.
    }
}
```



