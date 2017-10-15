<?php

namespace Shop\Order\Event;


use Broadway\Serializer\Serializable;
use Shop\Order\ValueObject\OrderId;

class OrderConfirmed implements Serializable
{
    private $orderId;
    private $confirmedAt;
    /**
     * @var
     */
    private $totalCost;

    public function __construct(OrderId $orderId, \DateTimeImmutable $confirmedAt)
    {
        $this->orderId = $orderId;
        $this->confirmedAt = $confirmedAt;
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getConfirmedAt(): \DateTimeImmutable
    {
        return $this->confirmedAt;
    }

    /**
     * @return mixed
     */
    public function getTotalCost()
    {
        return $this->totalCost;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            new OrderId($data['orderId']),
            new \DateTimeImmutable($data['confirmedAt'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'orderId'   => (string)$this->orderId,
            'confirmedAt' => $this->confirmedAt->format('Y-m-d\TH:i:s.uP')
        ];
    }
}
