<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Config;
use Dice\Dice;
use Common\Interfaces\RoutesInterface;
use Common\Entity\RequestEntity;

abstract class BaseRoutes implements RoutesInterface
{
    protected Config $config;
    protected Dice $inject;

    protected RequestEntity $request;

    public function __construct(RequestEntity $request)
    {
        $this->config = $GLOBALS['app']->di->getShared('config');
        $this->inject = new Dice();

        $this->request = $request;
    }
}
