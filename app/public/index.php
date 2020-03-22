<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Phalcon\Http\Response;
use BaseMvc\bootstrap;
use Dotenv\Dotenv;
use Common\ApiException\ApiException;

include '../vendor/autoload.php';
include '../mvc/Bootstrap.php';

(Dotenv::createImmutable('../'))->load();

try {
    $bootstrap = new Bootstrap();
    $app = $bootstrap->runApp();
    $config = $bootstrap->getConfig();

    $app->handle($_SERVER['REQUEST_URI']);
} catch (ApiException $e) {
    (new Response())
        ->setStatusCode($e->getHttpCode(), $e->getMessage())
        ->setJsonContent([
            'code' => $e->getCode(),
            'message' => $e->getMessage()
        ])
        ->send()
    ;
} catch (Exception $e) {
    throw new $e;
}
