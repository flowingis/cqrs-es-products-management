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
