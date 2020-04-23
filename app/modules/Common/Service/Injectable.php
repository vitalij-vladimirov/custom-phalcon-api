<?php
declare(strict_types=1);

namespace Common\Service;

use Phalcon\Di\Injectable as PhalconInjectable;
use Dice\Dice;

class Injectable extends PhalconInjectable
{
    public function inject(string $class): object
    {
        return (new Dice())->create($class);
    }
}
