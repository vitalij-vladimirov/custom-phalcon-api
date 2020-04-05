<?php
declare(strict_types=1);

namespace Common\Integrational\Test;

use Common\BaseClasses\BaseTestCase;
use Common\Variable;
use Carbon\Carbon;
use DateTime;
use DateTimeImmutable;
use Dice\Dice;

class VariableTestCase extends BaseTestCase
{
    private const VAR_STRING = 'a';
    private const VAR_INT = 1;
    private const VAR_FLOAT_E = 1e10;
    private const VAR_INT_STR = '1';
    private const VAR_FLOAT_E_STR = '1e10';
    private const VAR_FLOAT = 1.1;
    private const VAR_FLOAT_INT = 1.0;
    private const VAR_FLOAT_STR = '1.1';
    private const VAR_FLOAT_INT_STR = '1.0';
    private const VAR_FLOAT_STR_COMMA = '1,1';
    private const VAR_FLOAT_INT_STR_COMMA = '1,0';
    private const VAR_BOOL_T = true;
    private const VAR_BOOL_F = false;
    private const VAR_STRING_BOOL_T = 'y';
    private const VAR_STRING_BOOL_F = 'n';
    private const VAR_ARRAY = ['t' => 1];
    private const VAR_NULL = null;
    private const VAR_JSON = '{"t":1}';

    public function testWillValidateStrictInteger(): void
    {
        // Strict integer
        self::assertTrue(Variable::isInteger(self::VAR_INT, true));

        // Non-integer
        self::assertFalse(Variable::isInteger(self::VAR_STRING, true));
        self::assertFalse(Variable::isInteger(self::VAR_FLOAT_E, true));
        self::assertFalse(Variable::isInteger(self::VAR_INT_STR, true));
        self::assertFalse(Variable::isInteger(self::VAR_FLOAT_E_STR, true));
        self::assertFalse(Variable::isInteger(self::VAR_FLOAT, true));
        self::assertFalse(Variable::isInteger(self::VAR_FLOAT_INT, true));
        self::assertFalse(Variable::isInteger(self::VAR_FLOAT_STR, true));
        self::assertFalse(Variable::isInteger(self::VAR_FLOAT_INT_STR, true));
        self::assertFalse(Variable::isInteger(self::VAR_FLOAT_STR_COMMA, true));
        self::assertFalse(Variable::isInteger(self::VAR_FLOAT_INT_STR_COMMA, true));
        self::assertFalse(Variable::isInteger(self::VAR_BOOL_T, true));
        self::assertFalse(Variable::isInteger(self::VAR_BOOL_F, true));
        self::assertFalse(Variable::isInteger(self::VAR_STRING_BOOL_T, true));
        self::assertFalse(Variable::isInteger(self::VAR_STRING_BOOL_F, true));
        self::assertFalse(Variable::isInteger(self::VAR_ARRAY, true));
        self::assertFalse(Variable::isInteger(self::VAR_NULL, true));
        self::assertFalse(Variable::isInteger(self::VAR_JSON, true));
        self::assertFalse(Variable::isInteger(Carbon::now(), true));
    }

    public function testWillValidateNonStrictInteger(): void
    {
        // Integer
        self::assertTrue(Variable::isInteger(self::VAR_INT));

        // Non-strict integer
        self::assertTrue(Variable::isInteger(self::VAR_INT_STR));
        self::assertTrue(Variable::isInteger(self::VAR_FLOAT_E));
        self::assertTrue(Variable::isInteger(self::VAR_FLOAT_E_STR));
        self::assertTrue(Variable::isInteger(self::VAR_FLOAT_INT));
        self::assertTrue(Variable::isInteger(self::VAR_FLOAT_INT_STR));
        self::assertTrue(Variable::isInteger(self::VAR_FLOAT_INT_STR_COMMA));

        // Non-integer
        self::assertFalse(Variable::isInteger(self::VAR_STRING));
        self::assertFalse(Variable::isInteger(self::VAR_FLOAT));
        self::assertFalse(Variable::isInteger(self::VAR_FLOAT_STR));
        self::assertFalse(Variable::isInteger(self::VAR_FLOAT_STR_COMMA));
        self::assertFalse(Variable::isInteger(self::VAR_BOOL_T));
        self::assertFalse(Variable::isInteger(self::VAR_BOOL_F));
        self::assertFalse(Variable::isInteger(self::VAR_STRING_BOOL_T));
        self::assertFalse(Variable::isInteger(self::VAR_STRING_BOOL_F));
        self::assertFalse(Variable::isInteger(self::VAR_ARRAY));
        self::assertFalse(Variable::isInteger(self::VAR_NULL));
        self::assertFalse(Variable::isInteger(self::VAR_JSON));
        self::assertFalse(Variable::isInteger(Carbon::now()));
    }

    public function testWillValidateStrictFloat(): void
    {
        // Strict float
        self::assertTrue(Variable::isFloat(self::VAR_FLOAT, true));
        self::assertTrue(Variable::isFloat(self::VAR_FLOAT_E, true));
        self::assertTrue(Variable::isFloat(self::VAR_FLOAT_INT, true));

        // Non-float
        self::assertFalse(Variable::isFloat(self::VAR_INT, true));
        self::assertFalse(Variable::isFloat(self::VAR_STRING, true));
        self::assertFalse(Variable::isFloat(self::VAR_INT_STR, true));
        self::assertFalse(Variable::isFloat(self::VAR_FLOAT_E_STR, true));
        self::assertFalse(Variable::isFloat(self::VAR_FLOAT_STR, true));
        self::assertFalse(Variable::isFloat(self::VAR_FLOAT_INT_STR, true));
        self::assertFalse(Variable::isFloat(self::VAR_FLOAT_STR_COMMA, true));
        self::assertFalse(Variable::isFloat(self::VAR_FLOAT_INT_STR_COMMA, true));
        self::assertFalse(Variable::isFloat(self::VAR_BOOL_T, true));
        self::assertFalse(Variable::isFloat(self::VAR_BOOL_F, true));
        self::assertFalse(Variable::isFloat(self::VAR_STRING_BOOL_T, true));
        self::assertFalse(Variable::isFloat(self::VAR_STRING_BOOL_F, true));
        self::assertFalse(Variable::isFloat(self::VAR_ARRAY, true));
        self::assertFalse(Variable::isFloat(self::VAR_NULL, true));
        self::assertFalse(Variable::isFloat(self::VAR_JSON, true));
        self::assertFalse(Variable::isFloat(Carbon::now(), true));
    }

    public function testWillValidateNonStrictFloat(): void
    {
        // Non-strict float
        self::assertTrue(Variable::isFloat(self::VAR_FLOAT));
        self::assertTrue(Variable::isFloat(self::VAR_FLOAT_STR));
        self::assertTrue(Variable::isFloat(self::VAR_FLOAT_STR_COMMA));

        // Non-strict non-float
        self::assertFalse(Variable::isFloat(self::VAR_FLOAT_E_STR));
        self::assertFalse(Variable::isFloat(self::VAR_FLOAT_E));
        self::assertFalse(Variable::isFloat(self::VAR_FLOAT_INT));

        // Non-float
        self::assertFalse(Variable::isFloat(self::VAR_INT));
        self::assertFalse(Variable::isFloat(self::VAR_STRING));
        self::assertFalse(Variable::isFloat(self::VAR_INT_STR));
        self::assertFalse(Variable::isFloat(self::VAR_FLOAT_INT_STR));
        self::assertFalse(Variable::isFloat(self::VAR_FLOAT_INT_STR_COMMA));
        self::assertFalse(Variable::isFloat(self::VAR_BOOL_T));
        self::assertFalse(Variable::isFloat(self::VAR_BOOL_F));
        self::assertFalse(Variable::isFloat(self::VAR_STRING_BOOL_T));
        self::assertFalse(Variable::isFloat(self::VAR_STRING_BOOL_F));
        self::assertFalse(Variable::isFloat(self::VAR_ARRAY));
        self::assertFalse(Variable::isFloat(self::VAR_NULL));
        self::assertFalse(Variable::isFloat(self::VAR_JSON));
        self::assertFalse(Variable::isFloat(Carbon::now()));
    }

    public function testWillValidateStrictString(): void
    {
        // Strict string
        self::assertTrue(Variable::isString(self::VAR_STRING, true));
        self::assertTrue(Variable::isString(self::VAR_INT_STR, true));
        self::assertTrue(Variable::isString(self::VAR_FLOAT_E_STR, true));
        self::assertTrue(Variable::isString(self::VAR_FLOAT_STR, true));
        self::assertTrue(Variable::isString(self::VAR_FLOAT_INT_STR, true));
        self::assertTrue(Variable::isString(self::VAR_FLOAT_STR_COMMA, true));
        self::assertTrue(Variable::isString(self::VAR_FLOAT_INT_STR_COMMA, true));
        self::assertTrue(Variable::isString(self::VAR_JSON, true));
        self::assertTrue(Variable::isString(self::VAR_STRING_BOOL_T, true));
        self::assertTrue(Variable::isString(self::VAR_STRING_BOOL_F, true));

        // Non-string
        self::assertFalse(Variable::isString(self::VAR_FLOAT, true));
        self::assertFalse(Variable::isString(self::VAR_FLOAT_E, true));
        self::assertFalse(Variable::isString(self::VAR_FLOAT_INT, true));
        self::assertFalse(Variable::isString(self::VAR_INT, true));
        self::assertFalse(Variable::isString(self::VAR_BOOL_T, true));
        self::assertFalse(Variable::isString(self::VAR_BOOL_F, true));
        self::assertFalse(Variable::isString(self::VAR_ARRAY, true));
        self::assertFalse(Variable::isString(self::VAR_NULL, true));
        self::assertFalse(Variable::isString(Carbon::now(), true));
    }

    public function testWillValidateNonStrictString(): void
    {
        // Non-strict string
        self::assertTrue(Variable::isString(self::VAR_STRING));

        // Non-strict non-string
        self::assertFalse(Variable::isString(self::VAR_INT_STR));
        self::assertFalse(Variable::isString(self::VAR_FLOAT_E_STR));
        self::assertFalse(Variable::isString(self::VAR_FLOAT_STR));
        self::assertFalse(Variable::isString(self::VAR_FLOAT_INT_STR));
        self::assertFalse(Variable::isString(self::VAR_FLOAT_STR_COMMA));
        self::assertFalse(Variable::isString(self::VAR_FLOAT_INT_STR_COMMA));
        self::assertFalse(Variable::isString(self::VAR_JSON));
        self::assertFalse(Variable::isString(self::VAR_STRING_BOOL_T));
        self::assertFalse(Variable::isString(self::VAR_STRING_BOOL_F));

        // Non-string
        self::assertFalse(Variable::isString(self::VAR_FLOAT, true));
        self::assertFalse(Variable::isString(self::VAR_FLOAT_E, true));
        self::assertFalse(Variable::isString(self::VAR_FLOAT_INT, true));
        self::assertFalse(Variable::isString(self::VAR_INT, true));
        self::assertFalse(Variable::isString(self::VAR_BOOL_T, true));
        self::assertFalse(Variable::isString(self::VAR_BOOL_F, true));
        self::assertFalse(Variable::isString(self::VAR_ARRAY, true));
        self::assertFalse(Variable::isString(self::VAR_NULL, true));
        self::assertFalse(Variable::isString(Carbon::now(), true));
    }

    public function testWillValidateStrictBool(): void
    {
        // Strict boolean
        self::assertTrue(Variable::isBool(self::VAR_BOOL_T, true));
        self::assertTrue(Variable::isBool(self::VAR_BOOL_F, true));
        
        // Non-boolean
        self::assertFalse(Variable::isBool(self::VAR_STRING_BOOL_T, true));
        self::assertFalse(Variable::isBool(self::VAR_STRING_BOOL_F, true));
        self::assertFalse(Variable::isBool(self::VAR_STRING, true));
        self::assertFalse(Variable::isBool(self::VAR_INT_STR, true));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_E_STR, true));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_STR, true));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_INT_STR, true));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_STR_COMMA, true));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_INT_STR_COMMA, true));
        self::assertFalse(Variable::isBool(self::VAR_JSON, true));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT, true));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_E, true));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_INT, true));
        self::assertFalse(Variable::isBool(self::VAR_INT, true));
        self::assertFalse(Variable::isBool(self::VAR_ARRAY, true));
        self::assertFalse(Variable::isBool(self::VAR_NULL, true));
        self::assertFalse(Variable::isBool(Carbon::now(), true));
    }

    public function testWillValidateNonStrictBool(): void
    {
        // Boolean
        self::assertTrue(Variable::isBool(self::VAR_BOOL_T, false));
        self::assertTrue(Variable::isBool(self::VAR_BOOL_F, false));

        // Non-strict boolean
        self::assertTrue(Variable::isBool(self::VAR_INT_STR, false));
        self::assertTrue(Variable::isBool(self::VAR_INT, false));
        self::assertTrue(Variable::isBool(0, false));
        self::assertTrue(Variable::isBool('0', false));
        self::assertTrue(Variable::isBool(self::VAR_STRING_BOOL_T, false));
        self::assertTrue(Variable::isBool(self::VAR_STRING_BOOL_F, false));

        // Non-boolean
        self::assertFalse(Variable::isBool(self::VAR_STRING, false));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_E_STR, false));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_STR, false));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_INT_STR, false));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_STR_COMMA, false));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_INT_STR_COMMA, false));
        self::assertFalse(Variable::isBool(self::VAR_JSON, false));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT, false));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_E, false));
        self::assertFalse(Variable::isBool(self::VAR_FLOAT_INT, false));
        self::assertFalse(Variable::isBool(self::VAR_ARRAY, false));
        self::assertFalse(Variable::isBool(self::VAR_NULL, false));
        self::assertFalse(Variable::isBool(Carbon::now(), false));
    }

    public function testWillValidateArray(): void
    {
        // Array
        self::assertTrue(Variable::isArray(self::VAR_ARRAY));

        // Non-array
        self::assertFalse(Variable::isArray(self::VAR_BOOL_T));
        self::assertFalse(Variable::isArray(self::VAR_BOOL_F));
        self::assertFalse(Variable::isArray(self::VAR_STRING_BOOL_T));
        self::assertFalse(Variable::isArray(self::VAR_STRING_BOOL_F));
        self::assertFalse(Variable::isArray(self::VAR_INT_STR));
        self::assertFalse(Variable::isArray(self::VAR_INT));
        self::assertFalse(Variable::isArray(self::VAR_STRING));
        self::assertFalse(Variable::isArray(self::VAR_FLOAT_E_STR));
        self::assertFalse(Variable::isArray(self::VAR_FLOAT_STR));
        self::assertFalse(Variable::isArray(self::VAR_FLOAT_INT_STR));
        self::assertFalse(Variable::isArray(self::VAR_FLOAT_STR_COMMA));
        self::assertFalse(Variable::isArray(self::VAR_FLOAT_INT_STR_COMMA));
        self::assertFalse(Variable::isArray(self::VAR_JSON));
        self::assertFalse(Variable::isArray(self::VAR_FLOAT));
        self::assertFalse(Variable::isArray(self::VAR_FLOAT_E));
        self::assertFalse(Variable::isArray(self::VAR_FLOAT_INT));
        self::assertFalse(Variable::isArray(self::VAR_NULL));
        self::assertFalse(Variable::isArray(Carbon::now()));
    }

    public function testWillValidateObject(): void
    {
        // Object without instance
        self::assertTrue(Variable::isObject(Carbon::now()));

        // Non-object
        self::assertFalse(Variable::isObject(self::VAR_BOOL_T));
        self::assertFalse(Variable::isObject(self::VAR_BOOL_F));
        self::assertFalse(Variable::isObject(self::VAR_STRING_BOOL_T));
        self::assertFalse(Variable::isObject(self::VAR_STRING_BOOL_F));
        self::assertFalse(Variable::isObject(self::VAR_INT_STR));
        self::assertFalse(Variable::isObject(self::VAR_INT));
        self::assertFalse(Variable::isObject(self::VAR_STRING));
        self::assertFalse(Variable::isObject(self::VAR_FLOAT_E_STR));
        self::assertFalse(Variable::isObject(self::VAR_FLOAT_STR));
        self::assertFalse(Variable::isObject(self::VAR_FLOAT_INT_STR));
        self::assertFalse(Variable::isObject(self::VAR_FLOAT_STR_COMMA));
        self::assertFalse(Variable::isObject(self::VAR_FLOAT_INT_STR_COMMA));
        self::assertFalse(Variable::isObject(self::VAR_JSON));
        self::assertFalse(Variable::isObject(self::VAR_FLOAT));
        self::assertFalse(Variable::isObject(self::VAR_FLOAT_E));
        self::assertFalse(Variable::isObject(self::VAR_FLOAT_INT));
        self::assertFalse(Variable::isObject(self::VAR_NULL));
        self::assertFalse(Variable::isObject(self::VAR_ARRAY));
    }

    public function testWillValidateObjectInstance(): void
    {
        // Object with correct instance
        self::assertTrue(Variable::isObject(Carbon::now(), new Carbon()));
        self::assertTrue(Variable::isObject(Carbon::now(), Carbon::class));

        // Object with incorrect instance
        self::assertFalse(Variable::isObject(Carbon::now(), DateTimeImmutable::class));
        self::assertFalse(Variable::isObject(Carbon::now(), new DateTimeImmutable()));

        // Non object
        self::assertFalse(Variable::isObject(Carbon::now(), 'test'));
        self::assertFalse(Variable::isObject(self::VAR_STRING, Carbon::class));
        self::assertFalse(Variable::isObject(self::VAR_ARRAY, 'test'));
    }

    public function testWillValidateDateTimeObject(): void
    {
        // DateTime object
        self::assertTrue(Variable::isDateTimeObject(Carbon::now()));
        self::assertTrue(Variable::isDateTimeObject(new DateTime()));
        self::assertTrue(Variable::isDateTimeObject(new DateTimeImmutable()));

        // Not DateTime object
        self::assertFalse(Variable::isDateTimeObject(new Dice()));
        self::assertFalse(Variable::isDateTimeObject(self::VAR_INT));
        self::assertFalse(Variable::isDateTimeObject(self::VAR_STRING));
        self::assertFalse(Variable::isDateTimeObject(self::VAR_ARRAY));
    }

    public function testWillValidateStrictTypeDetection(): void
    {
        self::assertEquals('bool', Variable::getType(self::VAR_BOOL_T));
        self::assertEquals('bool', Variable::getType(self::VAR_BOOL_F));

        self::assertEquals('bool', Variable::getType(self::VAR_STRING_BOOL_T));
        self::assertEquals('bool', Variable::getType(self::VAR_STRING_BOOL_F));

        self::assertNotEquals('bool', Variable::getType(1));
        self::assertNotEquals('bool', Variable::getType(0));
        self::assertNotEquals('bool', Variable::getType(1.0));

        self::assertEquals('object', Variable::getType(Carbon::now(), true));
        self::assertEquals('string', Variable::getType(self::VAR_INT_STR, true));
        self::assertEquals('string', Variable::getType(self::VAR_STRING, true));
        self::assertEquals('string', Variable::getType(self::VAR_FLOAT_E_STR, true));
        self::assertEquals('string', Variable::getType(self::VAR_FLOAT_STR, true));
        self::assertEquals('string', Variable::getType(self::VAR_FLOAT_INT_STR, true));
        self::assertEquals('string', Variable::getType(self::VAR_FLOAT_STR_COMMA, true));
        self::assertEquals('string', Variable::getType(self::VAR_FLOAT_INT_STR_COMMA, true));
        self::assertEquals('string', Variable::getType(self::VAR_JSON, true));
        self::assertEquals('int', Variable::getType(self::VAR_INT, true));
        self::assertEquals('float', Variable::getType(self::VAR_FLOAT, true));
        self::assertEquals('float', Variable::getType(self::VAR_FLOAT_E, true));
        self::assertEquals('float', Variable::getType(self::VAR_FLOAT_INT, true));
        self::assertEquals('null', Variable::getType(self::VAR_NULL, true));
        self::assertEquals('array', Variable::getType(self::VAR_ARRAY, true));
    }

    public function testWillValidateNonStrictTypeDetection(): void
    {
        self::assertEquals('bool', Variable::getType(self::VAR_BOOL_T, false));
        self::assertEquals('bool', Variable::getType(self::VAR_BOOL_F, false));

        self::assertEquals('bool', Variable::getType(self::VAR_STRING_BOOL_T, false));
        self::assertEquals('bool', Variable::getType(self::VAR_STRING_BOOL_F, false));

        self::assertEquals('int', Variable::getType(1, false));
        self::assertEquals('int', Variable::getType(0, false));
        self::assertEquals('int', Variable::getType(1.0, false));

        self::assertEquals('string', Variable::getType(self::VAR_STRING));
        self::assertEquals('json', Variable::getType(self::VAR_JSON));
        self::assertEquals('object', Variable::getType(Carbon::now()));
        self::assertEquals('int', Variable::getType(self::VAR_INT_STR));
        self::assertEquals('int', Variable::getType(self::VAR_INT));
        self::assertEquals('int', Variable::getType(self::VAR_FLOAT_E_STR));
        self::assertEquals('int', Variable::getType(self::VAR_FLOAT_INT_STR));
        self::assertEquals('int', Variable::getType(self::VAR_FLOAT_INT_STR_COMMA));
        self::assertEquals('int', Variable::getType(self::VAR_FLOAT_E));
        self::assertEquals('int', Variable::getType(self::VAR_FLOAT_INT));
        self::assertEquals('float', Variable::getType(self::VAR_FLOAT));
        self::assertEquals('float', Variable::getType(self::VAR_FLOAT_STR));
        self::assertEquals('float', Variable::getType(self::VAR_FLOAT_STR_COMMA));
        self::assertEquals('null', Variable::getType(self::VAR_NULL));
        self::assertEquals('array', Variable::getType(self::VAR_ARRAY));
    }

    public function testWillValidateIfVariableTypeIsDefault(): void
    {
        self::assertTrue(Variable::isDefaultType(Variable::getType(self::VAR_STRING)));
        self::assertTrue(Variable::isDefaultType(Variable::getType(self::VAR_INT)));
        self::assertTrue(Variable::isDefaultType(Variable::getType(self::VAR_FLOAT)));
        self::assertTrue(Variable::isDefaultType(Variable::getType(self::VAR_BOOL_T)));
        self::assertTrue(Variable::isDefaultType(Variable::getType(self::VAR_ARRAY)));
        self::assertTrue(Variable::isDefaultType(Variable::getType(Carbon::now())));
        self::assertTrue(Variable::isDefaultType(Variable::getType(self::VAR_NULL)));

        self::assertFalse(Variable::isDefaultType(Variable::getType(self::VAR_JSON)));
    }

    public function testWillValidateDateTimeObjectConverting(): void
    {
        $date = '2020-04-02';
        $dateTime = '2020-04-02 01:40:30';
        $timestamp = 1585791630;

        $carbonDateTime = Carbon::createFromTimeString($dateTime);
        $dateTimeObject = new DateTime($dateTime);
        $dateTimeImmutable = new DateTimeImmutable($dateTime);

        self::assertEquals($timestamp, Variable::convertTimeObjectToString($carbonDateTime));
        self::assertEquals($timestamp, Variable::convertTimeObjectToString($dateTimeObject));
        self::assertEquals($timestamp, Variable::convertTimeObjectToString($dateTimeImmutable));

        self::assertEquals($dateTime, Variable::convertTimeObjectToString($carbonDateTime, false));
        self::assertEquals($dateTime, Variable::convertTimeObjectToString($dateTimeObject, false));
        self::assertEquals($dateTime, Variable::convertTimeObjectToString($dateTimeImmutable, false));

        self::assertEquals($date, Variable::convertTimeObjectToString($carbonDateTime, false, 'Y-m-d'));
        self::assertEquals($date, Variable::convertTimeObjectToString($dateTimeObject, false, 'Y-m-d'));
        self::assertEquals($date, Variable::convertTimeObjectToString($dateTimeImmutable, false, 'Y-m-d'));
    }

    public function testWillRestoreArrayTypes(): void
    {
        $carbonObject = Carbon::now();

        $array = [
            'string' => 'some text ',
            'integer' => 10,
            'integer_true' => 1,
            'integer_false' => 0,
            'float' => 1.1,
            'float_int' => 1.0,
            'float_string' => '1,2',
            'float_str_int' => '1,0',
            'string_int' => '11',
            'null' => null,
            'empty' => '',
            'empty_spaced' => ' ',
            'bool_true' => true,
            'bool_false' => false,
            'bool_string_true' => 'true',
            'bool_string_int_false' => '0',
            'array' => [
                'object' => $carbonObject,
                'json' => '{"t1":"text","t2":"12"}',
            ],
        ];

        self::assertEquals(
            Variable::restoreArrayTypes($array),
            [
                'string' => 'some text',
                'integer' => 10,
                'integer_true' => 1,
                'integer_false' => 0,
                'float' => 1.1,
                'float_int' => 1,
                'float_string' => 1.2,
                'float_str_int' => 1,
                'string_int' => 11,
                'null' => null,
                'empty' => null,
                'empty_spaced' => null,
                'bool_true' => true,
                'bool_false' => false,
                'bool_string_true' => 'true',
                'bool_string_int_false' => 0,
                'array' => [
                    'object' => $carbonObject,
                    'json' => [
                        't1' => 'text',
                        't2' => 12,
                    ],
                ],
            ]
        );

        self::assertEquals(
            Variable::restoreArrayTypes($array, true, true, true),
            [
                'string' => 'some text',
                'integer' => 10,
                'integer_true' => true,
                'integer_false' => false,
                'float' => 1.1,
                'float_int' => 1,
                'float_string' => 1.2,
                'float_str_int' => 1,
                'string_int' => 11,
                'null' => null,
                'empty' => null,
                'empty_spaced' => null,
                'bool_true' => true,
                'bool_false' => false,
                'bool_string_true' => true,
                'bool_string_int_false' => false,
                'array' => [
                    'object' => $carbonObject,
                    'json' => [
                        't1' => 'text',
                        't2' => 12,
                    ],
                ],
            ]
        );

        $nonTrimRestore = Variable::restoreArrayTypes($array, false);
        self::assertEquals(null, $nonTrimRestore['empty']);
        self::assertEquals(' ', $nonTrimRestore['empty_spaced']);

        $nonTrimNonNullRestore = Variable::restoreArrayTypes($array, false, false);
        self::assertEquals('', $nonTrimNonNullRestore['empty']);
        self::assertEquals(' ', $nonTrimNonNullRestore['empty_spaced']);

        $trimNonNullRestore = Variable::restoreArrayTypes($array, true, false);
        self::assertEquals('', $trimNonNullRestore['empty']);
        self::assertEquals('', $trimNonNullRestore['empty_spaced']);
    }
}
