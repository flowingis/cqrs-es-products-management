<?php

namespace Tests\Shop\Order\Process;


use Broadway\CommandHandling\Testing\TraceableCommandBus;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use Shop\Order\Command\ConfirmOrder;
use Shop\Order\Event\OrderPaymentRequested;
use Shop\Order\Process\CheckoutProcess;
use Shop\Order\ValueObject\OrderId;
use Shop\Payment\Command\Pay;
use Shop\Payment\Event\PaymentDone;
use Shop\Payment\ValueObject\PaymentId;
use Tests\Utils\Processor\ProcessorScenarioTestCase;

class CheckoutProcessTest extends ProcessorScenarioTestCase
{
    /**
     * @test
     */
    public function should_pay_on_order_payment_request()
    {
        $totalCost = 100;
        $this->scenario
            ->when(
                new OrderPaymentRequested(
                    new OrderId('00000000-0000-0000-0000-000000000321'),
                    $totalCost,
                    new \DateTimeImmutable('2017-06-14')
                )
            )
            ->then([
                new Pay(
                    new PaymentId('00000000-0000-0000-0000-000000000421'),
                    new OrderId('00000000-0000-0000-0000-000000000321'),
                    $totalCost,
                    new \DateTimeImmutable('2017-06-14')
                ),
            ]);
    }

    /**
     * @test
     */
    public function should_confirm_order_on_order_payment_done()
    {
        $this->scenario
            ->when(
                new PaymentDone(
                    new PaymentId('00000000-0000-0000-0000-000000000421'),
                    new OrderId('00000000-0000-0000-0000-000000000321'),
                    new \DateTimeImmutable('2017-06-14')
                )
            )
            ->then([
                new ConfirmOrder(
                    new OrderId('00000000-0000-0000-0000-000000000321'),
                    new PaymentId('00000000-0000-0000-0000-000000000421'),
                    new \DateTimeImmutable('2017-06-14')
                ),
            ]);
    }

    /**
     * @param TraceableCommandBus $traceableCommandBus
     *
     * @return mixed
     */
    protected function createProcessor(TraceableCommandBus $traceableCommandBus)
    {
        /** @var UuidGeneratorInterface $uuidGeneratorStub */
        $uuidGeneratorStub = $this->prophesize(\Broadway\UuidGenerator\UuidGeneratorInterface::class);

        $uuidGeneratorStub->generate()->willReturn('00000000-0000-0000-0000-000000000421');

        return new CheckoutProcess($traceableCommandBus, $uuidGeneratorStub->reveal());
    }
}
