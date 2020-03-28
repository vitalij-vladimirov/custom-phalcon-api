<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Mvc\Bootstrap;
use Dotenv\Dotenv;

include '../vendor/autoload.php';
include '../mvc/Bootstrap.php';

(Dotenv::createImmutable('../'))->load();

try {
    $bootstrap = new Bootstrap();
    $app = $bootstrap->runApp();
    $config = $bootstrap->getConfig();

    $app->handle($_SERVER['REQUEST_URI']);
} catch (Exception $exception) {
    echo '<strong>Error:</strong> ' . $exception->getFile() . ':' . $exception->getLine() . '<br>';
    if (!empty($exception->getMessage())) {
        echo "\n" . '<strong>Message:</strong> ' . $exception->getMessage();
    }
}
