<?php
declare(strict_types=1);

namespace Common\Exception;

use Common\Config\DefaultErrorCodes;

class ForbiddenException extends DefaultException
{
    public function __construct(
        string $message = 'Forbidden',
        string $code = DefaultErrorCodes::FORBIDDEN
    ) {
        parent::__construct($message, $code);
    }
}
