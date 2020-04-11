<?php
declare(strict_types=1);

namespace Common\Task;

use Common\BaseClasses\BaseTask;
use Throwable;

class CheckDbStateTask extends BaseTask
{
    public function mainAction(): void
    {
        try {
            $this->db->connect();
            echo 1;
        } catch (Throwable $throwable) {
            echo 0;
        }
    }
}
