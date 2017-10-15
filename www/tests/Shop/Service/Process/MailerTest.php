<?php

namespace Tests\Shop\Service\Process;


use Shop\Order\Event\OrderConfirmed;
use Shop\Order\ValueObject\OrderId;
use Shop\Service\Mailer;

class MailerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function should_send_email_on_order_confirmation()
    {
        $orderConfirmed = new OrderConfirmed(
            new OrderId('00000000-0000-0000-0000-000000000321'),
            new \DateTimeImmutable('2017-02-14')
        );

        /** @var Mailer $mailer */
        $mailer = $this->prophesize(Mailer::class);

        $mailer->send(
            'admin@cqrs-es-ws.dev',
            'rf@ideato.it', 'Order ' . (string)$orderConfirmed->getOrderId() . ' confirmed.'
        )->shouldBeCalled();

        $mailerProcessor = new \Shop\Service\Process\Mailer($mailer->reveal());

        $mailerProcessor->handleOrderConfirmed($orderConfirmed);
    }
}
