<?php
declare(strict_types=1);

namespace Example\Mapper;

use Common\Interfaces\RequestMapperInterface;
use Example\Entity\VendorFilter;

class VendorFilteredRequestMapper implements RequestMapperInterface
{
    public function mapRequestToObject(array $data): VendorFilter
    {
        $vendorFilter = new VendorFilter();

        if (isset($data['environment'])) {
            $vendorFilter->setEnvironment($data['environment']);
        }

        if (isset($data['search'])) {
            $vendorFilter->setSearch($data['search']);
        }

        return $vendorFilter;
    }
}
