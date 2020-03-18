<?php
declare(strict_types=1);

namespace Common\ApiException;

use Common\Config\DefaultErrorCodes;

class TooManyRequestsApiException extends ApiException
{
    public function __construct(
        string $message = 'Too Many Requests',
        string $code = DefaultErrorCodes::TOO_MANY_REQUESTS,
        int $httpCode = 429
    ) {
        parent::__construct($message, $code, $httpCode);
    }
}
