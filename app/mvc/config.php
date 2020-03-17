<?php

use Phalcon\Config;

return new Config([
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
        'mvcDir'        => '/app/mvc/',
        'modulesDir'    => '/app/modules/',
        'migrationsDir' => '/app/db/migrations/',
        'seedsDir'      => '/app/db/seeds/',
        'viewsDir'      => '/app/views/',
        'cacheDir'      => '/var/cache/api',
        'baseUri'       => '/',
    ],
]);
