<?php

namespace Shop\Order\Process;

use Broadway\Processor\Processor;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Shop\Order\Command\ConfirmOrder;
use Shop\Order\Event\OrderPaymentRequested;
use Shop\Payment\Command\Pay;
use Shop\Payment\Event\PaymentDone;
use Shop\Payment\ValueObject\PaymentId;

class CheckoutProcess extends Processor
{
    /**
     * @var \Broadway\CommandHandling\CommandBus
     */
    private $commandBus;
    /**
     * @var UuidGeneratorInterface
     */
    private $uuidGenerator;

    public function __construct(\Broadway\CommandHandling\CommandBus $commandBus, UuidGeneratorInterface $uuidGenerator)
    {
        $this->commandBus = $commandBus;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function handleOrderPaymentRequested(OrderPaymentRequested $event)
    {
        $this->commandBus->dispatch(new Pay(
            new PaymentId($this->uuidGenerator->generate()),
            $event->getOrderId(),
            $event->getTotalCost(),
            $event->getRequestedAt()
        ));
    }

    public function handlePaymentDone(PaymentDone $event)
    {
        $this->commandBus->dispatch(new ConfirmOrder(
            $event->getOrderId(),
            $event->getPaymentId(),
            $event->getDoneAt()
        ));
    }
}
