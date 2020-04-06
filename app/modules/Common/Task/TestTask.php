<?php
declare(strict_types=1);

namespace Common\Task;

use Common\BaseClasses\BaseTask;

/**
 * Test task is used for testing development process.
 * In any case do not commit this file changes, use is only for local testing.
 */
class TestTask extends BaseTask
{
    public function onConstruct(): void
    {
        parent::onConstruct();
    }

    public function mainAction(string $argument): void
    {
        // default action goes here
    }
}
