<?php
declare(strict_types=1);

namespace Common\ApiException;

use Common\Config\DefaultErrorCodes;

class ConflictApiException extends ApiException
{
    public function __construct(
        string $message = 'Conflict',
        string $code = DefaultErrorCodes::CONFLICT,
        array $data = []
    ) {
        parent::__construct($message, $code, 409, $data);
    }
}
