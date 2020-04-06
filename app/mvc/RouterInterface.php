<?php
declare(strict_types=1);

namespace Mvc;

use Phalcon\Mvc\Micro;

interface RouterInterface
{
    public function getRoutes(Micro $app);
}
