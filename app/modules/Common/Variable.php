<?php
declare(strict_types=1);

namespace Common;

class Variable
{
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
            if ($trimString && is_string($value)) {
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

            if (is_string($value) && Json::isJson($value)) {
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

    public static function isFloat($variable, bool $strictCheck = false): bool
    {
        if ($strictCheck === true) {
            return is_float($variable);
        }

        if (is_string($variable)
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

        if (is_string($variable) && is_numeric($variable)) {
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

        return (is_int($variable) || (is_string($variable) && is_numeric($variable)))
            && in_array($variable, ['0', '1', 0, 1], true);
    }
}
