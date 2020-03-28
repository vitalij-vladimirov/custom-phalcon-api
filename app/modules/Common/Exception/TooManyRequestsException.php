<?php
declare(strict_types=1);

namespace Common\Exception;

use Common\Config\DefaultErrorCodes;

class TooManyRequestsException extends DefaultException
{
    public function __construct(
        string $message = 'Too Many Requests',
        string $code = DefaultErrorCodes::TOO_MANY_REQUESTS
    ) {
        parent::__construct($message, $code);
    }
}
