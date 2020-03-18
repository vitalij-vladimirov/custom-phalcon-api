<?php
declare(strict_types=1);

namespace Common\ApiException;

use Common\Config\DefaultErrorCodes;

class UnauthorizedApiException extends ApiException
{
    public function __construct(
        string $message = 'Unauthorized',
        string $code = DefaultErrorCodes::UNAUTHORIZED,
        int $httpCode = 401
    ) {
        parent::__construct($message, $code, $httpCode);
    }
}
