<?php
declare(strict_types=1);

namespace BaseMvc;

use Common\Service\CacheManager;
use Phalcon\Config;
use Phalcon\Loader;
use Common\Json;

/**
 * Local variables
 * @var Config $config
 */

// phpcs:disable
$loader = new Loader();

$loader->registerDirs([
    $config->application->modulesDir,
    $config->application->mvcDir,
])->register();

$loader->registerNamespaces(getNamespaces());
// phpcs:enable

function getNamespaces(): array
{
    $namespacesCache = file_exists(CacheManager::NAMESPACES_CACHE_FILE) ?
        (file_get_contents(CacheManager::NAMESPACES_CACHE_FILE) ?? null) :
        null
    ;

    if (!$namespacesCache) {
        // List of namespaces necessary to run namespaces cache manager
        return [
            'Common' => '/app/modules/Common',
            'Common\Task' => '/app/modules/Common/Task',
            'Common\Service' => '/app/modules/Common/Service',
        ];
    }

    return Json::decode($namespacesCache);
}
