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
