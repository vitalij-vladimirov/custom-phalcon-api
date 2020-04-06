<?php
declare(strict_types=1);

namespace Example\Config;

use Common\BaseClasses\BaseRoutes;
use Example\Controller\ExampleController;

class Routes extends BaseRoutes
{
    public function get()
    {
//        return (new ExampleController())->getJoke();
        dd((new ExampleController())->getJoke());
    }
}
