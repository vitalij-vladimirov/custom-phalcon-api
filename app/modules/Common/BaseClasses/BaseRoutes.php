<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Common\Interfaces\RoutesInterface;
use Common\Entity\RequestEntity;

abstract class BaseRoutes extends Injectable implements RoutesInterface
{
    protected RequestEntity $request;

    public function __construct(RequestEntity $request)
    {
        $this->request = $request;
    }

    abstract public function get();
}
