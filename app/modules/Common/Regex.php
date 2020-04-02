<?php
declare(strict_types=1);

namespace Common;

class Regex
{
    public const VAR_AZ_LOWER = '/^[a-z0-9]{1,}$/';
    public const VAR_AZ_UPPER = '/^[A-Z]{1,}$/';
    public const VAR_AZ_UPPER_FIRST = '/^[A-Z]+[a-z0-9]{1,}$/';
    public const VAR_PASCAL_CASE = '/^[A-Z]+[a-z0-9]{1,}+[A-Z]+[a-zA-Z0-9]{1,}$/';
    public const VAR_CAMEL_CASE = '/^[a-z]{1,}+[A-Z]+[a-zA-Z0-9]{1,}$/';
    public const VAR_SNAKE_CASE = '/^[a-z]+[_]+[a-z_]{1,}$/';
    public const VAR_KEBAB_CASE = '/^[a-z]+[-]+[a-z\\-]{1,}$/';
    public const VAR_RAW_CASE = '/^[a-zA-Z]{1,}+[a-zA-Z0-9 _\\-]{1,}$/';
    
    public const METHOD_TYPE_GET = 'getter';
    public const METHOD_TYPE_SET = 'setter';
    public const METHOD_TYPE_SET_GET = 'set_get';

    public static function isValidPattern(string $value, string $pattern): bool
    {
        return preg_match($pattern, $value) ? true : false;
    }

    public static function isEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? true : false;
    }

    public static function isMethodName(string $value, string $type = null): bool
    {
        if ($type !== null && !preg_match('/^[a-z]/', $type)) {
            return false;
        }

        if ($type === self::METHOD_TYPE_SET_GET) {
            return self::isValidPattern($value, '/^(get|is|set)+[A-Z]+[a-zA-Z0-9]/');
        }

        if ($type === self::METHOD_TYPE_GET) {
            return self::isValidPattern($value, '/^(get|is)+[A-Z]+[a-zA-Z0-9]/');
        }

        if ($type === self::METHOD_TYPE_SET) {
            return self::isValidPattern($value, '/^(set)+[A-Z]+[a-zA-Z0-9]/');
        }

        if ($type !== null) {
            return self::isValidPattern($value, '/^(' . $type . ')+[A-Z]+[a-zA-Z0-9]/');
        }

        return self::isValidPattern($value, self::VAR_CAMEL_CASE);
    }

    public static function isClassName(string $value): bool
    {
        return self::isValidPattern($value, self::VAR_PASCAL_CASE);
    }
}
