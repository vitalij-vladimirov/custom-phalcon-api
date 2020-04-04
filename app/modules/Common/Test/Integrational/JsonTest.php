<?php
declare(strict_types=1);

namespace Common\Integrational\Test;

use Common\BaseClasses\BaseTest;
use Common\Json;
use Common\Variable;

class JsonTest extends BaseTest
{
    private const JSON_OBJECT = '{"t":"y","t2":{"t1":1,"t2":"two"},"t3":[["v1","v2"],["v3","v4"]]}';
    private const JSON_ARRAY = ['t' => 'y','t2' => ['t1' => 1,'t2' => 'two'],'t3' => [['v1','v2'],['v3','v4']]];
    private const JSON_STRING = 'test string';
    private const JSON_ONE = 1;
    private const JSON_ZERO = 0;
    private const JSON_STRING_ONE = '1';
    private const JSON_STRING_ZERO = '0';
    private const JSON_TRUE = true;
    private const JSON_FALSE = false;

    public function testWillGetVariableType(): void
    {
        self::assertEquals('json', Variable::getType(self::JSON_OBJECT));
        self::assertEquals('array', Variable::getType(self::JSON_ARRAY));
        self::assertEquals('string', Variable::getType(self::JSON_STRING));
        self::assertEquals('int', Variable::getType(self::JSON_ONE));
        self::assertEquals('int', Variable::getType(self::JSON_ZERO));
        self::assertEquals('int', Variable::getType(self::JSON_STRING_ONE));
        self::assertEquals('int', Variable::getType(self::JSON_STRING_ZERO));
        self::assertEquals('bool', Variable::getType(self::JSON_TRUE));
        self::assertEquals('bool', Variable::getType(self::JSON_FALSE));
    }

    public function testWillCheckIfVariableIsJson(): void
    {
        self::assertTrue(Json::isJson(self::JSON_OBJECT));
        self::assertFalse(Json::isJson(self::JSON_ARRAY));
        self::assertFalse(Json::isJson(self::JSON_STRING));
        self::assertFalse(Json::isJson(self::JSON_ONE));
        self::assertFalse(Json::isJson(self::JSON_ZERO));
        self::assertFalse(Json::isJson(self::JSON_STRING_ONE));
        self::assertFalse(Json::isJson(self::JSON_STRING_ZERO));
        self::assertFalse(Json::isJson(self::JSON_TRUE));
        self::assertFalse(Json::isJson(self::JSON_FALSE));
    }

    public function testWillEncodeArray(): void
    {
        self::assertEquals(self::JSON_OBJECT, Json::encode(self::JSON_ARRAY));
    }

    public function testWillDecodeJsonObject(): void
    {
        self::assertEquals(self::JSON_ARRAY, Json::decode(self::JSON_OBJECT));
    }
}
