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
    public const LOGIC_EXCEPTION = 'logic_exception';
    public const DATABASE_EXCEPTION = 'database_exception';

    public const INVALID_PARAMETERS = 'invalid_parameters';
    public const IS_REQUIRED = 'is_required';
    public const INVALID_LENGTH = 'invalid_length';
    public const UNSUPPORTED_VALUE = 'unsupported_value';
    public const BAD_EMAIL = 'bad_email';
    public const INVALID_FORMAT = 'invalid_format';
    public const INVALID_TYPE = 'invalid_type';
    public const DUPLICATE = 'duplicate';
    public const VALUES_DOES_NOT_MATCH = 'values_does_not_match';
    public const INVALID_FILE = 'invalid_file';
    public const INVALID_MIME_TYPE = 'invalid_mime_type';
    public const INVALID_FILE_SIZE = 'invalid_file_size';
    public const INVALID_RESOLUTION = 'invalid_resolution';
    public const VALIDATION_ERROR = 'validation_error';
    public const UNKNOWN_ERROR = 'unknown_error';
}
