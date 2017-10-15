<?php

namespace Shop\Order\Event;


use Broadway\Serializer\Serializable;
use Shop\Order\ValueObject\OrderId;

class OrderCreated implements Serializable
{
    private $orderId;
    private $createdAt;
    /**
     * @var
     */
    private $totalCost;
    /**
     * @var array
     */
    private $items;

    public function __construct(OrderId $orderId, $totalCost, array $items, \DateTimeImmutable $createdAt)
    {
        $this->orderId = $orderId;
        $this->createdAt = $createdAt;
        $this->totalCost = $totalCost;
        $this->items = $items;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            new OrderId($data['orderId']),
            $data['totalCost'],
            $data['items'],
            new \DateTimeImmutable($data['createdAt'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'orderId'   => (string)$this->orderId,
            'totalCost' => $this->totalCost,
            'items' => $this->items,
            'createdAt' => $this->createdAt->format('Y-m-d\TH:i:s.uP')
        ];
    }
}
