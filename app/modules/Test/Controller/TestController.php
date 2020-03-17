<?php
declare(strict_types=1);

namespace Test\Controller;

use DateTimeImmutable;

class TestController
{
    public function index(): string
    {
        return 'Index is ok<br>' .
            (new DateTimeImmutable())->format('Y-m-d H:i:s') .
            '<br>' .
            getenv('DB_CONNECTION') .
            '<br>'
        ;
    }
}
