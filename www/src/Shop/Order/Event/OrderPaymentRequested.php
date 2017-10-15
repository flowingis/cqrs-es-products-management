<?php

namespace Shop\Order\Event;


use Broadway\Serializer\Serializable;
use Shop\Order\ValueObject\OrderId;

class OrderPaymentRequested implements Serializable
{
    private $orderId;
    private $requestedAt;
    /**
     * @var
     */
    private $totalCost;

    public function __construct(OrderId $orderId, $totalCost, \DateTimeImmutable $requestedAt)
    {
        $this->orderId = $orderId;
        $this->requestedAt = $requestedAt;
        $this->totalCost = $totalCost;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
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
            $data['totalCost'],
            new \DateTimeImmutable($data['requestedAt'])
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
            'requestedAt' => $this->requestedAt->format('Y-m-d\TH:i:s.uP')
        ];
    }
}
