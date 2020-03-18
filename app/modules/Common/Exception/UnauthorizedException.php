<?php
declare(strict_types=1);

namespace Common\Exception;

use Common\Config\DefaultErrorCodes;

class UnauthorizedException extends DefaultException
{
    public function __construct(
        string $message = 'Unauthorized',
        string $code = DefaultErrorCodes::UNAUTHORIZED,
        int $httpCode = 401
    ) {
        parent::__construct($message, $code, $httpCode);
    }
}
