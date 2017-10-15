<?php

namespace Shop\Payment\Event;


use Broadway\Serializer\Serializable;
use Shop\Order\ValueObject\OrderId;
use Shop\Payment\ValueObject\PaymentId;

class PaymentDone implements Serializable
{
    /**
     * @var PaymentId
     */
    private $paymentId;

    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var \DateTimeImmutable
     */
    private $doneAt;

    public function __construct(PaymentId $paymentId, OrderId $orderId, \DateTimeImmutable $doneAt)
    {
        $this->paymentId = $paymentId;
        $this->orderId = $orderId;
        $this->doneAt = $doneAt;
    }

    /**
     * @return PaymentId
     */
    public function getPaymentId()
    {
        return $this->paymentId;
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
    public function getDoneAt()
    {
        return $this->doneAt;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self(
            new PaymentId($data['paymentId']),
            new OrderId($data['orderId']),
            new \DateTimeImmutable($data['doneAt'])
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'paymentId' => (string)$this->paymentId,
            'orderId' => (string)$this->orderId,
            'doneAt' => $this->doneAt->format('Y-m-d\TH:i:s.uP')
        ];
    }
}
