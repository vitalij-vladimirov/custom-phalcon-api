<?php
declare(strict_types=1);

namespace Common;

use Carbon\Carbon;
use DateTimeImmutable;
use DateTime;
use Exception;
use Common\Service\InjectionService;
use Throwable;

class Variable
{
    public const VAR_TYPE_STRING = 'string';
    public const VAR_TYPE_INT = 'int';
    public const VAR_TYPE_FLOAT = 'float';
    public const VAR_TYPE_BOOL = 'bool';
    public const VAR_TYPE_ARRAY = 'array';
    public const VAR_TYPE_OBJECT = 'object';
    public const VAR_TYPE_NULL = 'null';

    public const VAR_TYPE_JSON = 'json';

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
     * @param string[] $fields          - fields that have to be restored
     * @return array
     */
    public static function restoreArrayTypes(
        array $variables,
        bool $trimString = true,
        bool $convertToNull = true,
        bool $convertToBool = false,
        array $fields = ['*']
    ): array {
        $restoredVariables = [];

        foreach ($variables as $key => $variable) {
            $restoredVariables[$key] = $variable;

            if ((count($fields) === 1 && $fields[0] === '*') || isset($fields[$key])) {
                $restoredVariables[$key] = self::restoreVariableType(
                    $variable,
                    $trimString,
                    $convertToNull,
                    $convertToBool
                );
            }
        }

        return $restoredVariables;
    }

    /**
     * @param mixed $variable
     * @param bool $trimString
     * @param bool $convertToNull
     * @param bool $convertToBool
     * @return mixed
     */
    public static function restoreVariableType(
        $variable,
        bool $trimString = true,
        bool $convertToNull = true,
        bool $convertToBool = false
    ) {
        if ($trimString && self::isString($variable)) {
            $variable = trim($variable);
        }

        if (self::isBool($variable, true)
            || ($convertToBool && self::isBool($variable, false))
        ) {
            if (in_array(
                is_string($variable) ? strtolower($variable) : $variable,
                ['1', 1, 'y', 'yes', 'true', 't'],
                true
            )) {
                return true;
            }

            if (in_array(
                is_string($variable) ? strtolower($variable) : $variable,
                ['0', 0, 'n', 'no', 'false', 'f'],
                true
            )) {
                return false;
            }

            return (bool)$variable;
        }

        if (self::isInteger($variable)) {
            return (int)$variable;
        }

        if (self::isFloat($variable)) {
            return (float)str_replace(',', '.', $variable);
        }

        if (Json::isJson($variable)) {
            return self::restoreArrayTypes(Json::decode($variable));
        }

        if ($convertToNull && empty($variable)) {
            return null;
        }

        if (self::isArray($variable)) {
            return self::restoreArrayTypes($variable);
        }

        return $variable;
    }

    /**
     * @param Carbon|DateTime|DateTimeImmutable $object
     * @param bool $toTimestamp
     * @param string $format
     * @return int|string
     * @throws Exception
     */
    public static function convertTimeObjectToString(
        $object,
        bool $toTimestamp = true,
        string $format = 'Y-m-d H:i:s'
    ) {
        if (self::isObject($object, DateTime::class)
            || self::isObject($object, DateTimeImmutable::class)
        ) {
            $object = Carbon::instance($object);
        }

        if ($toTimestamp) {
            return $object->timestamp;
        }

        return $object->format($format);
    }

    public static function getType($variable, bool $defaultCheck = false): string
    {
        if ($variable === null || gettype($variable) === 'NULL') {
            return self::VAR_TYPE_NULL;
        }

        if (self::isString($variable, $defaultCheck)) {
            return self::VAR_TYPE_STRING;
        }

        if (self::isInteger($variable, $defaultCheck)) {
            return self::VAR_TYPE_INT;
        }

        if (self::isFloat($variable, $defaultCheck)) {
            return self::VAR_TYPE_FLOAT;
        }

        if (self::isBool($variable, $defaultCheck)) {
            return self::VAR_TYPE_BOOL;
        }

        if (self::isObject($variable)) {
            return self::VAR_TYPE_OBJECT;
        }

        if (self::isArray($variable)) {
            return self::VAR_TYPE_ARRAY;
        }

        if (Json::isJson($variable)) {
            return self::VAR_TYPE_JSON;
        }

        return gettype($variable);
    }

    public static function isDefaultType(string $type): bool
    {
        return in_array($type, self::DEFAULT_VAR_TYPES, true);
    }

    public static function isString($variable, bool $defaultCheck = false): bool
    {
        if ($defaultCheck) {
            return is_string($variable);
        }

        if (is_string($variable)
            && in_array(
                strtolower($variable),
                ['0', '1', 'y', 'yes', 'n', 'no', 'true', 'false', 't', 'f'],
                true
            )
        ) {
            return false;
        }

        return is_string($variable)
            && !is_numeric(str_replace(',', '.', $variable))
            && !Json::isJson($variable)
        ;
    }

    public static function isFloat($variable, bool $defaultCheck = false): bool
    {
        if (is_float($variable) && ($defaultCheck || $variable !== (float)(int)$variable)) {
            return true;
        }

        if ($defaultCheck) {
            return false;
        }

        $variable = str_replace(',', '.', $variable);

        if (!is_numeric($variable)) {
            return false;
        }

        $variable = (float)str_replace(',', '.', $variable);

        if ($variable !== (float)(int)$variable) {
            return true;
        }

        return false;
    }

    public static function isInteger($variable, bool $defaultCheck = false): bool
    {
        if (is_int($variable)) {
            return true;
        }

        if ($defaultCheck || is_bool($variable)) {
            return false;
        }

        $variable = str_replace(',', '.', $variable);

        if (!is_numeric($variable)) {
            return false;
        }

        $variable = (float)str_replace(',', '.', $variable);

        if ($variable === (float)(int)$variable) {
            return true;
        }

        return false;
    }

    public static function isBool($variable, bool $defaultCheck = true): bool
    {
        if (is_bool($variable)) {
            return true;
        }

        if ($defaultCheck) {
            return false;
        }

        return in_array(
            is_string($variable) ? strtolower($variable) : $variable,
            ['0', '1', 0, 1, 'y', 'yes', 'n', 'no', 'true', 'false', 't', 'f'],
            true
        );
    }

    public static function isArray($variable): bool
    {
        return is_array($variable);
    }

    /**
     * @param mixed $variable
     * @param object|string|null $instanceOf
     * @return bool
     */
    public static function isObject($variable, $instanceOf = null): bool
    {
        if (gettype($variable) !== self::VAR_TYPE_OBJECT) {
            return false;
        }

        if ($instanceOf === null) {
            return true;
        }

        if (gettype($instanceOf) === self::VAR_TYPE_OBJECT) {
            return $variable instanceof $instanceOf;
        }

        if (self::isString($instanceOf)) {
            try {
                $object = (new InjectionService())->inject($instanceOf);
            } catch (Throwable $exception) {
                return false;
            }

            return $variable instanceof $object;
        }

        return false;
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
