<?php

namespace Shop\Order;


use Broadway\CommandHandling\SimpleCommandHandler;
use Shop\Order\Aggregate\Order;
use Shop\Order\Command\Checkout;
use Shop\Order\Command\CreateOrder;

class OrderCommandHandler extends SimpleCommandHandler
{
    /**
     * @var \Broadway\Repository\Repository
     */
    private $repository;

    public function __construct(\Broadway\Repository\Repository $repository)
    {
        $this->repository = $repository;
    }

    public function handleCreateOrder(CreateOrder $command)
    {
        $product = Order::create($command);

        $this->repository->save($product);
    }

    public function handleCheckout(Checkout $command)
    {
        /** @var Order $order */
        $order = $this->repository->load($command->getOrderId());

        $order->checkout($command);

        $this->repository->save($order);
    }
}
