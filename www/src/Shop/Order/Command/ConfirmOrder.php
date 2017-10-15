<?php

namespace Shop\Order\Command;

use Shop\Order\ValueObject\OrderId;
use Shop\Payment\ValueObject\PaymentId;

class ConfirmOrder
{
    /**
     * @var OrderId
     */
    private $orderId;
    private $paymentId;
    /**
     * @var \DateTimeImmutable
     */
    private $confirmedAt;

    public function __construct(OrderId $orderId, PaymentId $paymentId, \DateTimeImmutable $confirmedAt)
    {
        $this->orderId = $orderId;
        $this->paymentId = $paymentId;
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
     * @return PaymentId
     */
    public function getPaymentId(): PaymentId
    {
        return $this->paymentId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getConfirmedAt(): \DateTimeImmutable
    {
        return $this->confirmedAt;
    }
}
