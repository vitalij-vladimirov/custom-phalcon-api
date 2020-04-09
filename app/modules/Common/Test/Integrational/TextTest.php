<?php
declare(strict_types=1);

namespace Common\Integrational\Test;

use Common\BaseClasses\BaseTestCase;
use Common\Exception\LogicException;
use Common\Text;

class TextTest extends BaseTestCase
{
    private const STRING_ONE_WORD_LOWER = 'test';
    private const STRING_ONE_WORD_UPPER = 'TEST';
    private const STRING_ONE_WORD_PASCAL = 'Test';
    private const STRING_TEXT = 'Test text. Of Five  words.';
    private const STRING_RAW_CASE = 'Test text of five words';
    private const STRING_PASCAL_CASE = 'TestTextOfFiveWords';
    private const STRING_CAMEL_CASE = 'testTextOfFiveWords';
    private const STRING_SNAKE_CASE = 'test_text_of_five_words';
    private const STRING_KEBAB_CASE = 'test-text-of-five-words';

    private const LONG_STRING = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin venenatis ut arcu'
        . ' id vestibulum. Vivamus ac faucibus purus. Etiam elit felis, pretium vitae quam eu, euismod posuere mi.'
        . ' Mauris sit amet auctor leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere'
        . ' cubilia Curae; Aliquam lobortis lobortis hendrerit.';

    private const SHORTENED_RAW = 'lorem ipsum dolor sit amet consectetur adipiscing elit proin venenatis ut arcu'
        . ' id vestibulum vivamus ac faucibus purus etiam elit felis pretium vitae quam eu euismod posuere mi mauris'
        . ' sit amet auctor leo vestibulum ante ipsum primis in faucibus orci luctus';

    private const RUSSIAN_TEXT = 'Лорем ипсум долор сит амет.';

    private const RUSSIAN_TEXT_KEBAB_CASE = 'lorem-ipsum-dolor-sit-amet';

    private const LITHUANIAN_TEXT = 'Bandomųjų lietuviškų žodžių aprėptis čiaudint į nosinę jokiu būdu ne ant Ąžuolo.'
        . 'ĄČĘĖĮŠŲŪŽ ąčęėįšųūž!';

    private const LITHUANIAN_TEXT_KEBAB_CASE = 'bandomuju-lietuvisku-zodziu-apreptis-ciaudint-i-nosine-jokiu-budu-ne'
        . '-ant-azuolo-a-c-e-e-i-s-u-u-z-aceeisuuz';

    public function testWillDetectStringCases(): void
    {
        self::assertEquals('mixed_lower', Text::detectStringType(self::STRING_ONE_WORD_LOWER));
        self::assertEquals('mixed_upper', Text::detectStringType(self::STRING_ONE_WORD_UPPER));
        self::assertEquals('pascal_one_word', Text::detectStringType(self::STRING_ONE_WORD_PASCAL));
        self::assertEquals('pascal_case', Text::detectStringType(self::STRING_PASCAL_CASE));
        self::assertEquals('camel_case', Text::detectStringType(self::STRING_CAMEL_CASE));
        self::assertEquals('snake_case', Text::detectStringType(self::STRING_SNAKE_CASE));
        self::assertEquals('kebab_case', Text::detectStringType(self::STRING_KEBAB_CASE));
        self::assertEquals('raw', Text::detectStringType(self::STRING_RAW_CASE));
        self::assertEquals('text', Text::detectStringType(self::STRING_TEXT));
    }

    public function testWillConvertStringToPascalCase(): void
    {
        self::assertEquals(self::STRING_ONE_WORD_PASCAL, Text::toPascalCase(self::STRING_ONE_WORD_UPPER));
        self::assertEquals(self::STRING_ONE_WORD_PASCAL, Text::toPascalCase(self::STRING_ONE_WORD_PASCAL));

        self::assertEquals(self::STRING_PASCAL_CASE, Text::toPascalCase(self::STRING_TEXT));
        self::assertEquals(self::STRING_PASCAL_CASE, Text::toPascalCase(self::STRING_RAW_CASE));
        self::assertEquals(self::STRING_PASCAL_CASE, Text::toPascalCase(self::STRING_CAMEL_CASE));
        self::assertEquals(self::STRING_PASCAL_CASE, Text::toPascalCase(self::STRING_SNAKE_CASE));
        self::assertEquals(self::STRING_PASCAL_CASE, Text::toPascalCase(self::STRING_KEBAB_CASE));
    }

    public function testWillConvertStringToCamelCase(): void
    {
        self::assertEquals(self::STRING_ONE_WORD_LOWER, Text::toCamelCase(self::STRING_ONE_WORD_UPPER));
        self::assertEquals(self::STRING_ONE_WORD_LOWER, Text::toCamelCase(self::STRING_ONE_WORD_PASCAL));

        self::assertEquals(self::STRING_CAMEL_CASE, Text::toCamelCase(self::STRING_TEXT));
        self::assertEquals(self::STRING_CAMEL_CASE, Text::toCamelCase(self::STRING_RAW_CASE));
        self::assertEquals(self::STRING_CAMEL_CASE, Text::toCamelCase(self::STRING_PASCAL_CASE));
        self::assertEquals(self::STRING_CAMEL_CASE, Text::toCamelCase(self::STRING_SNAKE_CASE));
        self::assertEquals(self::STRING_CAMEL_CASE, Text::toCamelCase(self::STRING_KEBAB_CASE));
    }

    public function testWillConvertStringToKebabCase(): void
    {
        self::assertEquals(self::STRING_ONE_WORD_LOWER, Text::toKebabCase(self::STRING_ONE_WORD_UPPER));
        self::assertEquals(self::STRING_ONE_WORD_LOWER, Text::toKebabCase(self::STRING_ONE_WORD_PASCAL));

        self::assertEquals(self::STRING_KEBAB_CASE, Text::toKebabCase(self::STRING_TEXT));
        self::assertEquals(self::STRING_KEBAB_CASE, Text::toKebabCase(self::STRING_RAW_CASE));
        self::assertEquals(self::STRING_KEBAB_CASE, Text::toKebabCase(self::STRING_PASCAL_CASE));
        self::assertEquals(self::STRING_KEBAB_CASE, Text::toKebabCase(self::STRING_SNAKE_CASE));
        self::assertEquals(self::STRING_KEBAB_CASE, Text::toKebabCase(self::STRING_CAMEL_CASE));
    }

    public function testWillConvertStringToSnakeCase(): void
    {
        self::assertEquals(self::STRING_ONE_WORD_LOWER, Text::toSnakeCase(self::STRING_ONE_WORD_UPPER));
        self::assertEquals(self::STRING_ONE_WORD_LOWER, Text::toSnakeCase(self::STRING_ONE_WORD_PASCAL));

        self::assertEquals(self::STRING_SNAKE_CASE, Text::toSnakeCase(self::STRING_TEXT));
        self::assertEquals(self::STRING_SNAKE_CASE, Text::toSnakeCase(self::STRING_RAW_CASE));
        self::assertEquals(self::STRING_SNAKE_CASE, Text::toSnakeCase(self::STRING_PASCAL_CASE));
        self::assertEquals(self::STRING_SNAKE_CASE, Text::toSnakeCase(self::STRING_KEBAB_CASE));
        self::assertEquals(self::STRING_SNAKE_CASE, Text::toSnakeCase(self::STRING_CAMEL_CASE));
    }

    public function testWillConvertStringToText(): void
    {
        $textExample = strtolower(self::STRING_RAW_CASE);
        
        self::assertEquals(self::STRING_ONE_WORD_LOWER, Text::toText(self::STRING_ONE_WORD_UPPER));
        self::assertEquals(self::STRING_ONE_WORD_LOWER, Text::toText(self::STRING_ONE_WORD_PASCAL));

        self::assertEquals($textExample, Text::toText(self::STRING_TEXT));
        self::assertEquals($textExample, Text::toText(self::STRING_RAW_CASE));
        self::assertEquals($textExample, Text::toText(self::STRING_PASCAL_CASE));
        self::assertEquals($textExample, Text::toText(self::STRING_KEBAB_CASE));
        self::assertEquals($textExample, Text::toText(self::STRING_CAMEL_CASE));
    }

    public function testWillConvertStringToSentence(): void
    {
        $oneWordExample = self::STRING_ONE_WORD_PASCAL . '.';
        $textExample = ucfirst(strtolower(self::STRING_RAW_CASE)) . '.';

        self::assertEquals($oneWordExample, Text::toSentence(self::STRING_ONE_WORD_UPPER));
        self::assertEquals($oneWordExample, Text::toSentence(self::STRING_ONE_WORD_PASCAL));

        self::assertEquals($textExample, Text::toSentence(self::STRING_TEXT));
        self::assertEquals($textExample, Text::toSentence(self::STRING_RAW_CASE));
        self::assertEquals($textExample, Text::toSentence(self::STRING_PASCAL_CASE));
        self::assertEquals($textExample, Text::toSentence(self::STRING_KEBAB_CASE));
        self::assertEquals($textExample, Text::toSentence(self::STRING_CAMEL_CASE));
    }

    public function testWillTryToConvertEmptyStringAndThrowException(): void
    {
        $this->expectException(LogicException::class);
        Text::toText('  ');
    }

    public function testWillTryToConvertStringWithBadCharactersAndThrowException(): void
    {
        $this->expectException(LogicException::class);
        Text::toText('"!\/"');
    }

    public function testWillCutTextTo256Symbols(): void
    {
        self::assertEquals(self::SHORTENED_RAW, Text::toText(self::LONG_STRING));
    }

    public function testWillConvertNonLatinLettersToLatinUrl(): void
    {
        self::assertEquals(self::RUSSIAN_TEXT_KEBAB_CASE, Text::toKebabCase(self::RUSSIAN_TEXT, true));
        self::assertEquals(self::LITHUANIAN_TEXT_KEBAB_CASE, Text::toKebabCase(self::LITHUANIAN_TEXT, true));
    }
}
