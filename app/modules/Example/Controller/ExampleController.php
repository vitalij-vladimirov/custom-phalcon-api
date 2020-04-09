<?php
declare(strict_types=1);

namespace Example\Controller;

use Common\BaseClasses\BaseController;

class ExampleController extends BaseController
{
    public function getLibs(): array
    {
//        return self::JOKES[rand(0, count(self::JOKES)-1)];
    }
}
