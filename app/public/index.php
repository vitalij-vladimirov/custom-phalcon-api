<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Mvc\Bootstrap;
use Mvc\RouterInterface;
use Mvc\Routes;

include '../vendor/autoload.php';
include '../mvc/Bootstrap.php';

try {
    $app = (new Bootstrap())->runApp();

    $router = $app->di->get('config')->customRouter ?? Routes::class;

    if (!isset(class_implements($router)[RouterInterface::class])) {
        throw new Exception('Router must implement \Mvc\RouterInterface');
    }

    (new $router())->getRoutes($app);

    $app->handle($_SERVER['REQUEST_URI']);
} catch (Exception $exception) {
    echo '<strong>Error:</strong> ' . $exception->getFile() . ':' . $exception->getLine() . '<br>';
    if (!empty($exception->getMessage())) {
        echo "\n" . '<strong>Message:</strong> ' . $exception->getMessage();
    }
}
