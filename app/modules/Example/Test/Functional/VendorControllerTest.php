<?php
declare(strict_types=1);

namespace Example\Test\Functional;

use Common\BaseClass\BaseTestCase;
use Example\Repository\VendorsRepository;

class VendorControllerTest extends BaseTestCase
{
    /** @var VendorsRepository $vendorRepository */
    private $vendorRepository;

    protected function setUp(): void
    {
        $this->vendorRepository = $this->inject(VendorsRepository::class);
    }

    public function testWillGetListOfVendors(): void
    {
        $vendors = $this->vendorRepository->createMany([
            [
                'lib_name' => 'PHP .env',
                'lib_url' => 'vlucas/phpdotenv',
                'version' => '4.1',
                'environment' => 'production',
                'description' => 'Env configuration reader',
            ], [
                'lib_name' => 'PHP Cron Scheduler',
                'lib_url' => 'peppeocchi/php-cron-scheduler',
                'version' => '3.0',
                'environment' => 'production',
                'description' => 'Cronjobs scheduler',
            ]
        ]);
    }
}
