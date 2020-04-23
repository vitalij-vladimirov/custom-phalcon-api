<?php
declare(strict_types=1);

namespace Example\Repository;

use Common\BaseClass\BaseRepository;
use Common\Entity\PaginatedResult;
use Example\Entity\VendorFilter;
use Example\Model\VendorModel;

class VendorsRepository extends BaseRepository
{
    protected function setModel(): void
    {
        $this->model = new VendorModel();
    }

    /**
     * @param VendorFilter $filter
     * @param int $limit
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return PaginatedResult
     */
    public function paginateByFilter(
        VendorFilter $filter,
        int $limit = 10,
        int $page = 1,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginatedResult {
        $builder = $this->model::where('id', '>', 0);

        if ($filter->getEnvironment() !== null) {
            $builder->where('environment', $filter->getEnvironment());
        }

        if ($filter->getSearch() !== null) {
            $builder->where(
                function ($query) use ($filter) {
                    $query
                        ->where('lib_name', 'LIKE', '%' . $filter->getSearch() . '%')
                        ->orWhere('lib_url', 'LIKE', '%' . $filter->getSearch() . '%')
                        ->orWhere('description', 'LIKE', '%' . $filter->getSearch() . '%')
                    ;
                }
            );
        }

        return $this->getPaginatedData($builder, $limit, $page, $orderBy, $orderDir, $columns);
    }
}
