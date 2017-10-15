<?php

namespace Shop\Order\ValueObject;


use Shop\Product\ValueObject\ProductId;

class OrderItem
{
    /**
     * @var ProductId
     */
    private $productId;
    private $quantity;

    public function __construct(ProductId $productId, $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    /**
     * @return ProductId
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
