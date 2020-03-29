<?php
declare(strict_types=1);

namespace Common;

use Common\Exception\BadRequestException;

class Regex
{
    public const METHOD_GET = 'getter';
    public const METHOD_SET = 'setter';
    public const METHOD_SET_GET = 'set_get';

    public static function isEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? true : false;
    }

    public static function isMethodName(string $value, string $type = null): bool
    {
        if ($type !== null && !preg_match('/^[a-z]/', $type)) {
            throw new BadRequestException('Method type must start from lowercase letter');
        }

        if ($type === self::METHOD_SET_GET) {
            return preg_match('/^(get|is|set)+[A-Z]+[a-zA-Z0-9]/', $value) ? true : false;
        }

        if ($type === self::METHOD_GET) {
            return preg_match('/^(get|is)+[A-Z]+[a-zA-Z0-9]/', $value) ? true : false;
        }

        if ($type === self::METHOD_SET) {
            return preg_match('/^(set)+[A-Z]+[a-zA-Z0-9]/', $value) ? true : false;
        }

        if ($type !== null) {
            return preg_match('/^(' . $type . ')+[A-Z]+[a-zA-Z0-9]/', $value) ? true : false;
        }

        return preg_match('/^[a-z]+[a-zA-Z0-9]/', $value) ? true : false;
    }

    public static function isClassName(string $value)
    {
        return preg_match('/^[A-Z]+[a-zA-Z0-9]/', $value);
    }
}
