<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Dice\Dice;
use Phalcon\Cli\Task;

abstract class BaseTask extends Task
{
    protected Dice $di;

    public function onConstruct(): void
    {
        $this->di = new Dice();
    }
}
