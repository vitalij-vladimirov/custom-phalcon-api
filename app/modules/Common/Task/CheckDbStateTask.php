<?php
declare(strict_types=1);

namespace Common\Task;

use Common\BaseClass\BaseTask;
use Throwable;

class CheckDbStateTask extends BaseTask
{
    public function mainAction(array $params = []): void
    {
        try {
            $this->di->get('db')->connect();
            echo 1;
        } catch (Throwable $throwable) {
            echo 0;
        }
    }
}
