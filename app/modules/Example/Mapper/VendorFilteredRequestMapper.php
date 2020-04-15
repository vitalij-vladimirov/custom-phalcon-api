<?php
declare(strict_types=1);

namespace Example\Mapper;

use Common\Interfaces\RequestMapperInterface;
use Documentation\Entity\RequestDoc;

class VendorFilteredRequestMapper implements RequestMapperInterface
{
    public function mapRequestToObject(array $data) // return filter entity
    {
        // TODO: write filter entity mapper
    }

    public function requestDocumentation(): ?RequestDoc
    {
        // TODO: write request documentation
        return null;
    }
}
