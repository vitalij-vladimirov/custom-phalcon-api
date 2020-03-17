<?php
declare(strict_types=1);

namespace BaseMvc;

use Phalcon\Config;
use Phalcon\Loader;
use Common\Json;
use Common\Task\CacheNamespacesTask;

/**
 * Local variables
 * @var Config $config
 */

/**
 * Registering an autoloader
 */
$loader = new Loader();

$loader->registerDirs([
    $config->application->modulesDir,
    $config->application->mvcDir,
])->register();

$loader->registerNamespaces(getNamespaces());

function getNamespaces(): array
{
    $namespacesCache = file_exists(CacheNamespacesTask::NAMESPACES_CACHE_FILE) ?
        (file_get_contents(CacheNamespacesTask::NAMESPACES_CACHE_FILE) ?? null) :
        null
    ;

    if (!$namespacesCache) {
        return [
            'Common' => '/app/modules/Common',
            'Common\Task' => '/app/modules/Common/Task',
        ];
    }

    return Json::decode($namespacesCache);
}
