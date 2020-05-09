<?php
declare(strict_types=1);

namespace Common\BaseMapper;

use Common\Entity\PaginationFilter;
use Common\Interfaces\RequestMapperInterface;

class BasePaginationFilterRequestMapper implements RequestMapperInterface
{
    private RequestMapperInterface $filterMapper;

    public function __construct(RequestMapperInterface $filterMapper)
    {
        $this->filterMapper = $filterMapper;
    }

    public function mapRequestToObject(array $data): PaginationFilter
    {
        $paginationFilter = new PaginationFilter();

        if (isset($data['limit'])) {
            $paginationFilter->setLimit((int)$data['limit']);
        }

        if (isset($data['page'])) {
            $paginationFilter->setPage((int)$data['page']);
        }

        if (isset($data['order_by'])) {
            $paginationFilter->setOrderBy($data['order_by']);
        }

        if (isset($data['order_direction'])) {
            $paginationFilter->setOrderDirection($data['order_direction']);
        }

        $paginationFilter->setFilter(
            $this->filterMapper->mapRequestToObject($this->removePaginationAttributes($data))
        );

        return $paginationFilter;
    }

    public function removePaginationAttributes(array $data): array
    {
        unset($data['limit'], $data['page'], $data['order_by'], $data['order_direction']);

        return $data;
    }
}
