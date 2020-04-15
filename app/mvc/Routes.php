<?php
declare(strict_types=1);

namespace Mvc;

use Phalcon\Di\Injectable;
use Phalcon\Mvc\Micro;

class Routes extends Injectable implements RouterInterface
{
    public function getRoutes(Micro $app): void
    {
        $app->get('/', function () use ($app) {
            return $app['view']->render('index');
        });

        $app->notFound(function () use ($app) {
            $app->response->setStatusCode(404, 'Not Found')->sendHeaders();
            return $app['view']->render('404');
        });
    }
}
