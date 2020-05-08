<?php
declare(strict_types=1);

namespace Common\BaseClass;

abstract class BaseResolver
{
    abstract public function resolveParameter(string $parameter);
//    abstract public function parameterDocumentation(): ?ParameterDoc;
}
