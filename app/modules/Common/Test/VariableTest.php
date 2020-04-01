<?php
declare(strict_types=1);

namespace Common\Test;

use Common\BaseClasses\BaseTest;
use Common\Variable;
use Carbon\Carbon;
use DateTime;
use DateTimeImmutable;
use Dice\Dice;

class VariableTest extends BaseTest
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
    private const VAR_ARRAY = ['t' => 1];
    private const VAR_NULL = null;
    private const VAR_JSON = '{"t":1}';

    public function testWillValidateStrictInteger(): void
    {
        // Strict integer
        $this->assertTrue(Variable::isInteger(self::VAR_INT, true));

        // Non-integer
        $this->assertFalse(Variable::isInteger(self::VAR_STRING, true));
        $this->assertFalse(Variable::isInteger(self::VAR_FLOAT_E, true));
        $this->assertFalse(Variable::isInteger(self::VAR_INT_STR, true));
        $this->assertFalse(Variable::isInteger(self::VAR_FLOAT_E_STR, true));
        $this->assertFalse(Variable::isInteger(self::VAR_FLOAT, true));
        $this->assertFalse(Variable::isInteger(self::VAR_FLOAT_INT, true));
        $this->assertFalse(Variable::isInteger(self::VAR_FLOAT_STR, true));
        $this->assertFalse(Variable::isInteger(self::VAR_FLOAT_INT_STR, true));
        $this->assertFalse(Variable::isInteger(self::VAR_FLOAT_STR_COMMA, true));
        $this->assertFalse(Variable::isInteger(self::VAR_FLOAT_INT_STR_COMMA, true));
        $this->assertFalse(Variable::isInteger(self::VAR_BOOL_T, true));
        $this->assertFalse(Variable::isInteger(self::VAR_BOOL_F, true));
        $this->assertFalse(Variable::isInteger(self::VAR_ARRAY, true));
        $this->assertFalse(Variable::isInteger(self::VAR_NULL, true));
        $this->assertFalse(Variable::isInteger(self::VAR_JSON, true));
        $this->assertFalse(Variable::isInteger(Carbon::now(), true));
    }

    public function testWillValidateNonStrictInteger(): void
    {
        // Integer
        $this->assertTrue(Variable::isInteger(self::VAR_INT));

        // Non-strict integer
        $this->assertTrue(Variable::isInteger(self::VAR_INT_STR));
        $this->assertTrue(Variable::isInteger(self::VAR_FLOAT_E));
        $this->assertTrue(Variable::isInteger(self::VAR_FLOAT_E_STR));
        $this->assertTrue(Variable::isInteger(self::VAR_FLOAT_INT));
        $this->assertTrue(Variable::isInteger(self::VAR_FLOAT_INT_STR));
        $this->assertTrue(Variable::isInteger(self::VAR_FLOAT_INT_STR_COMMA));

        // Non-integer
        $this->assertFalse(Variable::isInteger(self::VAR_STRING));
        $this->assertFalse(Variable::isInteger(self::VAR_FLOAT));
        $this->assertFalse(Variable::isInteger(self::VAR_FLOAT_STR));
        $this->assertFalse(Variable::isInteger(self::VAR_FLOAT_STR_COMMA));
        $this->assertFalse(Variable::isInteger(self::VAR_BOOL_T));
        $this->assertFalse(Variable::isInteger(self::VAR_BOOL_F));
        $this->assertFalse(Variable::isInteger(self::VAR_ARRAY));
        $this->assertFalse(Variable::isInteger(self::VAR_NULL));
        $this->assertFalse(Variable::isInteger(self::VAR_JSON));
        $this->assertFalse(Variable::isInteger(Carbon::now()));
    }

    public function testWillValidateStrictFloat(): void
    {
        // Strict float
        $this->assertTrue(Variable::isFloat(self::VAR_FLOAT, true));
        $this->assertTrue(Variable::isFloat(self::VAR_FLOAT_E, true));
        $this->assertTrue(Variable::isFloat(self::VAR_FLOAT_INT, true));

        // Non-float
        $this->assertFalse(Variable::isFloat(self::VAR_INT, true));
        $this->assertFalse(Variable::isFloat(self::VAR_STRING, true));
        $this->assertFalse(Variable::isFloat(self::VAR_INT_STR, true));
        $this->assertFalse(Variable::isFloat(self::VAR_FLOAT_E_STR, true));
        $this->assertFalse(Variable::isFloat(self::VAR_FLOAT_STR, true));
        $this->assertFalse(Variable::isFloat(self::VAR_FLOAT_INT_STR, true));
        $this->assertFalse(Variable::isFloat(self::VAR_FLOAT_STR_COMMA, true));
        $this->assertFalse(Variable::isFloat(self::VAR_FLOAT_INT_STR_COMMA, true));
        $this->assertFalse(Variable::isFloat(self::VAR_BOOL_T, true));
        $this->assertFalse(Variable::isFloat(self::VAR_BOOL_F, true));
        $this->assertFalse(Variable::isFloat(self::VAR_ARRAY, true));
        $this->assertFalse(Variable::isFloat(self::VAR_NULL, true));
        $this->assertFalse(Variable::isFloat(self::VAR_JSON, true));
        $this->assertFalse(Variable::isFloat(Carbon::now(), true));
    }

    public function testWillValidateNonStrictFloat(): void
    {
        // Non-strict float
        $this->assertTrue(Variable::isFloat(self::VAR_FLOAT));
        $this->assertTrue(Variable::isFloat(self::VAR_FLOAT_STR));
        $this->assertTrue(Variable::isFloat(self::VAR_FLOAT_STR_COMMA));

        // Non-strict non-float
        $this->assertFalse(Variable::isFloat(self::VAR_FLOAT_E_STR));
        $this->assertFalse(Variable::isFloat(self::VAR_FLOAT_E));
        $this->assertFalse(Variable::isFloat(self::VAR_FLOAT_INT));

        // Non-float
        $this->assertFalse(Variable::isFloat(self::VAR_INT));
        $this->assertFalse(Variable::isFloat(self::VAR_STRING));
        $this->assertFalse(Variable::isFloat(self::VAR_INT_STR));
        $this->assertFalse(Variable::isFloat(self::VAR_FLOAT_INT_STR));
        $this->assertFalse(Variable::isFloat(self::VAR_FLOAT_INT_STR_COMMA));
        $this->assertFalse(Variable::isFloat(self::VAR_BOOL_T));
        $this->assertFalse(Variable::isFloat(self::VAR_BOOL_F));
        $this->assertFalse(Variable::isFloat(self::VAR_ARRAY));
        $this->assertFalse(Variable::isFloat(self::VAR_NULL));
        $this->assertFalse(Variable::isFloat(self::VAR_JSON));
        $this->assertFalse(Variable::isFloat(Carbon::now()));
    }

    public function testWillValidateStrictString(): void
    {
        // Strict string
        $this->assertTrue(Variable::isString(self::VAR_STRING, true));
        $this->assertTrue(Variable::isString(self::VAR_INT_STR, true));
        $this->assertTrue(Variable::isString(self::VAR_FLOAT_E_STR, true));
        $this->assertTrue(Variable::isString(self::VAR_FLOAT_STR, true));
        $this->assertTrue(Variable::isString(self::VAR_FLOAT_INT_STR, true));
        $this->assertTrue(Variable::isString(self::VAR_FLOAT_STR_COMMA, true));
        $this->assertTrue(Variable::isString(self::VAR_FLOAT_INT_STR_COMMA, true));
        $this->assertTrue(Variable::isString(self::VAR_JSON, true));

        // Non-string
        $this->assertFalse(Variable::isString(self::VAR_FLOAT, true));
        $this->assertFalse(Variable::isString(self::VAR_FLOAT_E, true));
        $this->assertFalse(Variable::isString(self::VAR_FLOAT_INT, true));
        $this->assertFalse(Variable::isString(self::VAR_INT, true));
        $this->assertFalse(Variable::isString(self::VAR_BOOL_T, true));
        $this->assertFalse(Variable::isString(self::VAR_BOOL_F, true));
        $this->assertFalse(Variable::isString(self::VAR_ARRAY, true));
        $this->assertFalse(Variable::isString(self::VAR_NULL, true));
        $this->assertFalse(Variable::isString(Carbon::now(), true));
    }

    public function testWillValidateNonStrictString(): void
    {
        // Non-strict string
        $this->assertTrue(Variable::isString(self::VAR_STRING));

        // Non-strict non-string
        $this->assertFalse(Variable::isString(self::VAR_INT_STR));
        $this->assertFalse(Variable::isString(self::VAR_FLOAT_E_STR));
        $this->assertFalse(Variable::isString(self::VAR_FLOAT_STR));
        $this->assertFalse(Variable::isString(self::VAR_FLOAT_INT_STR));
        $this->assertFalse(Variable::isString(self::VAR_FLOAT_STR_COMMA));
        $this->assertFalse(Variable::isString(self::VAR_FLOAT_INT_STR_COMMA));
        $this->assertFalse(Variable::isString(self::VAR_JSON));

        // Non-string
        $this->assertFalse(Variable::isString(self::VAR_FLOAT, true));
        $this->assertFalse(Variable::isString(self::VAR_FLOAT_E, true));
        $this->assertFalse(Variable::isString(self::VAR_FLOAT_INT, true));
        $this->assertFalse(Variable::isString(self::VAR_INT, true));
        $this->assertFalse(Variable::isString(self::VAR_BOOL_T, true));
        $this->assertFalse(Variable::isString(self::VAR_BOOL_F, true));
        $this->assertFalse(Variable::isString(self::VAR_ARRAY, true));
        $this->assertFalse(Variable::isString(self::VAR_NULL, true));
        $this->assertFalse(Variable::isString(Carbon::now(), true));
    }

    public function testWillValidateStrictBool(): void
    {
        // Strict boolean
        $this->assertTrue(Variable::isBool(self::VAR_BOOL_T, true));
        $this->assertTrue(Variable::isBool(self::VAR_BOOL_F, true));
        
        // Non-boolean
        $this->assertFalse(Variable::isBool(self::VAR_STRING, true));
        $this->assertFalse(Variable::isBool(self::VAR_INT_STR, true));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_E_STR, true));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_STR, true));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_INT_STR, true));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_STR_COMMA, true));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_INT_STR_COMMA, true));
        $this->assertFalse(Variable::isBool(self::VAR_JSON, true));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT, true));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_E, true));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_INT, true));
        $this->assertFalse(Variable::isBool(self::VAR_INT, true));
        $this->assertFalse(Variable::isBool(self::VAR_ARRAY, true));
        $this->assertFalse(Variable::isBool(self::VAR_NULL, true));
        $this->assertFalse(Variable::isBool(Carbon::now(), true));
    }

    public function testWillValidateNonStrictBool(): void
    {
        // Boolean
        $this->assertTrue(Variable::isBool(self::VAR_BOOL_T, false));
        $this->assertTrue(Variable::isBool(self::VAR_BOOL_F, false));

        // Non-strict boolean
        $this->assertTrue(Variable::isBool(self::VAR_INT_STR, false));
        $this->assertTrue(Variable::isBool(self::VAR_INT, false));
        $this->assertTrue(Variable::isBool(0, false));
        $this->assertTrue(Variable::isBool('0', false));

        // Non-boolean
        $this->assertFalse(Variable::isBool(self::VAR_STRING, false));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_E_STR, false));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_STR, false));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_INT_STR, false));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_STR_COMMA, false));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_INT_STR_COMMA, false));
        $this->assertFalse(Variable::isBool(self::VAR_JSON, false));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT, false));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_E, false));
        $this->assertFalse(Variable::isBool(self::VAR_FLOAT_INT, false));
        $this->assertFalse(Variable::isBool(self::VAR_ARRAY, false));
        $this->assertFalse(Variable::isBool(self::VAR_NULL, false));
        $this->assertFalse(Variable::isBool(Carbon::now(), false));
    }

    public function testWillValidateArray(): void
    {
        // Array
        $this->assertTrue(Variable::isArray(self::VAR_ARRAY));

        // Non-array
        $this->assertFalse(Variable::isArray(self::VAR_BOOL_T));
        $this->assertFalse(Variable::isArray(self::VAR_BOOL_F));
        $this->assertFalse(Variable::isArray(self::VAR_INT_STR));
        $this->assertFalse(Variable::isArray(self::VAR_INT));
        $this->assertFalse(Variable::isArray(self::VAR_STRING));
        $this->assertFalse(Variable::isArray(self::VAR_FLOAT_E_STR));
        $this->assertFalse(Variable::isArray(self::VAR_FLOAT_STR));
        $this->assertFalse(Variable::isArray(self::VAR_FLOAT_INT_STR));
        $this->assertFalse(Variable::isArray(self::VAR_FLOAT_STR_COMMA));
        $this->assertFalse(Variable::isArray(self::VAR_FLOAT_INT_STR_COMMA));
        $this->assertFalse(Variable::isArray(self::VAR_JSON));
        $this->assertFalse(Variable::isArray(self::VAR_FLOAT));
        $this->assertFalse(Variable::isArray(self::VAR_FLOAT_E));
        $this->assertFalse(Variable::isArray(self::VAR_FLOAT_INT));
        $this->assertFalse(Variable::isArray(self::VAR_NULL));
        $this->assertFalse(Variable::isArray(Carbon::now()));
    }

    public function testWillValidateObject(): void
    {
        // Object without instance
        $this->assertTrue(Variable::isObject(Carbon::now()));

        // Non-object
        $this->assertFalse(Variable::isObject(self::VAR_BOOL_T));
        $this->assertFalse(Variable::isObject(self::VAR_BOOL_F));
        $this->assertFalse(Variable::isObject(self::VAR_INT_STR));
        $this->assertFalse(Variable::isObject(self::VAR_INT));
        $this->assertFalse(Variable::isObject(self::VAR_STRING));
        $this->assertFalse(Variable::isObject(self::VAR_FLOAT_E_STR));
        $this->assertFalse(Variable::isObject(self::VAR_FLOAT_STR));
        $this->assertFalse(Variable::isObject(self::VAR_FLOAT_INT_STR));
        $this->assertFalse(Variable::isObject(self::VAR_FLOAT_STR_COMMA));
        $this->assertFalse(Variable::isObject(self::VAR_FLOAT_INT_STR_COMMA));
        $this->assertFalse(Variable::isObject(self::VAR_JSON));
        $this->assertFalse(Variable::isObject(self::VAR_FLOAT));
        $this->assertFalse(Variable::isObject(self::VAR_FLOAT_E));
        $this->assertFalse(Variable::isObject(self::VAR_FLOAT_INT));
        $this->assertFalse(Variable::isObject(self::VAR_NULL));
        $this->assertFalse(Variable::isObject(self::VAR_ARRAY));
    }

    public function testWillValidateObjectInstance(): void
    {
        // Object with correct instance
        $this->assertTrue(Variable::isObject(Carbon::now(), new Carbon()));
        $this->assertTrue(Variable::isObject(Carbon::now(), \Carbon\Carbon::class));

        // Object with incorrect instance
        $this->assertFalse(Variable::isObject(Carbon::now(), \DateTimeImmutable::class));
        $this->assertFalse(Variable::isObject(Carbon::now(), new \DateTimeImmutable()));

        // Non object
        $this->assertFalse(Variable::isObject(Carbon::now(), 'test'));
        $this->assertFalse(Variable::isObject(self::VAR_STRING, \Carbon\Carbon::class));
        $this->assertFalse(Variable::isObject(self::VAR_ARRAY, 'test'));
    }

    public function testWillValidateDateTimeObject(): void
    {
        // DateTime object
        $this->assertTrue(Variable::isDateTimeObject(Carbon::now()));
        $this->assertTrue(Variable::isDateTimeObject(new \DateTime()));
        $this->assertTrue(Variable::isDateTimeObject(new \DateTimeImmutable()));

        // Not DateTime object
        $this->assertFalse(Variable::isDateTimeObject(new Dice()));
        $this->assertFalse(Variable::isDateTimeObject(self::VAR_INT));
        $this->assertFalse(Variable::isDateTimeObject(self::VAR_STRING));
        $this->assertFalse(Variable::isDateTimeObject(self::VAR_ARRAY));
    }

    public function testWillValidateStrictTypeDetection(): void
    {
        $this->assertEquals(Variable::getType(self::VAR_BOOL_T), 'bool');
        $this->assertEquals(Variable::getType(self::VAR_BOOL_F), 'bool');

        $this->assertNotEquals(Variable::getType(1), 'bool');
        $this->assertNotEquals(Variable::getType(0), 'bool');
        $this->assertNotEquals(Variable::getType(1.0), 'bool');

        $this->assertEquals(Variable::getType(Carbon::now(), true), 'object');
        $this->assertEquals(Variable::getType(self::VAR_INT_STR, true), 'string');
        $this->assertEquals(Variable::getType(self::VAR_STRING, true), 'string');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_E_STR, true), 'string');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_STR, true), 'string');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_INT_STR, true), 'string');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_STR_COMMA, true), 'string');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_INT_STR_COMMA, true), 'string');
        $this->assertEquals(Variable::getType(self::VAR_JSON, true), 'string');
        $this->assertEquals(Variable::getType(self::VAR_INT, true), 'int');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT, true), 'float');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_E, true), 'float');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_INT, true), 'float');
        $this->assertEquals(Variable::getType(self::VAR_NULL, true), 'null');
        $this->assertEquals(Variable::getType(self::VAR_ARRAY, true), 'array');
    }

    public function testWillValidateNonStrictTypeDetection(): void
    {
        $this->assertEquals(Variable::getType(self::VAR_BOOL_T, false), 'bool');
        $this->assertEquals(Variable::getType(self::VAR_BOOL_F, false), 'bool');

        $this->assertNotEquals(Variable::getType(1, false), 'bool');
        $this->assertNotEquals(Variable::getType(0, false), 'bool');
        $this->assertNotEquals(Variable::getType(1.0, false), 'bool');

        $this->assertEquals(Variable::getType(self::VAR_STRING), 'string');
        $this->assertEquals(Variable::getType(self::VAR_JSON), 'json');
        $this->assertEquals(Variable::getType(Carbon::now()), 'object');
        $this->assertEquals(Variable::getType(self::VAR_INT_STR), 'int');
        $this->assertEquals(Variable::getType(self::VAR_INT), 'int');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_E_STR), 'int');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_INT_STR), 'int');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_INT_STR_COMMA), 'int');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_E), 'int');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_INT), 'int');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT), 'float');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_STR), 'float');
        $this->assertEquals(Variable::getType(self::VAR_FLOAT_STR_COMMA), 'float');
        $this->assertEquals(Variable::getType(self::VAR_NULL), 'null');
        $this->assertEquals(Variable::getType(self::VAR_ARRAY), 'array');
    }

    public function testWillValidateIfVariableTypeIsDefault(): void
    {
        $this->assertTrue(Variable::isDefaultType(Variable::getType(self::VAR_STRING)));
        $this->assertTrue(Variable::isDefaultType(Variable::getType(self::VAR_INT)));
        $this->assertTrue(Variable::isDefaultType(Variable::getType(self::VAR_FLOAT)));
        $this->assertTrue(Variable::isDefaultType(Variable::getType(self::VAR_BOOL_T)));
        $this->assertTrue(Variable::isDefaultType(Variable::getType(self::VAR_ARRAY)));
        $this->assertTrue(Variable::isDefaultType(Variable::getType(Carbon::now())));
        $this->assertTrue(Variable::isDefaultType(Variable::getType(self::VAR_NULL)));

        $this->assertFalse(Variable::isDefaultType(Variable::getType(self::VAR_JSON)));
    }

    public function testWillValidateDateTimeObjectConverting(): void
    {
        $date = '2020-04-02';
        $dateTime = '2020-04-02 01:40:30';
        $timestamp = 1585791630;

        $carbonDateTime = Carbon::createFromTimeString($dateTime);
        $dateTimeObject = new DateTime($dateTime);
        $dateTimeImmutable = new DateTimeImmutable($dateTime);

        $this->assertEquals(Variable::convertTimeObjectToString($carbonDateTime), $timestamp);
        $this->assertEquals(Variable::convertTimeObjectToString($dateTimeObject), $timestamp);
        $this->assertEquals(Variable::convertTimeObjectToString($dateTimeImmutable), $timestamp);

        $this->assertEquals(Variable::convertTimeObjectToString($carbonDateTime, false), $dateTime);
        $this->assertEquals(Variable::convertTimeObjectToString($dateTimeObject, false), $dateTime);
        $this->assertEquals(Variable::convertTimeObjectToString($dateTimeImmutable, false), $dateTime);

        $this->assertEquals(Variable::convertTimeObjectToString($carbonDateTime, false, 'Y-m-d'), $date);
        $this->assertEquals(Variable::convertTimeObjectToString($dateTimeObject, false, 'Y-m-d'), $date);
        $this->assertEquals(Variable::convertTimeObjectToString($dateTimeImmutable, false, 'Y-m-d'), $date);
    }

    public function testWillRestoreArrayTypes(): void
    {
        $carbonObject = Carbon::now();

        $array = [
            'string' => 'some text',
            'integer' => 10,
            'integer_bool' => 1,
            'float' => 1.1,
            'float_int' => 1.0,
            'float_string' => '1,2',
            'float_str_int' => '1,0',
            'string_int' => '11',
            'null' => null,
            'empty' => ' ',
            'bool' => false,
            'array' => [
                'object' => $carbonObject,
                'json' => '{"t1":"text","t2":"12"}',
            ],
        ];

        $this->assertEquals(
            Variable::restoreArrayTypes($array),
            [
                'string' => 'some text',
                'integer' => 10,
                'integer_bool' => 1,
                'float' => 1.1,
                'float_int' => 1,
                'float_string' => 1.2,
                'float_str_int' => 1,
                'string_int' => 1,
                'null' => null,
                'empty' => null,
                'bool' => false,
                'array' => [
                    'object' => $carbonObject,
                    'json' => [
                        't1' => 'text',
                        't2' => 12,
                    ],
                ],
            ]
        );
    }
}
