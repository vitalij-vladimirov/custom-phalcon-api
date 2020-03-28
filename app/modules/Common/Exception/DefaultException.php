<?php
declare(strict_types=1);

namespace Common\Exception;

use Exception;

abstract class DefaultException extends Exception
{
    public function __construct(string $message, string $code)
    {
        parent::__construct($message);

        $this->code = $code;
    }
}
