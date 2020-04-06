<?php
declare(strict_types=1);

use Phalcon\Config;
use Common\Service\CustomRouter;
use Dotenv\Dotenv;

(Dotenv::createImmutable('/app/'))->load();

return new Config([

    /*
     * Specify cli commands shortcuts he to avoid Writing long command paths
     * Shortcut must specify full command path: Module:Task:command
     *
     * Example:
     *      'test' => 'Example:Test:run'
     *      Command:    cli test
     *      Will run:   cli Example:Test:run
     */
    'cliShortcuts' => [
        'migration' => 'Common:Migration:help',
        'migration:help' => 'Common:Migration:help',
        'migration:create' => 'Common:Migration:create',
        'migration:update' => 'Common:Migration:update',
        'migration:run' => 'Common:Migration:run',
    ],

    // To use default Router comment or delete this line.
    'customRouter'  => CustomRouter::class,

    'database' => [
        'adapter'    => getenv('DB_CONNECTION'),
        'host'       => getenv('DB_HOST'),
        'port'       => getenv('DB_PORT'),
        'username'   => getenv('DB_USERNAME'),
        'password'   => getenv('DB_PASSWORD'),
        'dbname'     => getenv('DB_DATABASE'),
        'charset'    => getenv('DB_CHARSET'),
        'collation'  => getenv('DB_COLLATION'),
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
