<?php
declare(strict_types=1);

namespace Common\Exception;

use Common\Config\DefaultErrorCodes;

final class LogicException extends DefaultException
{
    public function __construct(
        string $message,
        string $code = DefaultErrorCodes::LOGIC_EXCEPTION
    ) {
        parent::__construct($message, $code);
    }
}
