<?php

namespace Shop\Order\ValueObject;

use Assert\Assertion;

class OrderId
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * ProductId constructor.
     *
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        Assertion::uuid($uuid);
        $this->uuid = $uuid;
    }

    public function __toString()
    {
        return $this->uuid;
    }
}
