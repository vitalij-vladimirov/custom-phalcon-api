<?php
declare(strict_types=1);

namespace Example\Controller;

use Common\ApiException\NotFoundApiException;
use Example\Model\VendorModel;

class VendorController
{
    public function __construct()
    {
        if (APP_ENV === 'production') {
            throw new NotFoundApiException();
        }
    }

    public function getVendors(): void
    {
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

    public function deleteVendor(): void
    {
    }
}
