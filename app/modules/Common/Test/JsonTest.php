<?php
declare(strict_types=1);

namespace Common\Test;

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
        $this->assertEquals('json', Variable::getType(self::JSON_OBJECT));
        $this->assertEquals('array', Variable::getType(self::JSON_ARRAY));
        $this->assertEquals('string', Variable::getType(self::JSON_STRING));
        $this->assertEquals('int', Variable::getType(self::JSON_ONE));
        $this->assertEquals('int', Variable::getType(self::JSON_ZERO));
        $this->assertEquals('int', Variable::getType(self::JSON_STRING_ONE));
        $this->assertEquals('int', Variable::getType(self::JSON_STRING_ZERO));
        $this->assertEquals('bool', Variable::getType(self::JSON_TRUE));
        $this->assertEquals('bool', Variable::getType(self::JSON_FALSE));
    }

    public function testWillCheckIfVariableIsJson(): void
    {
        $this->assertTrue(Json::isJson(self::JSON_OBJECT));
        $this->assertFalse(Json::isJson(self::JSON_ARRAY));
        $this->assertFalse(Json::isJson(self::JSON_STRING));
        $this->assertFalse(Json::isJson(self::JSON_ONE));
        $this->assertFalse(Json::isJson(self::JSON_ZERO));
        $this->assertFalse(Json::isJson(self::JSON_STRING_ONE));
        $this->assertFalse(Json::isJson(self::JSON_STRING_ZERO));
        $this->assertFalse(Json::isJson(self::JSON_TRUE));
        $this->assertFalse(Json::isJson(self::JSON_FALSE));
    }

    public function testWillEncodeArray(): void
    {
        $this->assertEquals(self::JSON_OBJECT, Json::encode(self::JSON_ARRAY));
    }

    public function testWillDecodeJsonObject(): void
    {
        $this->assertEquals(self::JSON_ARRAY, Json::decode(self::JSON_OBJECT));
    }
}
