<?php

namespace BaseMvc;

use Common\ApiException\NotFoundApiException;
use Dice\Dice;
use Phalcon\Mvc\Micro;

$dice = new Dice();

/**
 * Local variables
 * @var Micro $app
 */

$app->get('/', function () use ($dice) {
    $run = $dice->create(\Example\Controller\ExampleController::class);
    echo $run->getJoke(2);
});

$app->notFound(function () use ($app) {
    throw new NotFoundApiException();
});
