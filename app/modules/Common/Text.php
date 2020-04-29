<?php
declare(strict_types=1);

namespace Common;

use Common\Exception\LogicException;
use Phalcon\Text as PhalconText;

class Text extends PhalconText
{
    private const VAR_NAME_LIMIT = 256;

    private const STRING_TYPE_TEXT = 'text'; // Any king of long text
    private const STRING_TYPE_RAW = 'raw';   // Short test text

    private const STRING_TYPE_PASCAL_CASE = 'pascal_case';  // TestTextOfFiveWords
    private const STRING_TYPE_CAMEL_CASE = 'camel_case';    // testTextOfFiveWords
    private const STRING_TYPE_SNAKE_CASE = 'snake_case';    // test_text_of_five_words
    private const STRING_TYPE_KEBAB_CASE = 'kebab_case';    // test-text-of-five-words

    private const STRING_TYPE_PASCAL_ONE_WORD = 'pascal_one_word'; // Test
    private const STRING_TYPE_MIXED_UPPER_CASE = 'mixed_upper'; // TEST
    private const STRING_TYPE_MIXED_LOWER_CASE = 'mixed_lower'; // test

    private const LETTERS_LATIN_ANALOGUES = [
        // Russian
        'А' => 'A', 'а' => 'a', 'Б' => 'B', 'б' => 'b', 'В' => 'V', 'в' => 'v', 'Г' => 'G', 'г' => 'g',
        'Д' => 'D', 'д' => 'd', 'Е' => 'E', 'е' => 'e', 'Ё' => 'Yo', 'ё' => 'yo', 'Ж' => 'Zh', 'ж' => 'zh',
        'З' => 'Z', 'з' => 'z', 'И' => 'I', 'и' => 'i', 'Й' => 'Y', 'й' => 'y', 'К' => 'K', 'к' => 'k',
        'Л' => 'L', 'л' => 'l', 'М' => 'M', 'м' => 'm', 'Н' => 'N', 'н' => 'n', 'О' => 'O', 'о' => 'o',
        'П' => 'P', 'п' => 'p', 'Р' => 'R', 'р' => 'r', 'С' => 'S', 'с' => 's', 'Т' => 'T', 'т' => 't',
        'У' => 'U', 'у' => 'u', 'Ф' => 'F', 'ф' => 'f', 'Х' => 'H', 'х' => 'h', 'Ц' => 'C', 'ц' => 'c',
        'Ч' => 'Ch', 'ч' => 'ch', 'Ш' => 'Sh', 'ш' => 'sh', 'Щ' => 'Chch', 'щ' => 'chch', 'Ы' => '', 'ы' => '',
        'Ь' => '\'', 'ь' => '\'', 'Э' => 'E', 'э' => 'e', 'Ю' => 'Yu', 'ю' => 'yu', 'Я' => 'Ya', 'я' => 'ya',
        // Lithuanian
        'Ą' => 'A', 'ą' => 'a', 'Č' => 'C', 'č' => 'c', 'Ę' => 'E', 'ę' => 'e', 'Ė' => 'E', 'ė' => 'e', 'Į' => 'I',
        'į' => 'i', 'Š' => 'S', 'š' => 's', 'Ų' => 'U', 'ų' => 'u', 'Ū' => 'U', 'ū' => 'u', 'Ž' => 'Z', 'ž' => 'z',
    ];

    public static function detectStringType(string $string): string
    {
        if (Regex::isValidPattern($string, Regex::VAR_AZ_LOWER)) {
            return self::STRING_TYPE_MIXED_LOWER_CASE;
        }

        if (Regex::isValidPattern($string, Regex::VAR_AZ_UPPER)) {
            return self::STRING_TYPE_MIXED_UPPER_CASE;
        }

        if (Regex::isValidPattern($string, Regex::VAR_AZ_UPPER_FIRST)) {
            return self::STRING_TYPE_PASCAL_ONE_WORD;
        }

        if (Regex::isValidPattern($string, Regex::VAR_PASCAL_CASE)) {
            return self::STRING_TYPE_PASCAL_CASE;
        }

        if (Regex::isValidPattern($string, Regex::VAR_CAMEL_CASE)) {
            return self::STRING_TYPE_CAMEL_CASE;
        }

        if (Regex::isValidPattern($string, Regex::VAR_SNAKE_CASE)) {
            return self::STRING_TYPE_SNAKE_CASE;
        }

        if (Regex::isValidPattern($string, Regex::VAR_KEBAB_CASE)) {
            return self::STRING_TYPE_KEBAB_CASE;
        }

        if (Regex::isValidPattern($string, Regex::VAR_RAW_CASE) && strlen($string) <= self::VAR_NAME_LIMIT) {
            return self::STRING_TYPE_RAW;
        }

        return self::STRING_TYPE_TEXT;
    }

    public static function toPascalCase(string $value): string
    {
        if (empty(trim($value))) {
            throw new LogicException('Can\'t convert empty string.');
        }

        $stringType = self::detectStringType($value);

        if ($stringType === self::STRING_TYPE_PASCAL_CASE || $stringType === self::STRING_TYPE_PASCAL_ONE_WORD) {
            return $value;
        }

        if ($stringType === self::STRING_TYPE_MIXED_UPPER_CASE || $stringType === self::STRING_TYPE_MIXED_LOWER_CASE) {
            return self::upperFirst(self::lower($value));
        }

        if ($stringType === self::STRING_TYPE_CAMEL_CASE) {
            return self::upperFirst($value);
        }

        if ($stringType === self::STRING_TYPE_SNAKE_CASE || $stringType === self::STRING_TYPE_KEBAB_CASE) {
            return self::convertSeparatorsToUppercase(
                $value,
                ($stringType === self::STRING_TYPE_SNAKE_CASE) ?'_' :'-'
            );
        }

        $value = self::convertSeparatorsToUppercase(
            self::convertRawAndTextToKebabCase($value, $stringType)
        );

        if (empty(trim($value))) {
            throw new LogicException('No valid characters found.');
        }

        return $value;
    }

    public static function toCamelCase(string $value): string
    {
        return self::lowerFirst(self::toPascalCase($value));
    }

    public static function toKebabCase(string $value, bool $url = false): string
    {
        if (empty(trim($value))) {
            throw new LogicException('Can\'t convert empty string.');
        }

        $stringType = self::detectStringType($value);

        if ($stringType === self::STRING_TYPE_KEBAB_CASE || $stringType === self::STRING_TYPE_MIXED_LOWER_CASE) {
            return $value;
        }

        if ($stringType === self::STRING_TYPE_MIXED_UPPER_CASE || $stringType === self::STRING_TYPE_PASCAL_ONE_WORD) {
            return self::lower($value);
        }

        if ($stringType === self::STRING_TYPE_SNAKE_CASE) {
            return str_replace('_', '-', $value);
        }

        if ($stringType === self::STRING_TYPE_PASCAL_CASE
            || $stringType === self::STRING_TYPE_PASCAL_ONE_WORD
            || $stringType === self::STRING_TYPE_CAMEL_CASE
        ) {
            $value = preg_replace('/(?<! )[A-Z]/', ' $0', $value);
            $value = trim(self::lower($value));

            return str_replace(['  ', ' ', ''], '-', $value);
        }

        $value = self::convertRawAndTextToKebabCase($value, $stringType, $url);

        if (empty(trim($value))) {
            throw new LogicException('No valid characters found.');
        }

        return $value;
    }

    public static function toSnakeCase(string $value): string
    {
        return str_replace('-', '_', self::toKebabCase($value));
    }

    public static function toText(string $value): string
    {
        return str_replace('-', ' ', self::toKebabCase($value));
    }

    public static function toSentence(string $value): string
    {
        return self::upperFirst(str_replace('-', ' ', self::toKebabCase($value))) .'.';
    }

    public static function toLatin(string $value): string
    {
        foreach (self::LETTERS_LATIN_ANALOGUES as $from => $to) {
            $value = str_replace($from, $to, $value);
        }

        return $value;
    }

    public static function uncamelizeMethod(string $value): string
    {
        if (Regex::isMethodName($value, Regex::METHOD_TYPE_SET_GET)) {
            $value = preg_replace('/^(get|is|set)/', '', $value);
        }

        return self::toSnakeCase($value);
    }

    public static function methodToVariable(string $value): string
    {
        if (Regex::isMethodName($value, Regex::METHOD_TYPE_GET)) {
            $value = preg_replace('/^(get|is|set)/', '', $value);
        }

        return self::lowerFirst($value);
    }

    public static function lowerFirst(string $value): string
    {
        if (empty(trim($value))) {
            return '';
        }

        return self::lower($value[0]) . substr($value, 1);
    }

    public static function upperFirst(string $value): string
    {
        if (empty(trim($value))) {
            return '';
        }

        return self::upper($value[0]) . substr($value, 1);
    }

    /**
     * Rewritten Phalcon\Text::camelize method
     *
     * @param string $value
     * @param string|null $delimiter
     * @return string
     * @throws LogicException
     */
    public static function camelize(string $value, $delimiter = null): string
    {
        return self::toPascalCase($value);
    }

    /**
     * Rewritten Phalcon\Text::uncamelize method
     *
     * @param string $value
     * @param string $delimiter
     * @return string
     * @throws LogicException
     */
    public static function uncamelize(string $value, $delimiter = '_'): string
    {
        if ($delimiter === '-') {
            return self::toKebabCase($value);
        }
        
        return self::toSnakeCase($value);
    }

    /**
     * Rewritten Phalcon\Text::humanize method
     *
     * @param string $value
     * @return string
     */
    public static function humanize(string $value): string
    {
        return self::toText($value);
    }

    private static function convertRawAndTextToKebabCase(
        string $value,
        string $stringType = self::STRING_TYPE_TEXT,
        bool $url = false
    ): string {
        if ($url) {
            $value = self::toLatin($value);
        }

        if ($stringType === self::STRING_TYPE_TEXT) {
            $value = preg_replace('/[^a-zA-Z0-9 _\\-]/', '', $value);
        }

        $value = preg_replace('/(?<! )[A-Z]/', ' $0', $value);
        $value = trim(self::lower($value));
        $value = preg_replace('/[_ \\-]/', '-', $value);
        $value = str_replace(['---', '--'], '-', $value);

        if (strlen($value) > self::VAR_NAME_LIMIT) {
            $newValue = '';

            foreach (explode('-', $value) as $word) {
                if (strlen($newValue) + strlen($word) > self::VAR_NAME_LIMIT) {
                    break;
                }

                $newValue .= ' ' . self::lower($word);
            }

            $value = str_replace('', '-', trim($newValue));
        }

        return $value;
    }

    private static function convertSeparatorsToUppercase(string $value, string $separator = '-'): string
    {
        $newValue = '';

        foreach (explode($separator, $value) as $word) {
            if (strlen($newValue) + strlen($word) > self::VAR_NAME_LIMIT) {
                break;
            }

            $newValue .= self::upperFirst(self::lower($word));
        }

        return $newValue;
    }
}
