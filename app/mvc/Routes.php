<?php
declare(strict_types=1);

namespace Mvc;

use Phalcon\Config;
use Phalcon\Mvc\Micro;

class Routes implements RouterInterface
{
    public function getRoutes(Micro $app, Config $config): Micro
    {
        $app->get('/', function () use ($app) {
            return $app['view']->render('index');
        });

        $app->notFound(function () use ($app) {
            $app->response->setStatusCode(404, "Not Found")->sendHeaders();
            return $app['view']->render('404');
        });

        return $app;
    }
}
