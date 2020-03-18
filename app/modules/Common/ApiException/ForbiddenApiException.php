<?php
declare(strict_types=1);

namespace Common\ApiException;

use Common\Config\DefaultErrorCodes;

class ForbiddenApiException extends ApiException
{
    public function __construct(
        string $message = 'Forbidden',
        string $code = DefaultErrorCodes::FORBIDDEN,
        int $httpCode = 409
    ) {
        parent::__construct($message, $code, $httpCode);
    }
}
