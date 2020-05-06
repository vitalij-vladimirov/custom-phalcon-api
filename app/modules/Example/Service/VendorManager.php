<?php
declare(strict_types=1);

namespace Example\Service;

use Common\Entity\PaginatedResult;
use Common\Entity\PaginationFilter;
use Common\File;
use Common\Json;
use Example\Entity\VendorFilter;
use Example\Model\VendorModel;
use Example\Repository\VendorsRepository;
use Example\Exception\VendorAlreadyExistsException;
use Illuminate\Support\Collection;

class VendorManager
{
    private const COMPOSER_LOCATION = '/app/composer.lock';

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

    public function updateVendorsFromComposer(): void
    {
        $composer = Json::decode($this->getComposerLockData());

        /** @var VendorModel[] $vendors */
        $vendors = $this->vendorsRepository->all();

        $currentVendorsList = [];
        foreach ($vendors as $vendor) {
            $currentVendorsList[$vendor->getLibUrl()] = $vendor;
        }

        $currentVendorsList = $this->readPackagesAndUpdateVendors(
            $composer['packages'],
            'production',
            $currentVendorsList
        );

        $currentVendorsList = $this->readPackagesAndUpdateVendors(
            $composer['packages-dev'],
            'development',
            $currentVendorsList
        );

        foreach ($currentVendorsList as $unusedVendor) {
            $this->vendorsRepository->deleteModel($unusedVendor);
        }
    }

    private function getComposerLockData(): string
    {
        return File::read(self::COMPOSER_LOCATION);
    }

    private function readPackagesAndUpdateVendors(
        array $packages,
        string $environment,
        array $currentVendorsList
    ): array {
        foreach ($packages as $package) {
            if (!isset($package['name'], $currentVendorsList[$package['name']])) {
                continue;
            }

            /** @var VendorModel $vendor */
            $vendor = $currentVendorsList[$package['name']];

            if ($package['version'][0] === 'v') {
                $package['version'] = substr($package['version'], 1);
            }

            $vendor
                ->setVersion($package['version'])
                ->setEnvironment($environment)
            ;

            if (!empty($package['description'])) {
                $vendor->setDescription($package['description']);
            }

            $this->vendorsRepository->updateModel($vendor);

            unset($currentVendorsList[$package['name']]);
        }

        return $currentVendorsList;
    }
}
