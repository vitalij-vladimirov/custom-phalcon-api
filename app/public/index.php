<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use BaseMvc\bootstrap;
use Dotenv\Dotenv;
use Common\Json;
use Common\ApiException\ApiException;

include '../vendor/autoload.php';
include '../mvc/Bootstrap.php';

(Dotenv::createImmutable('../'))->load();

$app = (new Bootstrap())->runApp();

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
