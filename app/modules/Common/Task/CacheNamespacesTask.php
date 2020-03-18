<?php
declare(strict_types=1);

namespace Common\Task;

use Common\Service\CacheManager;
use Phalcon\Cli\Task;

class CacheNamespacesTask extends Task
{
    /**
     * Run namespace caching every 15 seconds
     */
    public function mainAction()
    {
        for ($i = 1; $i <= 4; ++$i) {
            (new CacheManager())->cacheNamespaces();

            sleep(15);
        }
    }
}
