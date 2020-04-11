<?php
declare(strict_types=1);

namespace Common\Task;

use Common\BaseClasses\BaseTask;
use Common\Service\InjectionService;
use Throwable;

class CheckDbStateTask extends BaseTask
{
    private InjectionService $injectionService;

    public function __construct(InjectionService $injectionService)
    {
        $this->injectionService = $injectionService;
    }

    public function mainAction(array $params = []): void
    {
        try {
            $this->injectionService->getDb()->connect();
            echo 1;
        } catch (Throwable $throwable) {
            echo 0;
        }
    }
}
