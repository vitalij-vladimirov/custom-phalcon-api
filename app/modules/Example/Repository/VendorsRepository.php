<?php
declare(strict_types=1);

namespace Example\Repository;

use Common\BaseClasses\BaseRepository;
use Example\Model\VendorModel;

class VendorsRepository extends BaseRepository
{
    protected function setModel(): void
    {
        $this->model = new VendorModel();
    }
}
