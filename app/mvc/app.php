<?php

use Dice\Dice;
$dice = new Dice();

/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

/**
 * Add your routes here
 */
$app->get('/', function () use ($dice) {
    $run = $dice->create(\Test\Controller\NewController::class);
    $run->runTest();
});

/**
 * Not found handler
 */
$app->notFound(function () use($app) {
    $app->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo $app['view']->render('404');
});
