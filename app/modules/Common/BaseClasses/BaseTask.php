<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Cli\Task;
use Phalcon\Config;

abstract class BaseTask extends Task
{
    protected Config $config;

    public function onConstruct(): void
    {
        $this->config = $this->di->getShared('config');
    }
}
