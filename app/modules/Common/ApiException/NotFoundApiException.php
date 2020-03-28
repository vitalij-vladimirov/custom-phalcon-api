<?php
declare(strict_types=1);

namespace Common\ApiException;

use Common\Config\DefaultErrorCodes;

class NotFoundApiException extends ApiException
{
    public function __construct(
        string $message = 'Not found',
        string $code = DefaultErrorCodes::NOT_FOUND,
        array $data = []
    ) {
        parent::__construct($message, $code, 404, $data);
    }
}
