<?php
declare(strict_types=1);

namespace Common\Defaults;

use Phalcon\Config;
use Phalcon\Di;
use Phalcon\Mvc\Micro;
use Dice\Dice;

abstract class BaseClass
{
    private Di $di;
    private Micro $app;
    private Config $config;
    private Dice $call;

    public function __construct()
    {
        $this->app = $GLOBALS['app'];
        $this->di = $GLOBALS['di'];
        $this->config = $GLOBALS['config'];
        $this->call = new Dice();
    }
}