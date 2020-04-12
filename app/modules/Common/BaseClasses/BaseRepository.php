<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Common\Exception\DatabaseException;
//use Framework\Database\PaginatedResult;
use Phalcon\Di\Injectable;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query\BuilderInterface;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Paginator\Adapter\QueryBuilder;
use Exception;
use Throwable;

// TODO: finish base repository
abstract class BaseRepository extends Injectable
{
    abstract protected function getModelClass(): string;

    protected function createQueryBuilder($alias = null): BuilderInterface
    {
        return $this
            ->modelsManager
            ->createBuilder()
            ->addFrom($this->getModelClass(), $alias)
        ;
    }

    public function create(Model $model): Model
    {
        if (!$model->create()) {
            throw new DatabaseException('Failed to create Model.');
        }

        return $model;
    }

    public function update(Model $model): Model
    {
        if (!$model->update()) {
            throw new DatabaseException('Failed to update Model');
        }

        return $model;
    }

    public function delete(Model $model): bool
    {
        try {
            return $model->delete();
        } catch (Throwable $throwable) {
            throw new DatabaseException('failed to delete');
        }
    }

    public function createOrUpdate(Model $model): Model
    {
        if (!$model->save()) {
            throw new DatabaseException('Failed to save Model.');
        }

        return $model;
    }

//    public function getPaginatedResult(
//        BuilderInterface $queryBuilder,
//        int $startingPage = 1,
//        int $limit = 100,
//        string $orderBy = null,
//        string $orderByDirection = null,
//        string $orderTableAlias = null
//    ) {
//        if ($orderBy !== null) {
//            $orderByStatement = $orderTableAlias !== null ? $orderTableAlias . '.' . $orderBy : $orderBy;
//
//            if ($orderByDirection !== null) {
//                $orderByStatement .= ' ' . $orderByDirection;
//            }
//
//            $queryBuilder->orderBy($orderByStatement);
//        }
//
//        $paginatedQueryBuilder = new QueryBuilder([
//            'builder' => $queryBuilder,
//            'limit' => $limit,
//            'page' => $startingPage,
//        ]);
//
//        $paginationResult = $paginatedQueryBuilder->getPaginate();
//
//        return new PaginatedResult(
//            $paginationResult->total_items,
//            $paginationResult->total_pages,
//            $paginationResult->current,
//            $paginationResult->limit,
//            $paginationResult->items
//        );
//    }

    /**
     * @param array $conditions
     * @param int|null $limit
     * @param string|null $orderBy
     * @return Simple|Model[]
     */
    public function findAll(array $conditions, int $limit = null, string $orderBy = null): Simple
    {
        return $this
            ->buildQueryBuilder($conditions, $limit, $orderBy)
            ->getQuery()
            ->execute()
        ;
    }

    public function deleteAll(array $conditions): bool
    {
        try {
            return $this->findAll($conditions)->delete();
        } catch (Throwable $throwable) {
            throw new DatabaseException('Failed to delete multiple Models.');
        }
    }

    /**
     * @param array $conditions
     * @param int|null $limit
     * @param string|null $orderBy
     * @return Simple|Model[]
     */
    public function findAllAndLock(array $conditions, int $limit = null, string $orderBy = null): Simple
    {
        return $this
            ->buildQueryBuilder($conditions, $limit, $orderBy)
            ->forUpdate(true)
            ->getQuery()
            ->execute()
        ;
    }

    public function findOne(array $conditions, string $orderBy = null): ?Model
    {
        $results = $this
            ->buildQueryBuilder($conditions, 1, $orderBy)
            ->getQuery()
            ->execute()
        ;

        return $results->count() === 0 ? null : $results[0];
    }

    public function count(array $conditions): int
    {
        return $this
            ->buildQueryBuilder($conditions)
            ->getQuery()
            ->execute()
            ->count()
        ;
    }

    public function findOneAndLock(array $conditions): ?Model
    {
        $results = $this
            ->buildQueryBuilder($conditions, 1)
            ->forUpdate(true)
            ->getQuery()
            ->execute()
        ;

        return $results->count() === 0 ? null : $results[0];
    }

//    public function findMaximum(string $column)
//    {
//        $result = $this->createQueryBuilder()
//            ->columns(['maximum' => sprintf('MAX (%s)', $column)])
//            ->getQuery()
//            ->execute()
//            ->getFirst()
//        ;
//
//        return $result->maximum;
//    }

//    public function findMinimum(string $column)
//    {
//        $result = $this->createQueryBuilder()
//            ->columns(['minimum' => sprintf('MIN (%s)', $column)])
//            ->getQuery()
//            ->execute()
//            ->getFirst()
//        ;
//
//        return $result->minimum;
//    }

    public function isUniqueFieldValue($field, $value): bool
    {
        return $this->findOne([$field => $value]) === null;
    }

    private function buildQueryBuilder(
        array $conditions,
        int $limit = null,
        string $orderBy = null
    ): BuilderInterface {
        $queryBuilder = $this->createQueryBuilder();

        $wildCards = [];

        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $wildCards[] = $key . ' IN ({' . $key . ':array})';
            } elseif ($value === null) {
                $wildCards[] = $key . ' IS NULL';
                unset($conditions[$key]);
            } elseif (preg_match('/^(.*)\s([><=!]{1,2})$/', $key, $matches)) {
                $wildCards[] = $matches[1] . ' ' . $matches[2] . ' :' . $matches[1] . ':';
                unset($conditions[$key]);
                $conditions[$matches[1]] = $value;
            } else {
                $wildCards[] = $key . ' = :' . $key . ':';
            }
        }

        $queryBuilder->where(implode(' AND ', $wildCards), $conditions);

        if ($limit !== null) {
            $queryBuilder->limit($limit);
        }

        if ($orderBy !== null) {
            $queryBuilder->orderBy($orderBy);
        }

        return $queryBuilder;
    }
}
