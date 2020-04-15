<?php
declare(strict_types=1);

namespace Example\Controller;

use Common\ApiException\NotFoundApiException;
use Example\Repository\VendorsRepository;

class VendorController
{
    private VendorsRepository $vendorsRepository;

    public function __construct(
        VendorsRepository $vendorsRepository
    ) {
        if (APP_ENV === 'production') {
            throw new NotFoundApiException();
        }

        $this->vendorsRepository = $vendorsRepository;
    }

    public function getVendors(): void
    {
    }

    public function getVendor(): void
    {
    }

    public function createVendor(): void
    {
    }

    public function updateVendor(): void
    {
    }

    public function deleteVendor(): void
    {
    }
}
