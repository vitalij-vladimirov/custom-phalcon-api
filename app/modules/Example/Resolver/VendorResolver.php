<?php
declare(strict_types=1);

namespace Example\Resolver;

use Common\ApiException\NotFoundApiException;
use Common\Interfaces\ResolverInterface;
use Documentation\Entity\ParameterDoc;
use Example\Config\ErrorCodes;
use Example\Model\VendorModel;
use Example\Repository\VendorsRepository;

class VendorResolver implements ResolverInterface
{
    private VendorsRepository $vendorRepository;

    public function __construct(VendorsRepository $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    public function resolveParameter($parameter): VendorModel
    {
        /** @var VendorModel $vendor */
        $vendor = $this->vendorRepository->findOneById((int)$parameter);

        if ($vendor === null) {
            throw new NotFoundApiException('Vendor not found.', ErrorCodes::VENDOR_NOT_FOUND);
        }

        return $vendor;
    }

    public function parameterDocumentation(): ?ParameterDoc
    {
        // TODO: write parameter documentation
        return null;
    }
}
