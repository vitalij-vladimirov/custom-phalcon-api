<?php
declare(strict_types=1);

use Phalcon\Config;

/** @var Config $config */
$config = $GLOBALS['app']->di->getShared('config');

return [
    'paths' => [
        'migrations' => '/app/db/migrations'
    ],
    'migration_base_class' => '\Common\BaseClasses\BaseMigration',
    'environments' => [
        'default_migration_table' => 'migration',
        'default_database' => 'default',
        'default' => [
            'adapter' => $config->database->adapter,
            'host' => $config->database->host,
            'name' => $config->database->dbname,
            'user' => $config->database->username,
            'pass' => $config->database->password,
            'port' => $config->database->port,
        ]
    ]
];
