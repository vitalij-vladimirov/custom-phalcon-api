<?php
declare(strict_types=1);

namespace Common\Task;

use Common\BaseClasses\BaseTask;
use Common\File;
use Common\Service\InjectionService;
use Throwable;

class RemoveUnusedFilesTask extends BaseTask
{
    private InjectionService $injectionService;

    public function __construct(InjectionService $injectionService)
    {
        $this->injectionService = $injectionService;
    }

    public function mainAction(array $params = []): void
    {
        /*
         * Currently files cleaning is ran directly from task,
         * but it should be moved to service in case of growing
         */
        foreach ($this->injectionService->getConfig()->unusedFiles as $file) {
            if (File::exists($file)) {
                try {
                    File::delete($file);
                } catch (Throwable $exception) {
                    // not a big deal :)
                }
            }
        }
    }
}
