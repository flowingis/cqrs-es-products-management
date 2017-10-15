<?php

namespace Shop\Payment\ValueObject;


use Assert\Assertion;

class PaymentId
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
