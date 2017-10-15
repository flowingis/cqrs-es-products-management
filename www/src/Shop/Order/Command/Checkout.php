<?php

namespace Shop\Order\Command;

use Shop\Order\ValueObject\OrderId;

class Checkout
{
    /**
     * @var OrderId
     */
    private $orderId;
    private $totalCost;
    /**
     * @var \DateTimeImmutable
     */
    private $requestedAt;

    public function __construct(OrderId $orderId, $totalCost, \DateTimeImmutable $requestedAt)
    {
        $this->orderId = $orderId;
        $this->totalCost = $totalCost;
        $this->requestedAt = $requestedAt;
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
     * @return \DateTimeImmutable
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }
}
