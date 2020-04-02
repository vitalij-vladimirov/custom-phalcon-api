<?php
declare(strict_types=1);

namespace Common\Exception;

use Common\Config\DefaultErrorCodes;

final class InternalErrorException extends DefaultException
{
    public function __construct(
        string $message = 'Internal error',
        string $code = DefaultErrorCodes::INTERNAL_ERROR
    ) {
        parent::__construct($message, $code);
    }
}
