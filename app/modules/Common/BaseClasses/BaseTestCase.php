<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Config;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected Config $config;

    public function __construct()
    {
        parent::__construct();

        $this->config = $GLOBALS['app']->di->getShared('config');
    }
}
