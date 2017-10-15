<?php

namespace Tests\Shop\Order\ValueObject;

use Shop\Order\ValueObject\OrderId;

class OrderIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function should_accept_valid_uuid()
    {
        $orderId = new OrderId('00000000-0000-0000-0000-000000000321');
        $this->assertEquals('00000000-0000-0000-0000-000000000321', $orderId);
    }

    /**
     * @test
     * @expectedException \Assert\InvalidArgumentException
     */
    public function should_not_accept_not_valid_uuid()
    {
        new OrderId('-0000-0000-0000-000000000321');
    }
}
