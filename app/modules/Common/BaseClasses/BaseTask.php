<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Cli\Task;
use Phalcon\Config;
use Dice\Dice;

abstract class BaseTask extends Task
{
    protected Config $config;
    protected Dice $inject;

    public function onConstruct(): void
    {
        $this->config = $this->di->getShared('config');
        $this->inject = new Dice();
    }
}
