<?php
declare(strict_types=1);

namespace Common\Exception;

use Exception;

abstract class DefaultException extends Exception
{
    protected int $httpCode;

    public function __construct(string $message, string $code, int $httpCode)
    {
        parent::__construct($message);

        $this->code = $code;
        $this->httpCode = $httpCode;
    }
}
