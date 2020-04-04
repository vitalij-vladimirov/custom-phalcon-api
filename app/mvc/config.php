<?php
declare(strict_types=1);

use Phalcon\Config;
use Common\Service\CustomRouter;

return new Config([

    // To use default Router comment or delete this line.
    'customRouter'  => CustomRouter::class,

    'database' => [
        'adapter'    => getenv('DB_CONNECTION'),
        'host'       => getenv('DB_HOST'),
        'post'       => getenv('DB_PORT'),
        'username'   => getenv('DB_USERNAME'),
        'password'   => getenv('DB_PASSWORD'),
        'dbname'     => getenv('DB_DATABASE'),
        'charset'    => getenv('DB_CHARSET'),
    ],

    'application' => [
        'baseUri'           => '/',
        'mvcDir'            => '/app/mvc',
        'modulesDir'        => '/app/modules',
        'migrationsDir'     => '/app/db/migrations',
        'seedsDir'          => '/app/db/seeds',
        'viewsDir'          => '/app/views',
        'cacheDir'          => '/var/cache/phalcon',
        'namespacesCache'   => '/var/cache/phalcon/namespaces.json',
    ],

    'defaultNamespaces' => [
        'Mvc' => '/app/mvc',
        'Common' => '/app/modules/Common',
        'Common\Task' => '/app/modules/Common/Task',
        'Common\Service' => '/app/modules/Common/Service',
    ],
]);
