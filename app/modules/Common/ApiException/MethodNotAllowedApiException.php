<?php
declare(strict_types=1);

namespace Common\ApiException;

use Common\Config\DefaultErrorCodes;

class MethodNotAllowedApiException extends ApiException
{
    public function __construct(
        string $message = 'Method not allowed',
        string $code = DefaultErrorCodes::METHOD_NOT_ALLOWED,
        int $httpCode = 405
    ) {
        parent::__construct($message, $code, $httpCode);
    }
}
