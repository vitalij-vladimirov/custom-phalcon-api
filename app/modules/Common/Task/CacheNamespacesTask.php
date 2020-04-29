<?php
declare(strict_types=1);

namespace Common\Task;

use Phalcon\Cli\Task;
use Common\Exception\LogicException;
use Common\Service\CacheManager;

class CacheNamespacesTask extends Task
{
    public function mainAction(array $params = []): void
    {
        $type = $params[0] ?? null;

        if ($type !== null && $type !== 'cron') {
            throw new LogicException('Argument $type must be empty or \'cron\'.');
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

                if ($i < 4) {
                    sleep(15);
                }
            }
        }
    }
}
