<?php
declare(strict_types=1);

namespace Common\Integrational\Test;

use Common\BaseClass\BaseTestCase;
use Common\Regex;

class RegexTest extends BaseTestCase
{
    public function testWillValidatePattern(): void
    {
        self::assertTrue(Regex::isValidPattern('Test', Regex::VAR_AZ_UPPER_FIRST));
        self::assertTrue(Regex::isValidPattern('url-type-test', Regex::VAR_KEBAB_CASE));

        self::assertFalse(Regex::isValidPattern('Test', Regex::VAR_KEBAB_CASE));
    }

    public function testWillValidateEmail(): void
    {
        self::assertTrue(Regex::isEmail('test@test.com'));
        self::assertTrue(Regex::isEmail('any_mail@any.domain.co.uk'));

        self::assertFalse(Regex::isEmail('any mail@any.domain.co.uk'));
        self::assertFalse(Regex::isEmail('any.mail@domain'));
        self::assertFalse(Regex::isEmail('John Doe'));
    }

    public function testWillValidateIfVariableIsMethodName(): void
    {
        self::assertTrue(Regex::isMethodName('getTitle', Regex::METHOD_TYPE_SET_GET));
        self::assertTrue(Regex::isMethodName('isMale', Regex::METHOD_TYPE_SET_GET));
        self::assertTrue(Regex::isMethodName('setTitle', Regex::METHOD_TYPE_SET_GET));
        self::assertTrue(Regex::isMethodName('getName', Regex::METHOD_TYPE_GET));
        self::assertTrue(Regex::isMethodName('isCorrect', Regex::METHOD_TYPE_GET));
        self::assertTrue(Regex::isMethodName('setDate', Regex::METHOD_TYPE_SET));
        self::assertTrue(Regex::isMethodName('executeSomething', 'execute'));
        self::assertTrue(Regex::isMethodName('doSomething'));

        self::assertFalse(Regex::isMethodName('createTitle', Regex::METHOD_TYPE_SET_GET));
        self::assertFalse(Regex::isMethodName('create', Regex::METHOD_TYPE_SET_GET));
        self::assertFalse(Regex::isMethodName('doSomething', Regex::METHOD_TYPE_SET_GET));
        self::assertFalse(Regex::isMethodName('setName', Regex::METHOD_TYPE_GET));
        self::assertFalse(Regex::isMethodName('setCorrect', Regex::METHOD_TYPE_GET));
        self::assertFalse(Regex::isMethodName('getDate', Regex::METHOD_TYPE_SET));
        self::assertFalse(Regex::isMethodName('doSomething', 'execute'));
        self::assertFalse(Regex::isMethodName('DoSomething'));
    }

    public function testWillValidateIfVariableIsClassName(): void
    {
        self::assertTrue(Regex::isClassName('NewClassName'));
        self::assertTrue(Regex::isClassName('TestClass123'));

        self::assertFalse(Regex::isClassName('newClassName'));
        self::assertFalse(Regex::isClassName('1stTestClass'));
    }
}
