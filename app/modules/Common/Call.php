<?php
declare(strict_types=1);

namespace Common;

use Dice\Dice;

class Call
{
    public static function class(string $class): object
    {
        return (new Dice())->create($class);
    }
}
