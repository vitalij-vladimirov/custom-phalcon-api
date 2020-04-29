<?php
declare(strict_types=1);

namespace Common\Interfaces;

use Common\Entity\RequestData;

interface RoutesInterface
{
    public function get(RequestData $request);
}
