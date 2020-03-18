<?php
declare(strict_types=1);

namespace Common\ApiException;

use Common\Exception\DefaultException;

class ApiException extends DefaultException
{
    private int $httpCode;

    public function __construct(string $message, string $code, int $httpCode)
    {
        parent::__construct($message, $code);

        $this->httpCode = $httpCode;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
