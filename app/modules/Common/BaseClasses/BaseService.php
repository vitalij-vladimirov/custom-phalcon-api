<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Config;

abstract class BaseService
{
    protected Config $config;

    public function __construct()
    {
        $this->config = $GLOBALS['app']->di->getShared('config');
    }
}
