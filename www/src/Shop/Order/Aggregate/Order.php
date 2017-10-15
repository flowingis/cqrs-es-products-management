<?php

namespace Shop\Order\Aggregate;

use Broadway\EventSourcing\EventSourcedAggregateRoot;
use Shop\Order\Command\Checkout;
use Shop\Order\Command\CreateOrder;
use Shop\Order\Event\OrderConfirmed;
use Shop\Order\Event\OrderCreated;
use Shop\Order\Event\OrderPaymentRequested;
use Shop\Order\Exception\CheckoutDenied;

class Order extends EventSourcedAggregateRoot
{
    private $id;

    private $totalCost;

    public static function create(CreateOrder $command)
    {
        $product = new self();
        $product->apply(
            new OrderCreated(
                $command->getOrderId(),
                $command->getTotalCost(),
                $command->getItems(),
                $command->getCreatedAt()
            )
        );

        return $product;
    }

    public function checkout(Checkout $command)
    {
        if($this->totalCost !== $command->getTotalCost()) {
            throw new CheckoutDenied();
        }

        $this->apply(new OrderPaymentRequested(
            $command->getOrderId(),
            $command->getTotalCost(),
            $command->getRequestedAt()
        ));
    }

    protected function applyOrderCreated(OrderCreated $event)
    {
        $this->id = $event->getOrderId();
        $this->totalCost = $event->getTotalCost();
    }

    /**
     * @return string
     */
    public function getAggregateRootId()
    {
        return $this->id;
    }
}
