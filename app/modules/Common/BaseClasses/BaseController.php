<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Dice\Dice;
use Phalcon\Config;

abstract class BaseController
{
    protected Config $config;
    protected Dice $di;

    public function __construct()
    {
        $this->config = $GLOBALS['config'];
        $this->di = new Dice();
    }
}
