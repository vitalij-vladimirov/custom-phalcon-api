<?php
declare(strict_types=1);

namespace Common;

use Phalcon\Text as PhalconText;

class Text extends PhalconText
{
    public static function pascalize(string $value): string
    {
        return lcfirst(self::camelize($value));
    }

    public static function uncamelizeMethod(string $value): string
    {
        if (Regex::isMethodName($value, Regex::METHOD_SET_GET)) {
            $value = preg_replace('/^(get|is|set)/', '', $value);
        }

        return self::uncamelize($value);
    }

    public static function methodToVariable(string $value): string
    {
        if (Regex::isMethodName($value, Regex::METHOD_GET)) {
            $value = preg_replace('/^(get|is|set)/', '', $value);
        }

        return lcfirst($value);
    }
}
