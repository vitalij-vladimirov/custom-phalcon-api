<?php
declare(strict_types=1);

namespace Common;

use Carbon\Carbon;
use DateTimeImmutable;
use DateTime;

class Variable
{
    public const VAR_TYPE_STRING = 'string';
    public const VAR_TYPE_INT = 'int';
    public const VAR_TYPE_FLOAT = 'float';
    public const VAR_TYPE_BOOL = 'bool';
    public const VAR_TYPE_ARRAY = 'array';
    public const VAR_TYPE_OBJECT = 'object';
    public const VAR_TYPE_NULL = 'null';

    public const DEFAULT_VAR_TYPES = [
        self::VAR_TYPE_STRING,
        self::VAR_TYPE_INT,
        self::VAR_TYPE_FLOAT,
        self::VAR_TYPE_BOOL,
        self::VAR_TYPE_ARRAY,
        self::VAR_TYPE_OBJECT,
        self::VAR_TYPE_NULL,
    ];

    /**
     * This method is used to convert strings to proper types:
     *      int, float, json => array, bool, null
     *
     * @param array $variables      - list of variables to be restored
     * @param bool $trimString      - trim strings
     * @param bool $convertToNull   - convert empty values to null
     * @param bool $convertToBool   - convert [0 & 1] to [false & true]
     * @return array
     */
    public static function restoreTypes(
        array $variables,
        bool $trimString = true,
        bool $convertToNull = true,
        bool $convertToBool = false
    ): array {
        $restoredVariables = [];

        foreach ($variables as $key => $value) {
            if ($trimString && self::isString($value)) {
                $value = trim($value);
            }

            if ($convertToBool && self::isBoolean($value)) {
                $restoredVariables[$key] = (bool)$value;
                continue;
            }

            if (self::isFloat($value)) {
                $restoredVariables[$key] = (float)str_replace(',', '.', $value);
                continue;
            }

            if (self::isInteger($value)) {
                $restoredVariables[$key] = (int)$value;
                continue;
            }

            if (self::isString($value) && Json::isJson($value)) {
                $restoredVariables[$key] = self::restoreTypes(Json::decode($value));
                continue;
            }

            if ($convertToNull && empty($value)) {
                $restoredVariables[$key] = null;
                continue;
            }

            if (is_array($value)) {
                $restoredVariables[$key] = self::restoreTypes($value);
                continue;
            }

            $restoredVariables[$key] = $value;
        }

        return $restoredVariables;
    }

    public static function convertTimeObjectToString(
        $object,
        bool $toTimestamp = true,
        string $format = 'Y-m-d H:i:s'
    ) {
        if (self::isDateTime($object) || self::isDateTimeImmutable($object)) {
            $dateTime = Carbon::instance($object);
        } else {
            $dateTime = $object;
        }

        if ($toTimestamp) {
            return $dateTime->timestamp;
        }
        return $dateTime->format($format);
    }

    public static function getType($variable): string
    {
        $type = gettype($variable);

        switch ($type) {
            case 'integer':
                return self::VAR_TYPE_INT;
            case 'double':
                return self::VAR_TYPE_FLOAT;
            case 'boolean':
                return self::VAR_TYPE_BOOL;
            case 'NULL':
                return self::VAR_TYPE_NULL;
        }

        return $type;
    }

    public static function isDefaultType(string $type): bool
    {
        return in_array($type, self::DEFAULT_VAR_TYPES, true);
    }

    public static function isString($variable): bool
    {
        return is_string($variable);
    }

    public static function isFloat($variable, bool $strictCheck = false): bool
    {
        if ($strictCheck === true) {
            return is_float($variable);
        }

        if (self::isString($variable)
            && (strpos($variable, '.') !== false || strpos($variable, ',') !== false)
            && is_numeric(str_replace(',', '.', $variable))
        ) {
            $variable = (float)str_replace(',', '.', $variable);

            if (strpos((string)$variable, '.') === false) {
                return false;
            }

            return true;
        }

        return false;
    }

    public static function isInteger($variable, bool $strictCheck = false): bool
    {
        if ($strictCheck === true) {
            return is_int($variable);
        }

        if (self::isString($variable) && is_numeric($variable)) {
            $variable = (float)str_replace(',', '.', $variable);

            if (strpos((string)$variable, '.') === false) {
                return true;
            }

            return false;
        }

        return false;
    }

    public static function isBoolean($variable, bool $strictCheck = false): bool
    {
        if ($strictCheck === true) {
            return is_bool($variable);
        }

        return (is_int($variable) || (self::isString($variable) && is_numeric($variable)))
            && in_array($variable, ['0', '1', 0, 1], true);
    }

    public static function isObject($variable): bool
    {
        return gettype($variable) === self::VAR_TYPE_OBJECT;
    }

    public static function isCarbon($variable): bool
    {
        return self::isObject($variable) && $variable instanceof Carbon;
    }

    public static function isDateTime($variable): bool
    {
        return self::isObject($variable) && $variable instanceof DateTime;
    }

    public static function isDateTimeImmutable($variable): bool
    {
        return self::isObject($variable) && $variable instanceof DateTimeImmutable;
    }

    public static function isDateTimeObject($variable): bool
    {
        if (!self::isObject($variable)) {
            return false;
        }

        if ($variable instanceof Carbon) {
            return true;
        }

        if ($variable instanceof DateTime) {
            return true;
        }

        if ($variable instanceof DateTimeImmutable) {
            return true;
        }

        return false;
    }
}
