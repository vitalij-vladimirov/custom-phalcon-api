<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Common\Interfaces\RoutesInterface;
use Common\Entity\RequestEntity;
use Dice\Dice;

abstract class BaseRoutes implements RoutesInterface
{
    protected Dice $di;
    protected RequestEntity $request;

    public function __construct(RequestEntity $request)
    {
        $this->di = new Dice();
        $this->request = $request;
    }
}
