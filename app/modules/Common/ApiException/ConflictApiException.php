<?php
declare(strict_types=1);

namespace Common\ApiException;

use Common\Config\DefaultErrorCodes;

class ConflictApiException extends ApiException
{
    public function __construct(
        string $message = 'Conflict',
        string $code = DefaultErrorCodes::CONFLICT,
        int $httpCode = 409
    ) {
        parent::__construct($message, $code, $httpCode);
    }
}
