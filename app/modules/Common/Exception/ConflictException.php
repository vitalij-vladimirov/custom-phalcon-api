<?php
declare(strict_types=1);

namespace Common\Exception;

use Common\Config\DefaultErrorCodes;

class ConflictException extends DefaultException
{
    public function __construct(
        string $message = 'Conflict',
        string $code = DefaultErrorCodes::CONFLICT,
        int $httpCode = 409
    ) {
        parent::__construct($message, $code, $httpCode);
    }
}
