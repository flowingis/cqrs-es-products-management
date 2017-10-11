<?php

namespace Shop\Product\Command;

use Shop\Product\ValueObject\ProductId;

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

    public function __construct(ProductId $productId, $barcode, $name, $imageUrl, $brand, \DateTimeImmutable $createdAt)
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
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
