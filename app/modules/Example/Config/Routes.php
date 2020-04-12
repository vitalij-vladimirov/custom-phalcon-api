<?php
declare(strict_types=1);

namespace Example\Config;

use Common\BaseClasses\BaseRoutes;
use Common\Entity\RequestEntity;
use Example\Controller\VendorController;

class Routes extends BaseRoutes
{
    /** @var object|VendorController */
    private object $vendorController;

    public function __construct(RequestEntity $request)
    {
        parent::__construct($request);

        $this->vendorController = $this->inject(VendorController::class);
    }

    public function get()
    {
        return $this->vendorController->getAll();
    }
}
