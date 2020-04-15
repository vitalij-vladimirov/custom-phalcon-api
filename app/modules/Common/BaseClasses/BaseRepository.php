<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Common\Regex;
use Common\Variable;
use Common\Entity\PaginationEntity;
use Common\Exception\LogicException;
use Common\Exception\DatabaseException;
use Common\Exception\ForbiddenException;
use Throwable;

abstract class BaseRepository
{
    protected const DEFAULT_ORDER_BY = 'id';
    protected const ORDER_ASC = 'ASC';
    protected const ORDER_DESC = 'DESC';

    protected bool $allowDeleteMany = false;
    protected bool $allowUpdateMany = false;

    private const QUERY_BUILDER_OPERATORS = [
        '=', '!=', '<>', '<', '>', '<=', '>=', '!<', '!>', 'LIKE', 'NOT LIKE', 'NOT'
    ];

    protected BaseModel $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract protected function setModel(): void;

    /**
     * @param string[] $columns
     *
     * @return Collection|BaseModel[]
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model::get($columns);
    }

    /**
     * @param array $credentials
     * @param string[] $columns
     *
     * @return EloquentBuilder|BaseModel|null
     * @throws LogicException
     */
    public function first(array $credentials = [], array $columns = ['*']): ?BaseModel
    {
        return $this
            ->queryBuilder($credentials)
            ->orderBy('id', 'ASC')
            ->first($columns)
        ;
    }

    /**
     * @param array $credentials
     * @param string[] $columns
     *
     * @return EloquentBuilder|BaseModel|null
     * @throws LogicException
     */
    public function firstUpdated(array $credentials = [], array $columns = ['*']): ?BaseModel
    {
        return $this
            ->queryBuilder($credentials)
            ->orderBy('updated_at', 'ASC')
            ->first($columns)
        ;
    }

    /**
     * @param array $credentials
     * @param string[] $columns
     *
     * @return EloquentBuilder|BaseModel|null
     * @throws LogicException
     */
    public function last(array $credentials = [], array $columns = ['*']): ?BaseModel
    {
        return $this
            ->queryBuilder($credentials)
            ->orderBy('id', 'DESC')
            ->first($columns)
        ;
    }

    /**
     * @param array $credentials
     * @param array $columns
     * 
     * @return QueryBuilder|BaseModel|null
     * @throws LogicException
     */
    public function lastUpdated(array $credentials = [], array $columns = ['*']): ?BaseModel
    {
        return $this
            ->queryBuilder($credentials)
            ->orderBy('updated_by', 'ASC')
            ->first($columns)
        ;
    }

    /**
     * @param int $id
     * @param string[] $columns
     * 
     * @return QueryBuilder|BaseModel|null
     */
    public function findOneById(int $id, array $columns = ['*']): ?BaseModel
    {
        return $this->model::whereId($id)->first($columns);
    }

    /**
     * @param array $credentials
     * @param array $columns
     * 
     * @return QueryBuilder|BaseModel|null
     * @throws LogicException
     */
    public function findOneByCredentials(array $credentials, array $columns = ['*']): ?BaseModel
    {
        return $this->queryBuilder($credentials)->first($columns);
    }

    /**
     * @param string $column
     * @param string|int|float|array $values
     * @param string[] $columns
     * 
     * @return QueryBuilder|EloquentBuilder|BaseModel|null
     */
    public function findOneBy(string $column, $values, array $columns = ['*']): ?BaseModel
    {
        if (Variable::isArray($values)) {
            return $this->model::whereIn($column, $values)->first($columns);
        }

        return $this->model::where($column, '=', $values)->first($columns);
    }

    /**
     * @param int[] $ids
     * @param int $offset
     * @param int|null $limit
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return Collection|BaseModel[]
     */
    public function findManyByIds(
        array $ids,
        int $offset = 0,
        int $limit = null,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): Collection {
        $query = $this->model::whereIn('id', $ids)
            ->orderBy($orderBy, strtoupper($orderDir))
        ;

        if ($limit !== null) {
            $query
                ->offset($offset)
                ->limit($limit)
            ;
        }

        return $query->get($columns);
    }

    /**
     * @param int $from
     * @param int|null $to
     * @param int $offset
     * @param int|null $limit
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return Collection|BaseModel[]
     */
    public function findManyByIdsRange(
        int $from = 0,
        int $to = null,
        int $offset = 0,
        int $limit = null,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): Collection {
        if ($to === null) {
            $to = $this->model::max('id');
        }

        return $this->findManyBetween('id', $from, $to, $offset, $limit, $orderBy, $orderDir, $columns);
    }

    /**
     * @param string $column
     * @param string|int|float|array $values
     * @param int $offset
     * @param int|null $limit
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return Collection|BaseModel[]
     */
    public function findManyBy(
        string $column,
        $values,
        int $offset = 0,
        int $limit = null,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): Collection {
        if (Variable::isArray($values)) {
            $query = $this->model::whereIn($column, $values);
        } else {
            $query = $this->model::where($column, $values);
        }

        $query->orderBy($orderBy, strtoupper($orderDir));

        if ($limit !== null) {
            $query
                ->offset($offset)
                ->limit($limit)
            ;
        }

        return $query->get($columns);
    }

    /**
     * @param Carbon $from
     * @param Carbon|CarbonInterface|null $to
     * @param int $offset
     * @param int|null $limit
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return Collection|BaseModel[]
     */
    public function findManyCreatedBetween(
        Carbon $from,
        Carbon $to = null,
        int $offset = 0,
        int $limit = null,
        string $orderBy = 'created_at',
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): Collection {
        if ($to === null) {
            $to = Carbon::now();
        }

        return $this->findManyBetween('created_at', $from, $to, $offset, $limit, $orderBy, $orderDir, $columns);
    }

    /**
     * @param Carbon $from
     * @param Carbon|CarbonInterface|null $to
     * @param int $offset
     * @param int|null $limit
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return Collection|BaseModel[]
     */
    public function findManyUpdatedBetween(
        Carbon $from,
        Carbon $to = null,
        int $offset = 0,
        int $limit = null,
        string $orderBy = 'updated_at',
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): Collection {
        if ($to === null) {
            $to = Carbon::now();
        }

        return $this->findManyBetween('updated_at', $from, $to, $offset, $limit, $orderBy, $orderDir, $columns);
    }

    /**
     * @param string $column
     * @param mixed $from
     * @param mixed $to
     * @param int $offset
     * @param int|null $limit
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return Collection|BaseModel[]
     */
    public function findManyBetween(
        string $column,
        $from,
        $to,
        int $offset = 0,
        int $limit = null,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): Collection {
        $query = $this->model::whereBetween($column, [$from, $to])
            ->orderBy($orderBy, strtoupper($orderDir))
        ;

        if ($limit !== null) {
            $query
                ->offset($offset)
                ->limit($limit)
            ;
        }

        return $query->get($columns);
    }

    /**
     * @param array $credentials
     * @param int $offset
     * @param int|null $limit
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return Collection|BaseModel[]
     * @throws LogicException
     */
    public function findManyByCredentials(
        array $credentials,
        int $offset = 0,
        int $limit = null,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): Collection {
        $query = $this
            ->queryBuilder($credentials)
            ->orderBy($orderBy, strtoupper($orderDir))
        ;

        if ($limit !== null) {
            $query
                ->offset($offset)
                ->limit($limit)
            ;
        }

        return $query->get($columns);
    }

    /**
     * @param int $perPage
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return PaginationEntity
     */
    public function paginate(
        int $perPage = 10,
        int $page = 1,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginationEntity {
        $builder = $this->model::where('id', '>', 0);

        return $this->getPaginatedData($builder, $perPage, $page, $orderBy, $orderDir, $columns);
    }

    /**
     * @param string $column
     * @param string|int|float|array $values
     * @param int $perPage
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return PaginationEntity
     */
    public function paginateBy(
        string $column,
        $values,
        int $perPage = 10,
        int $page = 1,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginationEntity {
        if (Variable::isArray($values)) {
            $builder = $this->model::whereIn($column, $values);
        } else {
            $builder = $this->model::where($column, '=', $values);
        }

        return $this->getPaginatedData($builder, $perPage, $page, $orderBy, $orderDir, $columns);
    }

    /**
     * @param int[] $ids
     * @param int $perPage
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return PaginationEntity
     */
    public function paginateByIds(
        array $ids,
        int $perPage = 10,
        int $page = 1,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginationEntity {
        $builder = $this->model::where('id', '>', 0)
            ->whereIn('id', $ids);

        return $this->getPaginatedData($builder, $perPage, $page, $orderBy, $orderDir, $columns);
    }

    /**
     * @param int $from
     * @param int|null $to
     * @param int $perPage
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return PaginationEntity
     */
    public function paginateByIdsRange(
        int $from = 0,
        int $to = null,
        int $perPage = 10,
        int $page = 1,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginationEntity {
        if ($to === null) {
            $to = $this->model::max('id');
        }

        $builder = $this->model::where('id', '>', 0)
            ->whereBetween('id', [$from, $to]);

        return $this->getPaginatedData($builder, $perPage, $page, $orderBy, $orderDir, $columns);
    }

    /**
     * @param array $credentials
     * @param int $perPage
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return PaginationEntity
     * @throws LogicException
     */
    public function paginateByCredentials(
        array $credentials,
        int $perPage = 10,
        int $page = 1,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginationEntity {
        $builder = $this->queryBuilder($credentials);

        return $this->getPaginatedData($builder, $perPage, $page, $orderBy, $orderDir, $columns);
    }

    /**
     * @param string $column
     * @param array $credentials
     *
     * @return int|float|string|mixed
     * @throws LogicException
     */
    public function min(string $column, array $credentials = [])
    {
        return Variable::restoreVariableType(
            $this->queryBuilder($credentials)->min($column)
        );
    }

    /**
     * @param string $column
     * @param array $credentials
     *
     * @return int|float|string|mixed
     * @throws LogicException
     */
    public function max(string $column, array $credentials = [])
    {
        return Variable::restoreVariableType(
            $this->queryBuilder($credentials)->max($column)
        );
    }

    /**
     * @param string $column
     * @param array $credentials
     *
     * @return int|float|mixed
     * @throws LogicException
     */
    public function sum(string $column, array $credentials = [])
    {
        return Variable::restoreVariableType(
            $this->queryBuilder($credentials)->sum($column)
        );
    }

    /**
     * @param string $column
     * @param array $credentials
     *
     * @return int|float|mixed
     * @throws LogicException
     */
    public function average(string $column, array $credentials = [])
    {
        return Variable::restoreVariableType(
            $this->queryBuilder($credentials)->average($column)
        );
    }

    /**
     * @param string $column
     * @param array $credentials
     *
     * @return int|float|mixed
     * @throws LogicException
     */
    public function count(string $column = '*', array $credentials = []): int
    {
        return Variable::restoreVariableType(
            $this->queryBuilder($credentials)->count($column)
        );
    }

    /**
     * @param array $data
     *
     * @return BaseModel
     * @throws ForbiddenException
     */
    public function create(array $data): BaseModel
    {
        $this->validateFieldsCanBeUpdated($data);

        return $this->model::create($data);
    }

    /**
     * @param array $data
     *
     * @return Collection|BaseModel[]
     * @throws ForbiddenException
     */
    public function createMany(array $data): Collection
    {
        $this->validateFieldsCanBeUpdated($data);

        /** @var int|null $firstId */
        $firstId = null;

        /** @var int|null $lastId */
        $lastId = null;

        foreach ($data as $row) {
            $model = $this->create($row);

            if ($firstId === null) {
                $firstId = $model->getId();
            }

            $lastId = $model->getId();
        }

        return $this->findManyByIdsRange($firstId, $lastId);
    }

    /**
     * @param BaseModel $model
     *
     * @return BaseModel
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function saveModel(BaseModel $model): BaseModel
    {
        $this->validateFieldsCanBeUpdated($model->toArray());

        try {
            $model->save();
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }

        return $model;
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return BaseModel
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function updateOneById(int $id, array $data): BaseModel
    {
        $this->validateFieldsCanBeUpdated($data);

        try {
            $model = $this->model::findOrFail($id);

            $model->update($data);

            return $model;
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param string $column
     * @param string|int|float $value
     * @param array $data
     *
     * @return EloquentBuilder|BaseModel
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function updateOneBy(string $column, $value, array $data): BaseModel
    {
        $this->validateFieldsCanBeUpdated($data);

        try {
            $model = $this->model::where($column, '=', $value)
                ->firstOrFail()
            ;

            $model->update($data);

            return $model;
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param array $credentials
     * @param array $data
     *
     * @return BaseModel
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function updateOneByCredentials(array $credentials, array $data = []): BaseModel
    {
        $this->validateFieldsCanBeUpdated($data);

        try {
            $model = $this->findOneByCredentials($credentials);

            $model->update($data);

            return $model;
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param int[] $ids
     * @param array $data
     * @param int $limit
     *
     * @return Collection|BaseModel[]
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function updateManyByIds(array $ids, array $data, int $limit = 1000): Collection
    {
        $this->validateFieldsCanBeUpdated($data);
        $this->validateUpdateMany();

        try {
            $models = $this->model::whereIn('id', $ids)->limit($limit);

            $models->update($data);

            return $models->get();
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param int $from
     * @param int $to
     * @param array $data
     * @param int $limit
     *
     * @return Collection|BaseModel[]
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function updateManyByIdsRange(int $from, int $to, array $data, int $limit = 1000): Collection
    {
        $this->validateFieldsCanBeUpdated($data);
        $this->validateUpdateMany();

        try {
            $models = $this->model::whereBetween('id', [$from, $to])->limit($limit);
            $models->update($data);

            return $models->get();
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param string $column
     * @param string|int|float|array $values
     * @param array $data
     * @param int $limit
     *
     * @return Collection|BaseModel[]
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function updateManyBy(string $column, $values, array $data, int $limit = 1000): Collection
    {
        $this->validateFieldsCanBeUpdated($data);
        $this->validateUpdateMany();

        try {
            if (Variable::isArray($values)) {
                $models = $this->model::whereIn($column, $values)->limit($limit);
            } else {
                $models = $this->model::where($column, '=', $values)->limit($limit);
            }

            $ids = $this->getIds($models->get(['id']));

            $models->update($data);

            return $this->findManyByIds($ids);
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param array $credentials
     * @param array $data
     * @param int $limit
     *
     * @return Collection|BaseModel[]
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function updateManyByCredentials(array $credentials, array $data = [], int $limit = 1000): Collection
    {
        $this->validateFieldsCanBeUpdated($data);
        $this->validateUpdateMany();

        try {
            $models = $this->queryBuilder($credentials)->limit($limit);

            $ids = $this->getIds($models->get(['id']));

            $models->update($data);

            return $this->findManyByIds($ids);
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param BaseModel $model
     *
     * @return bool
     * @throws DatabaseException
     */
    public function deleteModel(BaseModel $model): bool
    {
        try {
            return $model->delete();
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws DatabaseException
     */
    public function deleteOneById(int $id): bool
    {
        try {
            return $this->model::findOrFail($id)->delete();
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param string $column
     * @param string|int|float $value
     *
     * @return bool
     * @throws DatabaseException
     */
    public function deleteOneBy(string $column, $value): bool
    {
        try {
            return $this->model::where($column, '=', $value)
                ->firstOrFail()
                ->delete()
            ;
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param array $credentials
     *
     * @return bool
     * @throws DatabaseException
     */
    public function deleteOneByCredentials(array $credentials): bool
    {
        try {
            return $this->queryBuilder($credentials)
                ->firstOrFail()
                ->delete()
            ;
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param array $ids
     * @param int $limit
     *
     * @return int
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function deleteManyByIds(array $ids, int $limit = 1000): int
    {
        $this->validateDeleteMany();

        try {
            $models = $this->model::whereIn('id', $ids)->limit($limit);

            $total = $models->count();

            $models->delete();

            return $total;
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param string $column
     * @param string|int|float|array $values
     * @param int $limit
     *
     * @return int
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function deleteManyBy(string $column, $values, int $limit = 1000): int
    {
        $this->validateDeleteMany();

        try {
            if (Variable::isArray($values)) {
                $models = $this->model::whereIn($column, $values)->limit($limit);
            } else {
                $models = $this->model::where($column, '=', $values)->limit($limit);
            }

            $total = $models->count();

            $models->delete();

            return $total;
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param int $from
     * @param int $to
     * @param int $limit
     *
     * @return int
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function deleteManyByIdsRange(int $from, int $to, int $limit = 1000): int
    {
        $this->validateDeleteMany();

        try {
            $models = $this->model::whereBetween('id', [$from, $to])->limit($limit);

            $total = $models->count();

            $models->delete();

            return $total;
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param array $credentials
     * @param int $limit
     *
     * @return int
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function deleteManyByCredentials(array $credentials, int $limit = 1000): int
    {
        $this->validateDeleteMany();

        try {
            $models = $this->queryBuilder($credentials)->limit($limit);

            $total = $models->count();

            $models->delete();

            return $total;
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * example: $credentials = [
     *              'id <=' => 20,              // $key contains $field name and operator
     *              'id <>' => 15,              // $key contains $field name and operator
     *              'name' => 'John Doe',       // operator is set to '='
     *              'address' => null,          // IS NULL
     *              'hour' => [10, 11, 15],     // IN
     *              'minute NOT' => [15, 30],   // NOT IN
     *              'second NOT' => null,       // IS NOT NULL
     *              'something LIKE' => '%text' // LIKE
     *          ];
     *
     * result:  WHERE id <= 20
     *              AND id <> 15
     *              AND name = 'John Doe'
     *              AND address IS NULL
     *              AND hour IN (10, 11, 15)
     *              AND minute NOT IN (15, 30, 45)
     *              AND second IS NOT NULL
     *              AND something LIKE '%text'
     *
     * @param array $credentials
     * @return QueryBuilder|EloquentBuilder|BaseModel
     * @throws LogicException
     */
    protected function queryBuilder(array $credentials)
    {
        $builder = $this->model::where('id', '>', 0);
        if (count($credentials) === 0) {
            return $builder;
        }

        foreach ($credentials as $key => $value) {
            $key = str_replace(['   ', '  '], '', trim($key));
            $operator = '=';

            if (Regex::isValidPattern(
                $key,
                '/^[a-zA-Z0-9_]{1,} (' . implode('|', self::QUERY_BUILDER_OPERATORS) . ')$/'
            )) {
                [$key, $operator] = explode(' ', $key);
            }

            if (!Regex::isValidPattern($key, '/[a-zA-Z0-9_]/')) {
                throw new LogicException('Bad key `' . $key . '`.');
            }

            if ($operator === 'NOT') {
                if (empty($value)) {
                    $builder->whereNotNull($key);
                    continue;
                }

                if (Variable::isArray($value)) {
                    $builder->whereNotIn($key, $value);
                    continue;
                }

                throw new LogicException('Wrong condition NOT with key `' . $key . '`.');
            }

            if (empty($value)) {
                $builder->whereNull($key);
                continue;
            }

            if (Variable::isArray($value)) {
                $builder->whereIn($key, $value);
                continue;
            }

            $builder->where($key, $operator, $value);
        }

        return $builder;
    }

    /**
     * @param QueryBuilder|EloquentBuilder $builder
     * @param int $perPage
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param array $columns
     *
     * @return PaginationEntity
     */
    protected function getPaginatedData(
        $builder,
        int $perPage,
        int $page,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginationEntity {
        $total = $builder->count();

        $data = $builder
            ->offset($perPage * $page - $perPage)
            ->limit($perPage)
            ->orderBy($orderBy, $orderDir)
            ->get($columns)
        ;

        return (new PaginationEntity())
            ->setTotalResults($total)
            ->setTotalPages((int)ceil($total / $perPage))
            ->setCurrentPage($page)
            ->setResultsPerPage($perPage)
            ->setData($data)
            ;
    }

    /**
     * @param Collection $models
     *
     * @return int[]
     */
    private function getIds(Collection $models): array
    {
        $ids = [];

        foreach ($models as $model) {
            $ids[] = $model->getId();
        }

        return $ids;
    }

    /**
     * @param array $data
     *
     * @throws ForbiddenException
     */
    private function validateFieldsCanBeUpdated(array $data): void
    {
        if (count($data) >= 1 && Variable::isArray($data[0])) {
            foreach ($data as $row) {
                if (isset($row['id']) || isset($row['created_at']) || isset($row['updated_at'])) {
                    throw new ForbiddenException(
                        'Fields `id`, `created_at` and `updated_at` can not be updated manually.'
                    );
                }
            }
        }

        if (isset($data['id']) || isset($data['created_at']) || isset($data['updated_at'])) {
            throw new ForbiddenException(
                'Fields `id`, `created_at` and `updated_at` can not be updated manually.'
            );
        }
    }

    /**
     * @throws ForbiddenException
     */
    private function validateUpdateMany(): void
    {
        if ($this->allowUpdateMany === false) {
            throw new ForbiddenException('Updating Many fields with single action is disabled.');
        }
    }

    /**
     * @throws ForbiddenException
     */
    private function validateDeleteMany(): void
    {
        if ($this->allowDeleteMany === false) {
            throw new ForbiddenException('Deleting Many fields with single action is disabled.');
        }
    }
}
