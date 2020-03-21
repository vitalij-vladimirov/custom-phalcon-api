<?php
declare(strict_types=1);

namespace Common\Task;

use Common\Service\CacheManager;
use Phalcon\Cli\Task;

class CacheNamespacesTask extends Task
{
    /**
     * Run namespace caching every 15 seconds
     *
     * @param string|null $type
     */
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

        echo 'Argument $type is incorrect' . PHP_EOL;

        return;
    }
}
