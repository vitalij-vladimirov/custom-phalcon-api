<?php
declare(strict_types=1);

namespace Common\BaseClass;

use Common\Service\Injectable;

abstract class BaseTask extends Injectable
{
    abstract public function mainAction(array $params = []): void;
}
