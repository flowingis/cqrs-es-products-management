Step 6: modifca prodotto
==============================

Provare la funzionalità con :
	- curl -X POST http://api.cqrsws.dev/app_dev.php/products
	- curl -X PUT http://api.cqrsws.dev/app_dev.php/products/{id ottenuto dalla post}


Aggiungere i file:
	- \Tests\Shop\Command\UpdateProductTest
	- \Shop\Product\Event\ProductUpdated
	- \Shop\Product\Command\UpdateProduct

```
<?php
namespace Tests\Shop\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBus;
use Broadway\EventStore\EventStore;
use Shop\Product\Command\UpdateProduct;
use Shop\Product\Event\ProductCreated;
use Shop\Product\Event\ProductUpdated;
use Shop\Product\ProductCommandHandler;
use Shop\Product\Repository;
use Shop\Product\ValueObject\ProductId;

class UpdateProductTest extends CommandHandlerScenarioTestCase
{
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

        $updateProduct = new UpdateProduct(
            $productId,
            '5707055029609',
            'Nome prodotto: Scaaarpe più belle',
            'http://static.politifact.com.s3.amazonaws.com/subjects/mugs/fake11.png',
            'Brand prodotto: Super Scaaaarpe più belle',
            new \DateTimeImmutable('2017-03-14')
        );

        $this->scenario
            ->withAggregateId($productId)
            ->given([$productCreated])
            ->when($updateProduct)
            ->then(
                [
                    new ProductUpdated(
                        $updateProduct->getProductId(),
                        $updateProduct->getBarcode(),
                        $updateProduct->getName(),
                        $updateProduct->getImageurl(),
                        $updateProduct->getBrand(),
                        $updateProduct->getCreatedAt()
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

class UpdateProduct extends CreateProduct
{

}
```

Aggiungendo l'evento vanno anche messe le proprietà protected nella classe base

```
<?php

namespace Shop\Product\Event;

use Shop\Product\ValueObject\ProductId;

class ProductUpdated extends ProductCreated
{
    /**
     * @param array $data
     *
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            new ProductId($data['productId']),
            $data['barcode'],
            $data['name'],
            $data['imageUrl'],
            $data['brand'],
            new \DateTimeImmutable($data['updatedAt'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'productId' => (string)$this->productId,
            'name'      => $this->name,
            'barcode'   => $this->barcode,
            'imageUrl'  => $this->imageUrl,
            'brand'     => $this->brand,
            'updatedAt' => $this->createdAt->format('Y-m-d\TH:i:s.uP')
        ];
    }
}
``


In \AppBundle\Controller\DefaultController aggiungere:


```
/**
     * @Route("/products/{id}", name="update_product")
     * @Method({"PUT"})
     */
    public function updateAction(Request $request)
    {
        $productId = new ProductId($request->get('id'));
        $updateProduct = new UpdateProduct(
            $productId,
            '5707055029609',
            'Nome prodotto: Scaaarpe più belle',
            'http://static.politifact.com.s3.amazonaws.com/subjects/mugs/fake1.png',
            'Brand prodotto: Super Scaaaarpe più belle',
            new \DateTimeImmutable()
        );

        $this->get('broadway.command_handling.simple_command_bus')->dispatch($updateProduct);

        return new JsonResponse($this->generateUrl('get_product', ['id' => (string)$productId]), 204);
    }
```

In ProductCommandHandler aggiungere:

```
    public function handleUpdateProduct(UpdateProduct $command)
    {
        /** @var Product $product */
        $product = $this->repository->load($command->getProductId());

        $product->update($command);

        $this->repository->save($product);
    }
```

In Product aggregato aggiungere:

```
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
```

In ProductProjectorTest aggiungere:

```
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
``

In ProductProjector aggiungere:

```
    protected function applyProductUpdated(ProductUpdated $event)
    {
        /** @var Product $product */
        $product = $this->repository->find($event->getProductId());

        $product->setBarcode($event->getBarcode());
        $product->setName($event->getName());
        $product->setImageUrl($event->getImageUrl());
        $product->setBrand($event->getBrand());
        $product->setUpdatedAt($event->getCreatedAt());

        $this->repository->save($product);
    }
```

In Product readmodel aggiungere:

```
    /**
     * @var \DateTimeImmutable
     */
    private $updatedAt;

    /**
     * @param \DateTimeImmutable $updatedAt
     */
    public function setUpdatedAt(\DateTimeImmutable $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

/**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
       ....

        if (isset($data['updatedAt'])) {
            $product->setUpdatedAt(new \DateTimeImmutable($data['updatedAt']));
        }

        ...
    }


/**
     * @return array
     */
    public function serialize()
    {
        return [
          ...
            'updatedAt' => $this->updatedAt ? $this->updatedAt->format('Y-m-d\TH:i:s.uP') : null,
        ];
    }
```

In ProductRepository aggiungere al metodo save:

```
public function save(Identifiable $product)
    {
        $serialized = $product->serialize();
        if ($product->getUpdatedAt()) {
            $this->connection->update(
                'product',
                $serialized,
                ['productId' => (string)$product->getId()]
            );
            return;
        }

        $this->connection->insert('product', $serialized);
    }
```


