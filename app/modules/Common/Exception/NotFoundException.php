<?php
declare(strict_types=1);

namespace Common\Exception;

use Common\Config\DefaultErrorCodes;

class NotFoundException extends DefaultException
{
    public function __construct(
        string $message = 'Not found',
        string $code = DefaultErrorCodes::NOT_FOUND,
        int $httpCode = 404
    ) {
        parent::__construct($message, $code, $httpCode);
    }
}
