<?php
declare(strict_types=1);

namespace Common\Interfaces;

use Documentation\Entity\RequestDoc;

interface RequestMapperInterface
{
    public function mapRequestToObject(array $data);
    public function requestDocumentation(): ?RequestDoc;
}
