<?php
declare(strict_types=1);

namespace Example\Route;

use Common\ApiException\NotFoundApiException;
use Common\ApiException\UnauthorizedApiException;
use Common\BaseClasses\BaseRoutes;
use Common\Entity\Route;
use Documentation\Entity\RouteDoc;
use Example\Controller\VendorController;
use Example\Mapper\VendorFilteredRequestMapper;
use Example\Mapper\VendorMapper;
use Example\Mapper\VendorPaginatedResponseMapper;
use Example\Resolver\VendorResolver;
use Example\Validator\VendorValidator;

class VendorsRoutes extends BaseRoutes
{
    protected function routes(): void
    {
        $this->get['/api/example/vendors'] = (new Route())
            ->setController(VendorController::class)
            ->setAction('getVendors')
            ->setPermissions(['example:read'])
            ->setRequestMapper(VendorFilteredRequestMapper::class)
            ->setResponseMapper(VendorPaginatedResponseMapper::class)
            ->setDocumentation(
                (new RouteDoc())
                    ->setSummary('List all vendors')
                    ->setExceptionsList([
                        UnauthorizedApiException::class,
                    ])
            )
        ;

        $this->get['/api/example/vendors/{vendor_id}'] = (new Route())
            ->setController(VendorController::class)
            ->setAction('getVendor')
            ->setPermissions(['example:read'])
            ->setResolvers([
                'vendor_id' => VendorResolver::class,
            ])
            ->setResponseMapper(VendorMapper::class)
            ->setDocumentation(
                (new RouteDoc())
                    ->setSummary('Get vendor information')
                    ->setExceptionsList([
                        UnauthorizedApiException::class,
                        NotFoundApiException::class => [
                            ['code' => 'vendor_not_found', 'message' => 'Vendor not found'],
                        ],
                    ])
            )
        ;

        $this->post['/api/example/vendors'] = (new Route())
            ->setController(VendorController::class)
            ->setAction('createVendor')
            ->setPermissions(['example:create'])
            ->setRequestMapper(VendorMapper::class)
            ->setValidator(VendorValidator::class)
            ->setResponseMapper(VendorMapper::class)
            ->setDocumentation(
                (new RouteDoc())
                    ->setSummary('Create vendor')
                    ->setExceptionsList([
                        UnauthorizedApiException::class,
                    ])
            )
        ;

        $this->put['/api/example/vendors/{vendor_id}'] = (new Route())
            ->setController(VendorController::class)
            ->setAction('updateVendor')
            ->setPermissions(['example:update'])
            ->setRequestMapper(VendorMapper::class)
            ->setValidator(VendorValidator::class)
            ->setResolvers([
                'vendor_id' => VendorResolver::class,
            ])
            ->setResponseMapper(VendorMapper::class)
            ->setDocumentation(
                (new RouteDoc())
                    ->setSummary('Update vendor')
                    ->setExceptionsList([
                        UnauthorizedApiException::class,
                        NotFoundApiException::class => [
                            ['code' => 'vendor_not_found', 'message' => 'Vendor not found'],
                        ],
                    ])
            )
        ;

        $this->delete['/api/example/vendors/{vendor_id}'] = (new Route())
            ->setController(VendorController::class)
            ->setAction('deleteVendor')
            ->setPermissions(['example:delete'])
            ->setResolvers([
                'vendor_id' => VendorResolver::class,
            ])
            ->setDocumentation(
                (new RouteDoc())
                    ->setSummary('Delete vendor')
                    ->setExceptionsList([
                        UnauthorizedApiException::class,
                        NotFoundApiException::class => [
                            ['code' => 'vendor_not_found', 'message' => 'Vendor not found'],
                        ],
                    ])
            )
        ;
    }
}
