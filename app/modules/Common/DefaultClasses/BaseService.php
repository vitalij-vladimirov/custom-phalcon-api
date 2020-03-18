<?php
declare(strict_types=1);

namespace Common\DefaultClasses;

use Dice\Dice;
use Phalcon\Config;

abstract class BaseService
{
    private Config $config;
    private Dice $di;

    public function __construct()
    {
        $this->config = $GLOBALS['config'];
        $this->di = new Dice();
    }
}
