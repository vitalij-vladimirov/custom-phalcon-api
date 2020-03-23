<?php
declare(strict_types=1);

namespace Example\Config;

use Common\Classes\BaseRoutes;

class Routes extends BaseRoutes
{
    public function get()
    {
        return [
            'test' => 'ok',
        ];
    }
}
