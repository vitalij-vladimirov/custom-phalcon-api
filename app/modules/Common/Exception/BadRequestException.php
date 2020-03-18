<?php
declare(strict_types=1);

namespace Common\Exception;

use Common\Config\DefaultErrorCodes;

class BadRequestException extends DefaultException
{
    public function __construct(
        string $message = 'Bad request',
        string $code = DefaultErrorCodes::BAD_REQUEST,
        int $httpCode = 400
    ) {
        parent::__construct($message, $code, $httpCode);
    }
}
