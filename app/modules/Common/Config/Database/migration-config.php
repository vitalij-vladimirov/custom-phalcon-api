<?php
declare(strict_types=1);

use Common\BaseClasses\Injectable;
use Common\BaseClasses\BaseMigration;

$config = (new Injectable())->di->get('config');

return [
    'paths' => [
        'migrations' => $config->application->migrationsDir
    ],
    'migration_base_class' => BaseMigration::class,
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
