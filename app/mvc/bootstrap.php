<?php
declare(strict_types=1);

define('APP_PATH', '/app');

use Dotenv\Dotenv;

include APP_PATH . '/vendor/autoload.php';
$dotenv = (Dotenv::createImmutable(APP_PATH . '/'))->load();

include APP_PATH . '/mvc/services.php';
$config = $di->getConfig();

include APP_PATH . '/mvc/loader.php';
