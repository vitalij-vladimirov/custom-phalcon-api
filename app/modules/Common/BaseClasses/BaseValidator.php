<?php
declare(strict_types=1);

namespace Common\BaseClasses;

abstract class BaseValidator
{
    abstract public function validateData(array $data): void;
}
