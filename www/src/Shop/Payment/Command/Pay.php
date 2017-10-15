<?php

namespace Shop\Payment\Command;


use Shop\Order\ValueObject\OrderId;
use Shop\Payment\ValueObject\PaymentId;

class Pay
{
    /**
     * @var PaymentId
     */
    private $paymentId;
    /**
     * @var OrderId
     */
    private $orderId;
    private $totalCost;
    /**
     * @var \DateTimeImmutable
     */
    private $requestedAt;

    public function __construct(PaymentId $paymentId, OrderId $orderId, $totalCost, \DateTimeImmutable $requestedAt)
    {
        $this->paymentId = $paymentId;
        $this->orderId = $orderId;
        $this->totalCost = $totalCost;
        $this->requestedAt = $requestedAt;
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
