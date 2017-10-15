<?php

namespace Shop\Order\Command;


use Shop\Order\ValueObject\OrderId;

class CreateOrder
{
    /**
     * @var OrderId
     */
    private $orderId;
    private $totalCost;
    /**
     * @var array
     */
    private $items;
    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    public function __construct(OrderId $orderId, $totalCost, array $items, \DateTimeImmutable $createdAt)
    {
        $this->orderId = $orderId;
        $this->totalCost = $totalCost;
        $this->items = $items;
        $this->createdAt = $createdAt;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return mixed
     */
    public function getTotalCost()
    {
        return $this->totalCost;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
