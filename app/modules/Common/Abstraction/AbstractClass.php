<?php
declare(strict_types=1);

namespace Common\Abstraction;

use Phalcon\Di;

abstract class AbstractClass
{
    private Di $di;

    public function __construct()
    {
        $this->di = new Di();
    }
}