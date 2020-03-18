<?php
declare(strict_types=1);

namespace Common\ApiException;

use Common\Config\DefaultErrorCodes;

class BadRequestApiException extends ApiException
{
    public function __construct(
        string $message = 'Bad request',
        string $code = DefaultErrorCodes::BAD_REQUEST,
        int $httpCode = 400
    ) {
        parent::__construct($message, $code, $httpCode);
    }
}