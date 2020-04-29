<?php
declare(strict_types=1);

namespace Example\Entity;

use Common\BaseClass\BaseEntity;

class VendorFilter extends BaseEntity
{
    private ?string $environment = null;
    private ?string $search = null;

    public function getEnvironment(): ?string
    {
        return $this->environment;
    }

    public function setEnvironment(?string $environment): VendorFilter
    {
        $this->environment = $environment;
        return $this;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): VendorFilter
    {
        $this->search = $search;
        return $this;
    }
}
