<?php
declare(strict_types=1);

namespace Common\Exception;

use Common\Config\DefaultErrorCodes;

final class DatabaseException extends DefaultException
{
    public function __construct(
        string $message,
        string $code = DefaultErrorCodes::DATABASE_EXCEPTION
    ) {
        parent::__construct($message, $code);
    }
}
