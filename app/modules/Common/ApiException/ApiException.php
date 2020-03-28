<?php
declare(strict_types=1);

namespace Common\ApiException;

use Common\Exception\DefaultException;

class ApiException extends DefaultException
{
    private int $httpCode;
    private array $data;

    public function __construct(string $message, string $code, int $httpCode, array $data = [])
    {
        parent::__construct($message, $code);

        $this->httpCode = $httpCode;
        $this->data = $data;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
