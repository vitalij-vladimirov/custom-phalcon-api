<?php
declare(strict_types=1);

namespace Common\Classes;

use Common\Interfaces\RoutesInterface;
use Common\Entity\RequestEntity;

abstract class BaseRoutes implements RoutesInterface
{
    private RequestEntity $request;

    public function __construct(RequestEntity $request)
    {
        $this->request = $request;
    }
}
