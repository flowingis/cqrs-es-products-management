Step 2: refactoirng per gestire gli id con value object
==========================

La prima implementazione del test la si potrebbe fare con lo uuid come string
che rifattorizzo con la classe VO:
    - ProductId
    - ProductIdTest


```
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

```

```
<?php

namespace Shop\Product\ValueObject;

use Assert\Assertion;

class ProductId
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

```