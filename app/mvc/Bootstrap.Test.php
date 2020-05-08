<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '1G');

use Mvc\Bootstrap;
use Common\Service\Injectable;
use Common\Service\MigrationManager;

include '/app/vendor/autoload.php';
include '/app/mvc/Bootstrap.php';

$customConfig = [
    'environment' => 'testing',
];

$bootstrap = new Bootstrap();
$app = $bootstrap->runApp($customConfig);
$db = $app->di->get('db');

if (getenv('TEST_TOKEN') === false
    && (
        $_SERVER['PHP_SELF'] === '/app/vendor/bin/phpunit'
        || substr($_SERVER['PHP_SELF'], 0, 19) === '/app/vendor/phpunit'
    )
) {
    putenv('TEST_TOKEN=1');
}

if (getenv('TEST_TOKEN') === false) {
    return;
}

$dbName = 'testdb_' . getenv('TEST_TOKEN');

$db->query('DROP DATABASE IF EXISTS ' . $dbName)->execute();
$db->query('CREATE DATABASE IF NOT EXISTS ' . $dbName)->execute();

$customConfig = [
    'database' => [
        'dbname' => $dbName
    ],
];

$bootstrap->setupTestDb($customConfig);

/** @var MigrationManager $migrationManager */
$migrationManager = (new Injectable)->inject(MigrationManager::class);
$migrationManager->runMigrations();
