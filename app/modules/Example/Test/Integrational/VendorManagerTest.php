<?php
declare(strict_types=1);

namespace Example\Test\Integrational;

use Common\BaseClass\BaseTestCase;
use Example\Model\VendorModel;
use Example\Repository\VendorsRepository;
use Example\Service\VendorManager;

class VendorManagerTest extends BaseTestCase
{
    private const EXAMPLE_VENDORS = [
        [
            'lib_name' => 'Laravel Filesystem',
            'lib_url' => 'illuminate/filesystem',
            'version' => '7.4',
            'environment' => 'production',
            'description' => 'Laravel File manager required to create migrations and seeds',
        ], [
            'lib_name' => 'Phinx',
            'lib_url' => 'robmorgan/phinx',
            'version' => '0.11.5',
            'environment' => 'production',
            'description' => 'Migrations manager required to run Eloquent migrations',
        ], [
            'lib_name' => 'Phalcon IDE stubs',
            'lib_url' => 'phalcon/ide-stubs',
            'version' => '4.0',
            'environment' => 'development',
            'description' => 'Phalcon IDE helpers',
        ], [
            'lib_name' => 'Phalcon DD',
            'lib_url' => 'phalcon/dd',
            'version' => '1.1',
            'environment' => 'development',
            'description' => 'Phalcon dump debugging manager',
        ]
    ];

    /** @var VendorManager */
    private $vendorManager;

    /** @var VendorsRepository */
    private $vendorsRepository;

    protected function setUp(): void
    {
        $this->truncate(new VendorModel());

        $this->vendorsRepository = $this->inject(VendorsRepository::class);
        $this->vendorManager = $this->inject(VendorManager::class);
    }

    public function testWillUpdateVendorsListFromComposerLockFile(): void
    {
        $this->vendorsRepository->createMany(self::EXAMPLE_VENDORS);

        // mock composer and create test

        $this->vendorManager->updateVendorsFromComposer();
    }
}
