<?php
declare(strict_types=1);

namespace Common\Entity;

use Common\BaseClass\BaseEntity;

class PaginationFilter extends BaseEntity
{
    private ?int $limit = null;
    private ?int $page = null;
    private ?string $orderBy = null;
    private ?string $orderDirection = null;
    private $filter;

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): PaginationFilter
    {
        $this->limit = $limit;
        return $this;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): PaginationFilter
    {
        $this->page = $page;
        return $this;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    public function setOrderBy(?string $orderBy): PaginationFilter
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    public function setOrderDirection(?string $orderDirection): PaginationFilter
    {
        $this->orderDirection = $orderDirection;
        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function setFilter($filter): PaginationFilter
    {
        $this->filter = $filter;
        return $this;
    }
}
