<?php
declare(strict_types=1);

namespace Common\Task;

use Phalcon\Cli\Task;

class DefaultTask extends Task
{
    public function mainAction(): void
    {
        echo PHP_EOL;
        echo 'CLI call structure: `cli Module:TaskName:ActionName ...parameters`' . PHP_EOL;
        echo '- Module folder name.' . PHP_EOL;
        echo '- TaskName should be without `Task` in the end.' . PHP_EOL;
        echo '- ActionName is optional, should be without `Action` in the end. Default action is `main`.' . PHP_EOL;
        echo '- ...parameters are optional, should be separated with spaces.' . PHP_EOL;
        echo PHP_EOL;
        echo 'Examples:' . PHP_EOL;
        echo '- `cli Test:CacheBuilder:cacheNamespaces param1 param2`' . PHP_EOL;
        echo '- `cli Test:CacheBuilder:cacheNamespaces`' . PHP_EOL;
        echo '- `cli Test:CacheBuilder`' . PHP_EOL;
        echo PHP_EOL;
    }

    public function notFoundAction(string $task, string $action = null): void
    {
        if ($action === null) {
            echo 'Task `' . $task . '` not found.' . PHP_EOL;
            return;
        }

        echo 'Action `' . $action . '` not found.' . PHP_EOL;
    }
}