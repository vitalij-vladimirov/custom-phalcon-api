<?php
declare(strict_types=1);

namespace Common;

use Phalcon\Text as PhalconText;

class Text extends PhalconText
{
    public function lowerCamelize(string $variable): string
    {
        return lcfirst(self::camelize($variable));
    }
}
