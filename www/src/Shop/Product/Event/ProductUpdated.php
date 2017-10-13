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
