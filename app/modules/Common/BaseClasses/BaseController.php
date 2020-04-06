<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Mvc\Micro;

abstract class BaseController
{
    protected Micro $app;

    public function __construct()
    {
        $this->app = $GLOBALS['app'];
    }
}
