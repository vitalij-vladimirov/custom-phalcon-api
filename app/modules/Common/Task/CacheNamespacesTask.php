<?php
declare(strict_types=1);

namespace Common\Task;

use Phalcon\Cli\Task;
use Common\Service\CacheManager;
use Common\Console;

class CacheNamespacesTask extends Task
{
    public function mainAction(string $type = null): void
    {
        /**
         * Run namespaces caching once
         */
        if ($type === null) {
            (new CacheManager())->cacheNamespaces();

            return;
        }

        /**
         * Run namespaces caching every 15 seconds
         */
        if ($type === 'cron') {
            for ($i = 1; $i <= 4; ++$i) {
                (new CacheManager())->cacheNamespaces();

                sleep(15);
            }

            return;
        }

        echo Console::error('Error: Argument {type} is incorrect');

        return;
    }
}
