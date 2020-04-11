<?php
declare(strict_types=1);

namespace Common\BaseClasses;

abstract class BaseTask
{
    abstract public function mainAction(array $params = []): void;
}
