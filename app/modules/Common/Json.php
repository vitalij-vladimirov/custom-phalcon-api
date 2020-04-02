<?php
declare(strict_types=1);

namespace Common;

class Json
{
    public static function encode(array $array): string
    {
        return json_encode($array, JSON_THROW_ON_ERROR, JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    public static function decode(string $json): array
    {
        return json_decode($json, true, JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_THROW_ON_ERROR);
    }

    public static function isJson($variable): bool
    {
        if (!Variable::isString($variable, true)
            || (
                Variable::isString($variable, true)
                && in_array(strtolower($variable), ['0', '1', 'true', 'false'], true)
            )
        ) {
            return false;
        }

        json_decode($variable);

        return (json_last_error() === JSON_ERROR_NONE);
    }
}
