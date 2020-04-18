<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Validation;

abstract class BaseValidator extends Validation
{
    protected const STRING_LENGTH = '255';

    abstract public function validateData(array $data): void;
}
