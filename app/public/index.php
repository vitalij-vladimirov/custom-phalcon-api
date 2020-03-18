<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use Common\Json;
use Common\ApiException\ApiException;

$di = new FactoryDefault();

include '../mvc/bootstrap.php';

$app = new Micro($di);

include '../mvc/app.php';

try {
    echo $app->handle($_SERVER['REQUEST_URI']);
} catch (ApiException $exception) {
    http_response_code($exception->getHttpCode());
    header('Content-type:application/json;charset=utf-8');

    echo Json::encode([
        'code' => $exception->getCode(),
        'message' => $exception->getMessage()
    ]);
} catch (Exception $exception) {
    echo $exception->getMessage() . '<br>';
    echo $exception->getCode() . '<br>';
}
