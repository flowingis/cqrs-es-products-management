<?php

namespace Tests\Shop\Product\ValueObject;


use Shop\Product\ValueObject\ProductId;

class ProductIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function should_accept_valid_uuid()
    {
        $productId = new ProductId('00000000-0000-0000-0000-000000000321');
        $this->assertEquals('00000000-0000-0000-0000-000000000321', $productId);
    }

    /**
     * @test
     * @expectedException \Assert\InvalidArgumentException
     */
    public function should_not_accept_not_valid_uuid()
    {
        new ProductId('-0000-0000-0000-000000000321');
    }
}
