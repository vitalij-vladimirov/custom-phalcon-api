<?php
declare(strict_types=1);

namespace Common\BaseClass;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Common\Regex;
use Common\Variable;
use Common\Entity\PaginatedResult;
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

    private const FORBIDDEN_TO_UPDATE_FIELDS = ['id', 'created_at', 'updated_at'];

    public function __construct()
    {
        $this->setModel();
    }

    abstract protected function setModel(): void;

    /**
     * @param string[] $columns
     *
     * @return Collection|BaseModel[]
     * @throws LogicException
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->queryBuilder()->get($columns);
    }

    /**
     * @param array $credentials
     * @param string[] $columns
     *
     * @return Builder|BaseModel|null
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
     * @return Builder|BaseModel|null
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
     * @return Builder|BaseModel|null
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
     * @return Builder|BaseModel|null
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
     * @return Builder|BaseModel|null
     * @throws LogicException
     */
    public function findOneById(int $id, array $columns = ['*']): ?BaseModel
    {
        return $this->queryBuilder()->where('id', '=', $id)->first($columns);
    }

    /**
     * @param array $credentials
     * @param array $columns
     *
     * @return Builder|BaseModel|null
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
     * @return Builder|BaseModel|null
     * @throws LogicException
     */
    public function findOneBy(string $column, $values, array $columns = ['*']): ?BaseModel
    {
        if (Variable::isArray($values)) {
            return $this->queryBuilder()->whereIn($column, $values)->first($columns);
        }

        return $this->queryBuilder()->where($column, '=', $values)->first($columns);
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
     * @throws LogicException
     */
    public function findManyByIds(
        array $ids,
        int $offset = 0,
        int $limit = null,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): Collection {
        $query = $this->queryBuilder()->whereIn('id', $ids)
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
     * @throws LogicException
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
            $to = $this->queryBuilder()->max('id');
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
     * @throws LogicException
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
            $query = $this->queryBuilder()->whereIn($column, $values);
        } else {
            $query = $this->queryBuilder()->where($column, '=', $values);
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
     * @throws LogicException
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
     * @throws LogicException
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
     * @throws LogicException
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
        $query = $this->queryBuilder()->whereBetween($column, [$from, $to])
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
     * @param int $limit
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return PaginatedResult
     * @throws LogicException
     */
    public function paginate(
        int $limit = 10,
        int $page = 1,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginatedResult {
        return $this->getPaginatedData($this->queryBuilder(), $limit, $page, $orderBy, $orderDir, $columns);
    }

    /**
     * @param string $column
     * @param string|int|float|array $values
     * @param int $limit
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return PaginatedResult
     * @throws LogicException
     */
    public function paginateBy(
        string $column,
        $values,
        int $limit = 10,
        int $page = 1,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginatedResult {
        if (Variable::isArray($values)) {
            $builder = $this->queryBuilder()->whereIn($column, $values);
        } else {
            $builder = $this->queryBuilder()->where($column, '=', $values);
        }

        return $this->getPaginatedData($builder, $limit, $page, $orderBy, $orderDir, $columns);
    }

    /**
     * @param int[] $ids
     * @param int $limit
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return PaginatedResult
     * @throws LogicException
     */
    public function paginateByIds(
        array $ids,
        int $limit = 10,
        int $page = 1,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginatedResult {
        $builder = $this->queryBuilder()->whereIn('id', $ids);

        return $this->getPaginatedData($builder, $limit, $page, $orderBy, $orderDir, $columns);
    }

    /**
     * @param int $from
     * @param int|null $to
     * @param int $limit
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return PaginatedResult
     * @throws LogicException
     */
    public function paginateByIdsRange(
        int $from = 0,
        int $to = null,
        int $limit = 10,
        int $page = 1,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginatedResult {
        if ($to === null) {
            $to = $this->queryBuilder()->max('id');
        }

        $builder = $this->queryBuilder()->whereBetween('id', [$from, $to]);

        return $this->getPaginatedData($builder, $limit, $page, $orderBy, $orderDir, $columns);
    }

    /**
     * @param array $credentials
     * @param int $limit
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param string[] $columns
     *
     * @return PaginatedResult
     * @throws LogicException
     */
    public function paginateByCredentials(
        array $credentials,
        int $limit = 10,
        int $page = 1,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginatedResult {
        $builder = $this->queryBuilder($credentials);

        return $this->getPaginatedData($builder, $limit, $page, $orderBy, $orderDir, $columns);
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
     * @throws LogicException
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
    public function createModel(BaseModel $model): BaseModel
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
            return $this->queryBuilder()->findOrFail($id)->update($data);
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }
    }

    /**
     * @param string $column
     * @param string|int|float $value
     * @param array $data
     *
     * @return Builder|BaseModel
     * @throws DatabaseException
     * @throws ForbiddenException
     */
    public function updateOneBy(string $column, $value, array $data): BaseModel
    {
        $this->validateFieldsCanBeUpdated($data);

        try {
            $model = $this->queryBuilder()
                ->where($column, '=', $value)
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
            return  ($this->findOneByCredentials($credentials))->update($data);
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
        $this->validateUpdateMany();
        $this->validateFieldsCanBeUpdated($data);

        try {
            $models = $this->queryBuilder()
                ->whereIn('id', $ids)
                ->limit($limit)
            ;

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
        $this->validateUpdateMany();
        $this->validateFieldsCanBeUpdated($data);

        try {
            $models = $this->queryBuilder()
                ->whereBetween('id', [$from, $to])
                ->limit($limit)
            ;

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
        $this->validateUpdateMany();
        $this->validateFieldsCanBeUpdated($data);

        try {
            if (Variable::isArray($values)) {
                $models = $this->queryBuilder()
                    ->whereIn($column, $values)
                    ->limit($limit)
                ;
            } else {
                $models = $this->queryBuilder()
                    ->where($column, '=', $values)
                    ->limit($limit)
                ;
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
        $this->validateUpdateMany();
        $this->validateFieldsCanBeUpdated($data);

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
     * @return BaseModel
     * @throws DatabaseException
     */
    public function updateModel(BaseModel $model): BaseModel
    {
        try {
            $model->setHidden(self::FORBIDDEN_TO_UPDATE_FIELDS);
            $model->update($model->toArray());
        } catch (Throwable $throwable) {
            throw new DatabaseException($throwable->getMessage());
        }

        return $model;
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
            return $this->queryBuilder()->findOrFail($id)->delete();
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
            return $this->queryBuilder()
                ->where($column, '=', $value)
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
            $models = $this->queryBuilder()
                ->whereIn('id', $ids)
                ->limit($limit)
            ;

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
                $models = $this->queryBuilder()
                    ->whereIn($column, $values)
                    ->limit($limit)
                ;
            } else {
                $models = $this->queryBuilder()
                    ->where($column, '=', $values)
                    ->limit($limit)
                ;
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
            $models = $this->queryBuilder()
                ->whereBetween('id', [$from, $to])
                ->limit($limit)
            ;

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
     *
     * @return Builder
     * @throws LogicException
     */
    protected function queryBuilder(array $credentials = []): Builder
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
     * @param Builder $builder
     * @param int $limit
     * @param int $page
     * @param string $orderBy
     * @param string $orderDir
     * @param array $columns
     *
     * @return PaginatedResult
     */
    protected function getPaginatedData(
        $builder,
        int $limit,
        int $page,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $orderDir = self::ORDER_ASC,
        array $columns = ['*']
    ): PaginatedResult {
        $total = $builder->count();

        $data = $builder
            ->offset($limit * $page - $limit)
            ->limit($limit)
            ->orderBy($orderBy, $orderDir)
            ->get($columns)
        ;

        return (new PaginatedResult())
            ->setTotalResults($total)
            ->setTotalPages((int)ceil($total / $limit))
            ->setCurrentPage($page)
            ->setLimit($limit)
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
        if (count($data) >= 1 && isset($data[0]) && Variable::isArray($data[0])) {
            foreach ($data as $row) {
                foreach (self::FORBIDDEN_TO_UPDATE_FIELDS as $forbiddenToUpdateField) {
                    if (isset($row[$forbiddenToUpdateField])) {
                        throw new ForbiddenException(
                            'Field `' . $forbiddenToUpdateField . '` can not be updated manually.'
                        );
                    }
                }
            }
        }

        foreach (self::FORBIDDEN_TO_UPDATE_FIELDS as $forbiddenToUpdateField) {
            if (isset($data[$forbiddenToUpdateField])) {
                throw new ForbiddenException(
                    'Field `' . $forbiddenToUpdateField . '` can not be updated manually.'
                );
            }
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
