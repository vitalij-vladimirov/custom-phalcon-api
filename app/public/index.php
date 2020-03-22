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

$bootstrap = new Bootstrap();
$app = $bootstrap->runApp();
$config = $bootstrap->getConfig();

try {
    $app->handle($_SERVER['REQUEST_URI']);
} catch (ApiException $e) {
    $app->response
        ->setStatusCode($e->getHttpCode(), $e->getMessage())
        ->setContentType('application/json; charset=utf-8')
        ->sendHeaders()
    ;

    echo Json::encode([
        'code' => $e->getCode(),
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    throw new $e;
}
