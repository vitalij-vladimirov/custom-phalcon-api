<?php
declare(strict_types=1);

namespace Common\Interfaces;

use Common\BaseClasses\BaseEntity;
use Common\BaseClasses\BaseModel;
use Documentation\Entity\ParameterDoc;

interface ResolverInterface
{
    /**
     * @param int|string $parameter
     * @return BaseEntity|BaseModel
     */
    public function resolveParameter($parameter);

    public function parameterDocumentation(): ?ParameterDoc;
}
