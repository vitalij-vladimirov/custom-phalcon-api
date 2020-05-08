<?php
declare(strict_types=1);

namespace Example\Resolver;

use Common\ApiException\NotFoundApiException;
use Common\BaseClass\BaseResolver;
use Example\Config\ErrorCodes;
use Example\Model\VendorModel;
use Example\Repository\VendorsRepository;

class VendorResolver extends BaseResolver
{
    private VendorsRepository $vendorsRepository;

    public function __construct(VendorsRepository $vendorsRepository)
    {
        $this->vendorsRepository = $vendorsRepository;
    }

    public function resolveParameter($parameter): VendorModel
    {
        /** @var VendorModel $vendor */
        $vendor = $this->vendorsRepository->findOneById((int)$parameter);

        if ($vendor === null) {
            throw new NotFoundApiException('Vendor not found.', ErrorCodes::VENDOR_NOT_FOUND);
        }

        return $vendor;
    }
}
