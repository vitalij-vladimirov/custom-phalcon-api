<?php
declare(strict_types=1);

namespace Example\Test\Functional;

use Common\BaseClass\BaseTestCase;
use Example\Model\VendorModel;
use Example\Repository\VendorsRepository;

class VendorControllerTest extends BaseTestCase
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

    /** @var VendorsRepository $vendorsRepository */
    private $vendorsRepository;

    protected function setUp(): void
    {
        $this->truncate(new VendorModel());

        $this->vendorsRepository = $this->inject(VendorsRepository::class);
    }

    public function testWillGetFullListOfVendors(): void
    {
        $vendors = $this->vendorsRepository->createMany(self::EXAMPLE_VENDORS);

        $request = $this->getRequest('/api/example/vendors');

        self::assertEquals(200, $request->getStatusCode());
        self::assertEquals(
            [
                array_merge(['id' => $vendors[0]->getId()], self::EXAMPLE_VENDORS[0]),
                array_merge(['id' => $vendors[1]->getId()], self::EXAMPLE_VENDORS[1]),
                array_merge(['id' => $vendors[2]->getId()], self::EXAMPLE_VENDORS[2]),
                array_merge(['id' => $vendors[3]->getId()], self::EXAMPLE_VENDORS[3]),
            ],
            $request->getJsonContent()
        );
    }

    public function testWillGetFilteredListOfVendors(): void
    {
        $vendors = $this->vendorsRepository->createMany(self::EXAMPLE_VENDORS);

        $request = $this->getRequest('/api/example/vendors', [
            'environment' => 'development'
        ]);

        self::assertEquals(4, $vendors->count());
        self::assertEquals(200, $request->getStatusCode());
        self::assertEquals(
            [
                array_merge(['id' => $vendors[2]->getId()], self::EXAMPLE_VENDORS[2]),
                array_merge(['id' => $vendors[3]->getId()], self::EXAMPLE_VENDORS[3]),
            ],
            $request->getJsonContent()
        );
    }

    public function testWillGetEmptyListOfVendors(): void
    {
        $request = $this->getRequest('/api/example/vendors', [
            'environment' => 'development'
        ]);

        self::assertEquals(200, $request->getStatusCode());
        self::assertEquals([], $request->getJsonContent());
    }

    public function testWillGetPaginatedListOfVendors(): void
    {
        $vendors = $this->vendorsRepository->createMany(self::EXAMPLE_VENDORS);

        $request = $this->getRequest(
            '/api/example/vendors/paginated',
            [
                'limit' => 2,
                'page' => 1,
                'order_by' => 'lib_name',
                'order_direction' => 'asc',
            ]
        );

        self::assertEquals(200, $request->getStatusCode());
        self::assertEquals(
            [
                'total_results' => 4,
                'total_pages' => 2,
                'current_page' => 1,
                'limit' => 2,
                'data' => [
                    array_merge(['id' => $vendors[0]->getId()], self::EXAMPLE_VENDORS[0]),
                    array_merge(['id' => $vendors[3]->getId()], self::EXAMPLE_VENDORS[3]),
                ],
            ],
            $request->getJsonContent()
        );
    }

    public function testWillGetVendorById(): void
    {
        $vendors = $this->vendorsRepository->createMany(self::EXAMPLE_VENDORS);

        $request = $this->getRequest('/api/example/vendors/' . $vendors[0]->getId());

        self::assertEquals(200, $request->getStatusCode());
        self::assertEquals(
            array_merge(
                [
                    'id' => $vendors[0]->getId()
                ],
                self::EXAMPLE_VENDORS[0]
            ),
            $request->getJsonContent()
        );
    }

    public function testWillCreateVendor(): void
    {
        $request = $this->postRequest(
            '/api/example/vendors',
            self::EXAMPLE_VENDORS[2]
        );

        self::assertEquals(200, $request->getStatusCode());

        $vendors = $this->vendorsRepository->all();

        self::assertEquals(1, $vendors->count());

        /** @var VendorModel $vendor */
        $vendor = $vendors[0];

        self::assertEquals(
            [
                'id' => $vendor->getId(),
                'lib_name' => $vendor->getLibName(),
                'lib_url' => $vendor->getLibUrl(),
                'description' => $vendor->getDescription(),
                'environment' => $vendor->getEnvironment(),
                'version' => $vendor->getVersion(),
            ],
            $request->getJsonContent()
        );
    }

    public function testWillUpdateVendor(): void
    {
        $vendor = $this->vendorsRepository->create(self::EXAMPLE_VENDORS[0]);

        self::assertEquals(
            array_merge(['id' => $vendor->getId()], self::EXAMPLE_VENDORS[0]),
            [
                'id' => $vendor->getId(),
                'lib_name' => $vendor->getLibName(),
                'lib_url' => $vendor->getLibUrl(),
                'description' => $vendor->getDescription(),
                'environment' => $vendor->getEnvironment(),
                'version' => $vendor->getVersion(),
            ],
        );

        $request = $this->putRequest(
            '/api/example/vendors/' . $vendor->getId(),
            self::EXAMPLE_VENDORS[2]
        );

        self::assertEquals(200, $request->getStatusCode());

        self::assertEquals(
            array_merge(['id' => $vendor->getId()], self::EXAMPLE_VENDORS[2]),
            $request->getJsonContent()
        );
    }

    public function testWillDeleteVendor(): void
    {
        $vendor = $this->vendorsRepository->create(self::EXAMPLE_VENDORS[0]);

        $request = $this->deleteRequest('/api/example/vendors/' . $vendor->getId());

        self::assertEquals(204, $request->getStatusCode());

        self::assertEmpty($request->getContent());
    }

    public function testWillGetNotFoundError(): void
    {
        $request = $this->getRequest('/api/example/vendors/777');

        self::assertEquals(404, $request->getStatusCode());
        self::assertEquals(
            [
                'code' => 'vendor_not_found',
                'message' => 'Vendor not found.',
            ],
            $request->getJsonContent()
        );
    }

    public function testWillGetValidationError(): void
    {
        $request = $this->postRequest('/api/example/vendors');

        self::assertEquals(400, $request->getStatusCode());

        self::assertEquals(
            [
                'code' => 'invalid_parameters',
                'message' => 'Invalid parameters.',
                'data' => [
                    [
                        'field' => 'lib_name',
                        'code' => 'is_required',
                        'message' => 'Field \'lib_name\' is required.',
                    ],
                    [
                        'field' => 'lib_url',
                        'code' => 'is_required',
                        'message' => 'Field \'lib_url\' is required.',
                    ],
                    [
                        'field' => 'version',
                        'code' => 'is_required',
                        'message' => 'Field \'version\' is required.',
                    ],
                    [
                        'field' => 'environment',
                        'code' => 'is_required',
                        'message' => 'Field \'environment\' is required.',
                    ],
                    [
                        'field' => 'description',
                        'code' => 'is_required',
                        'message' => 'Field \'description\' is required.',
                    ]
                ],
            ],
            $request->getJsonContent()
        );
    }
}
