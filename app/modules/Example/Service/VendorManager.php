<?php
declare(strict_types=1);

namespace Example\Service;

use Common\Entity\PaginatedResult;
use Common\Entity\PaginationFilter;
use Example\Entity\VendorFilter;
use Example\Model\VendorModel;
use Example\Repository\VendorsRepository;
use Example\Exception\VendorAlreadyExistsException;
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

    public function createVendor(VendorModel $vendorModel): VendorModel
    {
        if ($this->vendorsRepository->oneWithLibNameOrLibUrlExists($vendorModel)) {
            throw new VendorAlreadyExistsException();
        }

        return $this->vendorsRepository->createModel($vendorModel);
    }

    public function updateVendor(VendorModel $vendorModel, VendorModel $updateData): VendorModel
    {
        if ($this->vendorsRepository->oneWithLibNameOrLibUrlAndDifferentIdExists($updateData, $vendorModel->getId())) {
            throw new VendorAlreadyExistsException();
        }

        $vendorModel
            ->setLibName($updateData->getLibName())
            ->setLibUrl($updateData->getLibUrl())
            ->setDescription($updateData->getDescription())
            ->setEnvironment($updateData->getEnvironment())
            ->setVersion($updateData->getVersion())
        ;

        return $this->vendorsRepository->updateModel($vendorModel);
    }
}
