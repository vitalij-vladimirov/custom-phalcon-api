<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;

$di = new FactoryDefault();

include '../mvc/bootstrap.php';

$app = new Micro($di);

include '../mvc/app.php';

try {
    $app->handle($_SERVER['REQUEST_URI']);
} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
