<?php

namespace Shop\Product\ReadModel;

use Broadway\ReadModel\Identifiable;
use Broadway\ReadModel\Repository;
use Doctrine\DBAL\Connection;

class ProductRepository implements Repository
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(Identifiable $product)
    {
        $serialized = $product->serialize();
        $this->connection->insert('product', $serialized);
    }

    /**
     * @param string $id
     *
     * @return Identifiable|null
     */
    public function find($id)
    {
        $productData = $this->connection->fetchAssoc(
            'select * from product where productId = :id',
            ["id" => $id]
        );

        $product = Product::deserialize($productData);

        return $product;
    }

    /**
     * @param array $fields
     *
     * @return Identifiable[]
     */
    public function findBy(array $fields)
    {
        // TODO: Implement findBy() method.
    }

    /**
     * @return Identifiable[]
     */
    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    /**
     * @param string $id
     */
    public function remove($id)
    {
        // TODO: Implement remove() method.
    }
}
