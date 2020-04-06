<?php
declare(strict_types=1);

namespace Mvc;

use Phalcon\Mvc\Micro;

class Routes implements RouterInterface
{
    public function getRoutes(Micro $app)
    {
        $app->get('/api/example', function () use ($app) {
            return $app['view']->render('index');
        });

        $app->get('/', function () use ($app) {
            return $app['view']->render('index');
        });

        $app->notFound(function () use ($app) {
            $app->response->setStatusCode(404, "Not Found")->sendHeaders();
            return $app['view']->render('404');
        });
    }
}
