<?php
declare(strict_types=1);

namespace Common\Exception;

use Common\Config\DefaultErrorCodes;

class MethodNotAllowedException extends DefaultException
{
    public function __construct(
        string $message = 'Method not allowed',
        string $code = DefaultErrorCodes::METHOD_NOT_ALLOWED,
        int $httpCode = 405
    ) {
        parent::__construct($message, $code, $httpCode);
    }
}
