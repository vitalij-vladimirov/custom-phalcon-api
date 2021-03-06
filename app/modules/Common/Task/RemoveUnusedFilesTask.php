<?php
declare(strict_types=1);

namespace Common\Task;

use Common\BaseClass\BaseTask;
use Common\File;
use Throwable;

class RemoveUnusedFilesTask extends BaseTask
{
    public function mainAction(array $params = []): void
    {
        /*
         * Currently files cleaning is ran directly from task,
         * but it should be moved to service in case of growing
         */
        foreach ($this->di->get('config')->unusedFiles as $file) {
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
