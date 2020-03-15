<?php

/**
 * Local variables
 * @var \Phalcon\Config $config
 */

use Common\{Cache, Json};

/**
 * Registering an autoloader
 */
$loader = new \Phalcon\Loader();

$loader->registerDirs([
    $config->application->modulesDir,
    $config->application->mvcDir,
])->register();

$loader->registerNamespaces(getNamespaces());

function getNamespaces(): array
{
    $namespacesCache = Cache::read('namespaces') ?? null;

    if (!$namespacesCache) {
        return [
            'Common\Helpers' => '/app/modules/Common/Helpers',
            'Common\Task' => '/app/modules/Common/Task',
        ];
    }

    return Json::decode($namespacesCache);
}