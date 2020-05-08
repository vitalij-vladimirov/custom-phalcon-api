<?php
declare(strict_types=1);

namespace Example\Service;

use Illuminate\Support\Collection;
use Common\Service\Injectable;
use Common\Entity\PaginatedResult;
use Common\Entity\PaginationFilter;
use Common\Json;
use Example\Entity\VendorFilter;
use Example\Model\VendorModel;
use Example\Repository\VendorsRepository;
use Example\Exception\VendorAlreadyExistsException;
use Example\Resolver\ComposerDataResolver;

class VendorManager extends Injectable
{
    private VendorsRepository $vendorsRepository;
    private ComposerDataResolver $composerDataResolver;

    public function __construct(
        VendorsRepository $vendorsRepository,
        ComposerDataResolver $composerDataResolver
    ) {
        $this->vendorsRepository = $vendorsRepository;
        $this->composerDataResolver = $composerDataResolver;
    }

    public function getVendorsList(VendorFilter $vendorFilter): Collection
    {
        if ($vendorFilter->getEnvironment() !== null) {
            return $this->vendorsRepository->findManyBy(
                'environment',
                $vendorFilter->getEnvironment()
            );
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
        $vendorExists = $this->vendorsRepository
            ->oneWithLibNameOrLibUrlAndDifferentIdExists(
                $updateData,
                $vendorModel->getId()
            )
        ;

        if ($vendorExists) {
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
        $composer = Json::decode($this->composerDataResolver->getComposerLockData());

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
