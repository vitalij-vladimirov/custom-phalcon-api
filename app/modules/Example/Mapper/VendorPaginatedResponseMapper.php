<?php
declare(strict_types=1);

namespace Example\Mapper;

use Common\Interfaces\ResponseMapperInterface;
use Documentation\Entity\ResponseDoc;

class VendorPaginatedResponseMapper implements ResponseMapperInterface
{
    public function mapResponseToArray($object): array
    {
        // TODO: write paginated result mapper
        return [];
    }

    public function responseDocumentation(): ?ResponseDoc
    {
        // TODO: write response documentation, create default paginated result doc
        return null;
    }
}
