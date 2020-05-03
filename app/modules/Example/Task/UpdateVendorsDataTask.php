<?php
declare(strict_types=1);

namespace Example\Task;

use Common\BaseClass\BaseTask;
use Example\Service\VendorManager;

class UpdateVendorsDataTask extends BaseTask
{
    private VendorManager $vendorManager;

    public function __construct(VendorManager $vendorManager)
    {
        $this->vendorManager = $vendorManager;
    }

    public function mainAction(array $params = []): void
    {
        $this->vendorManager->updateVendorsFromComposer();
    }
}
