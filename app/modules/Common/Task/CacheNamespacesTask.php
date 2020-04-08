<?php
declare(strict_types=1);

namespace Common\Task;

use Common\Exception\InternalErrorException;
use Phalcon\Cli\Task;
use Common\Service\CacheManager;

class CacheNamespacesTask extends Task
{
    public function mainAction(string $type = null): void
    {
        if ($type !== null && $type !== 'cron') {
            throw new InternalErrorException('Argument $type must be empty or \'cron\'.');
        }

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
        }
    }
}
