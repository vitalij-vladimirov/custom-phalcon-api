<?php
declare(strict_types=1);

namespace Common\BaseClass;

use Documentation\Entity\ParameterDoc;

abstract class BaseResolver
{
    abstract public function resolveParameter($parameter);
    abstract public function parameterDocumentation(): ?ParameterDoc;
}
