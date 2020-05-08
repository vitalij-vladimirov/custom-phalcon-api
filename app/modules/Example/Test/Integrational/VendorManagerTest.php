<?php
declare(strict_types=1);

namespace Example\Test\Integrational;

use PHPUnit\Framework\MockObject\MockObject;
use Common\BaseClass\BaseTestCase;
use Common\Json;
use Example\Model\VendorModel;
use Example\Repository\VendorsRepository;
use Example\Resolver\ComposerDataResolver;
use Example\Service\VendorManager;

class VendorManagerTest extends BaseTestCase
{
    private const EXAMPLE_VENDORS = [
        [
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

    /** @var VendorsRepository */
    private $vendorsRepository;

    /** @var ComposerDataResolver|MockObject */
    private $composerDataResolverMock;

    protected function setUp(): void
    {
        $this->composerDataResolverMock = $this
            ->createStub(ComposerDataResolver::class);

        $this->truncate(new VendorModel());

        $this->vendorsRepository = $this->inject(VendorsRepository::class);
    }

    public function testWillUpdateVendorsListFromComposerLockFile(): void
    {
        $this->composerDataResolverMock
            ->method('getComposerLockData')
            ->willReturn(Json::encode([
                '_readme' => 'Test readme',
                'packages' => [
                    [
                        'name' => self::EXAMPLE_VENDORS[0]['lib_url'],
                        'version' => '9.9.9',
                        'description' => 'Production vendor test description',
                    ],
                ],
                'packages-dev' => [
                    [
                        'name' => self::EXAMPLE_VENDORS[2]['lib_url'],
                        'version' => 'v7.7.7',
                        'description' => 'Development vendor test description',
                    ],
                ],
            ]))
        ;

        $this->registerMock($this->composerDataResolverMock);

        $originalVendors = $this->vendorsRepository->createMany(self::EXAMPLE_VENDORS);

        $vendorManager = $this->inject(VendorManager::class);
        $vendorManager->updateVendorsFromComposer();

        $modifiedVendors = $this->vendorsRepository->all();

        $this->assertEquals(
            [
                [
                        'id' => $originalVendors[0]->getId(),
                        'lib_name' => self::EXAMPLE_VENDORS[0]['lib_name'],
                        'lib_url' => self::EXAMPLE_VENDORS[0]['lib_url'],
                        'version' => '9.9.9',
                        'environment' => self::EXAMPLE_VENDORS[0]['environment'],
                        'description' => 'Production vendor test description',
                        'created_at' => $originalVendors[0]->getCreatedAt()->timestamp,
                        'updated_at' => $originalVendors[0]->getUpdatedAt()->timestamp,
                ], [
                        'id' => $originalVendors[2]->getId(),
                        'lib_name' => self::EXAMPLE_VENDORS[2]['lib_name'],
                        'lib_url' => self::EXAMPLE_VENDORS[2]['lib_url'],
                        'version' => '7.7.7',
                        'environment' => self::EXAMPLE_VENDORS[2]['environment'],
                        'description' => 'Development vendor test description',
                        'created_at' => $originalVendors[2]->getCreatedAt()->timestamp,
                        'updated_at' => $originalVendors[2]->getUpdatedAt()->timestamp,
                    ],
                ],
            $modifiedVendors->toArray()
        );
    }
}
