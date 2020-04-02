<?php
declare(strict_types=1);

namespace Common\Test;

use Common\BaseClasses\BaseTest;
use Common\Text;

class TextTest extends BaseTest
{
    private const RAW_CASE = 'Test text of five words';
    private const PASCAL_CASE = 'TestTextOfFiveWords';
    private const CAMEL_CASE = 'testTextOfFiveWords';
    private const CASE_CASE = 'test_text_of_five_words';
    private const KEBAB_CASE = 'test-text-of-five-words';

    public function testWillConvertTextToCamelCase(): void
    {
        $this->assertEquals(self::CAMEL_CASE, Text::camelize(self::RAW_CASE));
        $this->assertEquals(self::CAMEL_CASE, Text::camelize(self::PASCAL_CASE));
        $this->assertEquals(self::CAMEL_CASE, Text::camelize(self::CASE_CASE));
        $this->assertEquals(self::CAMEL_CASE, Text::camelize(self::KEBAB_CASE));
    }
}
