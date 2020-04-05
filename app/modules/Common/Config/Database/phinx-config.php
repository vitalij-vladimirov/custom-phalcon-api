<?php

return [
    'paths' => [
        'migrations' => '/app/db/migrations'
    ],
    'migration_base_class' => '\Common\Config\Database\Migration',
    'environments' => [
        'default_migration_table' => 'migration',
        'default_database' => 'dev',
        'dev' => [
//           TODO: get config from env or main config file
            'adapter' => 'mysql',
            'host' => 'phalcon-api-db',
            'name' => 'phalcon_api',
            'user' => 'api',
            'pass' => 'api',
            'port' => '3306',
//            'adapter' => getenv('DB_CONNECTION'),
//            'host' => getenv('DB_HOST'),
//            'name' => getenv('DB_DATABASE'),
//            'user' => getenv('DB_USERNAME'),
//            'pass' => getenv('DB_PASSWORD'),
//            'port' => getenv('DB_PORT'),
        ]
    ]
];