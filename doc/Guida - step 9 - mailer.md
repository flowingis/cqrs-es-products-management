Step 9: mailer
==============


Aggiungere i file:
	- \Tests\Shop\Service\Process\MailerTest
    - \Shop\Service\Process\Mailer
    - \Shop\Service\Mailer
    

```
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
```

```
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
```

```
<?php

namespace Shop\Service;


interface Mailer
{
    public function send($from, $to, $message);
}
``