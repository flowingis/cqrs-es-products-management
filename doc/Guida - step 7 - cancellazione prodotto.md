Step 7: cancellazione prodotto
==============================

Provare la funzionalità con :
	- curl -X POST http://api.cqrsws.dev/app_dev.php/products
	- curl -X DELETE http://api.cqrsws.dev/app_dev.php/products/{id ottenuto dalla post}


Aggiungere i file:
	- \Tests\Shop\Command\DeleteProductTest
	- \Shop\Product\Event\ProductDeleted
	- \Shop\Product\Command\DeleteProduct

```
<?php
namespace Tests\Shop\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventHandling\EventBus;
use Broadway\EventStore\EventStore;
use Shop\Product\Command\DeleteProduct;
use Shop\Product\Event\ProductCreated;
use Shop\Product\Event\ProductDeleted;
use Shop\Product\ProductCommandHandler;
use Shop\Product\Repository;
use Shop\Product\ValueObject\ProductId;

class DeleteProductTest extends CommandHandlerScenarioTestCase
{
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

        $deleteProduct = new DeleteProduct(
            $productId,
            new \DateTimeImmutable('2017-03-14')
        );

        $this->scenario
            ->withAggregateId($productId)
            ->given([$productCreated])
            ->when($deleteProduct)
            ->then(
                [
                    new ProductDeleted(
                        $productId,
                        new \DateTimeImmutable('2017-03-14')
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

namespace Shop\Product\Event;

use Broadway\Serializer\Serializable;
use Shop\Product\ValueObject\ProductId;

class ProductDeleted implements Serializable
{
    /**
     * @var ProductId
     */
    private $productId;
    /**
     * @var \DateTimeImmutable
     */
    private $deletedAt;

    public function __construct(ProductId $productId, \DateTimeImmutable $deletedAt)
    {
        $this->productId = $productId;
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return ProductId
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDeletedAt(): \DateTimeImmutable
    {
        return $this->deletedAt;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            new ProductId($data['productId']),
            new \DateTimeImmutable($data['deletedAt'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'productId' => (string)$this->productId,
            'deletedAt' => $this->deletedAt->format('Y-m-d\TH:i:s.uP')
        ];
    }
}
```

```
<?php

namespace Shop\Product\Command;

use Shop\Product\ValueObject\ProductId;

class DeleteProduct
{
    /**
     * @var ProductId
     */
    private $productId;
    /**
     * @var \DateTimeImmutable
     */
    private $deletedAt;

    public function __construct(ProductId $productId, \DateTimeImmutable $deletedAt)
    {
        $this->productId = $productId;
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return ProductId
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDeletedAt(): \DateTimeImmutable
    {
        return $this->deletedAt;
    }
}

```

In Product aggregato aggiungere:

```
    public function delete(DeleteProduct $command)
    {
        $this->apply(new ProductDeleted(
            $command->getProductId(),
            $command->getDeletedAt()
            )
        );
    }
```

In ProductCommandHandler aggiungere:

```
    public function handleDeleteProduct(DeleteProduct $command)
    {
        /** @var Product $product */
        $product = $this->repository->load($command->getProductId());

        $product->delete($command);

        $this->repository->save($product);
    }
```


In ProductProjectorTest aggiungere:

```
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
```

In ProductProjector aggiungere:

```
    protected function applyProductDeleted(ProductDeleted $event)
    {
        /** @var Product $product */
        $product = $this->repository->find($event->getProductId());

        $this->repository->remove((string)$product->getId());
    }
```

In ProductRepository aggiungere:

```
    /**
     * @param string $id
     */
    public function remove($id)
    {
        $this->connection->delete('product', ['productId' => $id]);
    }
``

In DefaultController aggiungere:

```
    /**
     * @Route("products/{id}", name="delete_product")
     * @Method({"DELETE"})
     */
    public function deleteAction(Request $request)
    {
        $productId = new ProductId($request->get('id'));

        $deleteProduct = new DeleteProduct(
            $productId,
            new \DateTimeImmutable()
        );

        $this->get('broadway.command_handling.simple_command_bus')->dispatch($deleteProduct);

        return new JsonResponse(['product_id' => (string)$productId], 200);
    }
``
