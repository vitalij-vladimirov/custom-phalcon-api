<?php
declare(strict_types=1);

namespace Common\Config;

class DefaultErrorCodes
{
    public const BAD_REQUEST = 'bad_request';               // 400
    public const UNAUTHORIZED = 'unauthorized';             // 401
    public const FORBIDDEN = 'forbidden';                   // 403
    public const NOT_FOUND = 'not_found';                   // 404
    public const METHOD_NOT_ALLOWED = 'method_not_allowed'; // 405
    public const CONFLICT = 'conflict';                     // 409
    public const TOO_MANY_REQUESTS = 'too_many_requests';   // 429
    public const INTERNAL_ERROR = 'internal_error';
}
