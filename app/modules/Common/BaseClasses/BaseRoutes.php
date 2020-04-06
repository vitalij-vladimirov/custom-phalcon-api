<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Common\Interfaces\RoutesInterface;
use Common\Entity\RequestEntity;
use Phalcon\Config;

abstract class BaseRoutes implements RoutesInterface
{
    protected Config $config;
    protected RequestEntity $request;

    public function __construct(RequestEntity $request)
    {
        $this->config = $GLOBALS['app']->di->getShared('config');
        $this->request = $request;
    }
}
