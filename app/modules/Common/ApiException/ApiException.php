<?php
declare(strict_types=1);

namespace Common\ApiException;

use Common\Exception\DefaultException;

class ApiException extends DefaultException
{
    public function __construct(string $message, string $code, int $httpCode)
    {
        parent::__construct($message, $code, $httpCode);
    }
}
