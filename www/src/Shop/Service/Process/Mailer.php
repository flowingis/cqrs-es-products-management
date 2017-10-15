<?php

namespace Shop\Service\Process;

use Broadway\Processor\Processor;
use Shop\Order\Event\OrderConfirmed;

/**
 * Class Mailer
 *
 * @package Soisy\Domain\Common\Service
 */
class Mailer extends Processor
{
    private $mailer;

    public function __construct(\Shop\Service\Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handleOrderConfirmed(OrderConfirmed $event)
    {
        $message = 'Order ' . (string)$event->getOrderId() . ' confirmed.';

        $this->mailer->send('admin@cqrs-es-ws.dev', 'rf@ideato.it', $message);
    }
}
