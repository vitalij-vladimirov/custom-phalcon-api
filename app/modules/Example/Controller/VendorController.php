<?php
declare(strict_types=1);

namespace Example\Controller;

use Illuminate\Support\Collection;
use Common\ApiException\BadRequestApiException;
use Common\ApiException\NotFoundApiException;
use Common\Entity\PaginatedResult;
use Common\Entity\PaginationFilter;
use Example\Config\ErrorCodes;
use Example\Entity\VendorFilter;
use Example\Exception\VendorAlreadyExistsException;
use Example\Model\VendorModel;
use Example\Repository\VendorsRepository;
use Example\Service\VendorManager;

class VendorController
{
    private VendorManager $vendorManager;
    private VendorsRepository $vendorsRepository;

    public function __construct(
        VendorManager $vendorManager,
        VendorsRepository $vendorsRepository
    ) {
        if (APP_ENV === 'production') {
            throw new NotFoundApiException();
        }

        $this->vendorsRepository = $vendorsRepository;
        $this->vendorManager = $vendorManager;
    }

    /**
     * @param VendorFilter $vendorFilter
     * @return Collection|VendorModel[]
     */
    public function getVendors(VendorFilter $vendorFilter): Collection
    {
        return $this->vendorManager->getVendorsList($vendorFilter);
    }

    public function getVendorsPaginated(PaginationFilter $paginationFilter): PaginatedResult
    {
        return $this->vendorManager->getVendorsPaginated($paginationFilter);
    }

    public function getVendor(VendorModel $vendorModel): VendorModel
    {
        return $vendorModel;
    }

    public function createVendor(VendorModel $vendorModel): VendorModel
    {
        try {
            return $this->vendorManager->createVendor($vendorModel);
        } catch (VendorAlreadyExistsException $exception) {
            throw new BadRequestApiException(
                'Vendor already exists.',
                ErrorCodes::VENDOR_ALREADY_EXISTS
            );
        }
    }

    public function updateVendor(VendorModel $vendorModel, VendorModel $updateData): VendorModel
    {
        try {
            return $this->vendorManager->updateVendor($vendorModel, $updateData);
        } catch (VendorAlreadyExistsException $exception) {
            throw new BadRequestApiException(
                'Vendor with the same name or url already exists.',
                ErrorCodes::VENDOR_ALREADY_EXISTS
            );
        }
    }

    public function deleteVendor(VendorModel $vendorModel): bool
    {
        return $this->vendorsRepository->deleteModel($vendorModel);
    }
}
