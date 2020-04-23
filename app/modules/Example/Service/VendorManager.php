<?php
declare(strict_types=1);

namespace Example\Service;

use Common\Entity\PaginatedResult;
use Common\Entity\PaginationFilter;
use Example\Entity\VendorFilter;
use Example\Repository\VendorsRepository;
use Illuminate\Support\Collection;

class VendorManager
{
    private VendorsRepository $vendorsRepository;

    public function __construct(VendorsRepository $vendorsRepository)
    {
        $this->vendorsRepository = $vendorsRepository;
    }

    public function getVendorsList(VendorFilter $vendorFilter): Collection
    {
        if ($vendorFilter->getEnvironment() !== null) {
            return $this->vendorsRepository->findManyBy('environment', $vendorFilter->getEnvironment());
        }

        return $this->vendorsRepository->all();
    }

    public function getVendorsPaginated(PaginationFilter $paginationFilter): PaginatedResult
    {
        return $this->vendorsRepository->paginateByFilter(
            $paginationFilter->getFilter(),
            $paginationFilter->getLimit(),
            $paginationFilter->getPage(),
            $paginationFilter->getOrderBy(),
            $paginationFilter->getOrderDirection()
        );
    }
}
