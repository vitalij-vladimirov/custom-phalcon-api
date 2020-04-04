<?php
declare(strict_types=1);

namespace Common;

class Console
{
    public const COLOR_DEFAULT = 39;
    public const COLOR_BLACK = 30;
    public const COLOR_RED = 31;
    public const COLOR_GREEN = 32;
    public const COLOR_YELLOW = 33;
    public const COLOR_BLUE = 34;
    public const COLOR_MAGENTA = 35;
    public const COLOR_CYAN = 36;
    public const COLOR_LIGHT_GRAY = 37;
    public const COLOR_DARK_GRAY = 90;
    public const COLOR_LIGHT_RED = 91;
    public const COLOR_LIGHT_GREEN = 92;
    public const COLOR_LIGHT_YELLOW = 93;
    public const COLOR_LIGHT_BLUE = 94;
    public const COLOR_LIGHT_MAGENTA = 95;
    public const COLOR_LIGHT_CYAN = 96;

    public const STYLE_NORMAL = 0;
    public const STYLE_BOLD = 1;
    public const STYLE_DIM = 2;
    public const STYLE_ITALIC = 3;
    public const STYLE_UNDERLINED = 4;
    public const STYLE_BLINK = 5;
    public const STYLE_REVERSE = 7;
    public const STYLE_HIDDEN = 8;

    public static function output(
        string $text,
        int $textColor = self::COLOR_DEFAULT,
        int $bgColor = self::COLOR_DEFAULT,
        array $styles = []
    ): string {
        if (empty(trim($text))) {
            return '';
        }

        if (!Regex::isValidPattern((string)$textColor, '/^[0-9]{1,2}$/')) {
            $textColor = self::COLOR_DEFAULT;
        }

        if (!Regex::isValidPattern((string)$textColor, '/^[0-9]{2}$/')) {
            $bgColor = self::COLOR_DEFAULT;
        }
        $bgColor += 10;

        $styling = $textColor . ';' . $bgColor;

        if (count($styles)) {
            foreach ($styles as $style) {
                if (!Regex::isValidPattern((string)$style, '/^[0-9]$/')) {
                    continue;
                }

                $styling .= ';' . $style;
            }
        }

        return "\e[" . $styling . 'm' . $text . "\e[0m";
    }

    public static function error(string $text, bool $eolInTheEnt = true): string
    {
        return self::output(
            ' ' . $text . ' ',
            self::COLOR_BLACK,
            self::COLOR_RED,
            [
                self::STYLE_BOLD
            ]
        ) . ($eolInTheEnt ? PHP_EOL : '');
    }

    public static function warning(string $text, bool $eolInTheEnt = true): string
    {
        return self::output(
            ' ' . $text . ' ',
            self::COLOR_BLACK,
            self::COLOR_YELLOW,
            [
                self::STYLE_BOLD
            ]
        ) . ($eolInTheEnt ? PHP_EOL : '');
    }

    public static function success(string $text, bool $eolInTheEnt = true): string
    {
        return self::output(
            ' ' . $text . ' ',
            self::COLOR_BLACK,
            self::COLOR_GREEN,
            [
                self::STYLE_BOLD
            ]
        ) . ($eolInTheEnt ? PHP_EOL : '');
    }

    public static function messageHeader(
        string $text,
        bool $eolInTheEnt = false,
        int $textColor = self::COLOR_DEFAULT,
        int $bgColor = self::COLOR_DARK_GRAY,
        array $styles = [self::STYLE_BOLD]
    ): string {
        return self::output(PHP_EOL . ' ' . $text . ' ', $textColor, $bgColor, $styles) .
            ($eolInTheEnt ? PHP_EOL : '');
    }

    public static function message(
        string $text,
        bool $eolInTheEnt = true,
        int $textColor = self::COLOR_DEFAULT,
        int $bgColor = self::COLOR_DARK_GRAY,
        array $styles = []
    ): string {
        $lines = explode("\n", $text);

        $text = '';
        foreach ($lines as $line) {
            $text .= PHP_EOL . ' ' . $line . ' ';
        }

        return self::output($text, $textColor, $bgColor, $styles) . PHP_EOL .
            ($eolInTheEnt ? PHP_EOL : '');
    }
}
