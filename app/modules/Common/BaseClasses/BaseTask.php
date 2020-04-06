<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Dice\Dice;
use Phalcon\Cli\Task;
use Phalcon\Config;

abstract class BaseTask extends Task
{
    protected Dice $di;
    protected Config $config;

    public function onConstruct(): void
    {
        $this->di = new Dice();
        $this->config = $GLOBALS['config'];
    }
}
