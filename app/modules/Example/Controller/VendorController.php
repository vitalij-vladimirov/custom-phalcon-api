<?php
declare(strict_types=1);

namespace Example\Controller;

use Common\ApiException\ForbiddenApiException;
use Common\ApiException\NotFoundApiException;
use Common\Entity\PaginatedResult;
use Common\Entity\PaginationFilter;
use Common\Exception\DatabaseException;
use Example\Entity\VendorFilter;
use Example\Model\VendorModel;
use Example\Repository\VendorsRepository;
use Example\Service\VendorManager;
use Illuminate\Support\Collection;

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

    public function createVendor(): void
    {
    }

    public function updateVendor(): void
    {
    }

    public function deleteVendor(VendorModel $vendorModel): void
    {
        $this->vendorsRepository->deleteModel($vendorModel);
    }
}
