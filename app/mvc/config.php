<?php
declare(strict_types=1);

use Phalcon\Config;
use Common\Service\CustomRouter;
use Dotenv\Dotenv;

if (file_exists('/app/.env')) {
    (Dotenv::createImmutable('/app/'))->load();
} else {
    (Dotenv::createImmutable('/app/mvc/', '.env.development'))->load();
}

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
        'test' => 'Common:Test',
        'migration' => 'Common:Migration',
    ],

    /*
     * List of files (mostly vendor/*) that should be automatically deleted.
     * To manually remove files run `cli Common:RemoveUnusedFiles`.
     * Removal command is being automatically ran every minute.
     */
    'unusedFiles' => [
        '/app/vendor/bin/phinx',    // use command `cli migration` instead of `phinx`
        '/app/vendor/bin/carbon',   // I do not use this one. Remove this line if you need Carbon-Cli
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
