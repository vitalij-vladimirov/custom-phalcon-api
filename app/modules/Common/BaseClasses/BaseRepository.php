<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Common\Exception\LogicException;
use Common\Regex;
use Common\Variable;
use Illuminate\Database\Eloquent\Builder;
use Phalcon\Di\Injectable;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository extends Injectable
{
    private const DEFAULT_ORDER_BY = 'id';
    private const ORDER_ASC = 'ASC';
    private const ORDER_DESC = 'DESC';

    private const QUERY_BUILDER_OPERATORS = ['=', '!=', '<>', '<', '>', '<=', '>=', '!<', '!>', 'LIKE', 'NOT LIKE', 'NOT'];

    protected BaseModel $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract protected function setModel(): void;

    public function findOneById(int $id): ?BaseModel
    {
        return $this->model::firstWhere('id', '=', $id);
    }

    /**
     * @param array $ids
     * @return Collection|Builder|BaseModel[]
     */
    public function findManyByIds(array $ids): Collection
    {
        return $this->model::whereIn('id', $ids)->get();
    }

    /**
     * @param int|null $from
     * @param int|null $to
     * @return Collection|Builder|BaseModel[]
     */
    public function findManyByIdsRange(int $from = 0, int $to = null): Collection
    {
        if ($to === null) {
            $to = $this->model::max('id');
        }

        return $this->findManyBetween('id', $from, $to);
    }

    public function findOne(string $column, $value): ?BaseModel
    {
        return $this->model::firstWhere($column, '=', $value);
    }

    /**
     * @param Carbon $from
     * @param Carbon|CarbonInterface|null $to
     * @param int|null $limit
     * @param string $orderBy
     * @param string $order
     * @return Collection|Builder|BaseModel[]
     */
    public function findManyCreatedBetween(
        Carbon $from,
        Carbon $to = null,
        int $limit = null,
        string $orderBy = 'created_at',
        string $order = self::ORDER_DESC
    ): Collection {
        if ($to === null) {
            $to = Carbon::now();
        }

        return $this->findManyBetween('created_at', $from, $to, $limit, $orderBy, $order);
    }

    /**
     * @param Carbon $from
     * @param Carbon|CarbonInterface|null $to
     * @param int|null $limit
     * @param string $orderBy
     * @param string $order
     * @return Collection|Builder|BaseModel[]
     */
    public function findManyUpdatedBetween(
        Carbon $from,
        Carbon $to = null,
        int $limit = null,
        string $orderBy = 'updated_at',
        string $order = self::ORDER_DESC
    ): Collection {
        if ($to === null) {
            $to = Carbon::now();
        }

        return $this->findManyBetween('updated_at', $from, $to, $limit, $orderBy, $order);
    }

    /**
     * @param string $column
     * @param mixed $from
     * @param mixed $to
     * @param int|null $limit
     * @param string $orderBy
     * @param string $order
     * @return Collection|Builder|BaseModel[]
     */
    public function findManyBetween(
        string $column,
        $from,
        $to,
        int $limit = null,
        string $orderBy = self::DEFAULT_ORDER_BY,
        string $order = self::ORDER_ASC
    ): Collection {
        return $this->model::whereBetween($column, [$from, $to])
            ->orderBy($orderBy, strtoupper($order))
            ->limit($limit)
            ->get()
        ;
    }

    public function min($column, array $conditions = []): int
    {
        if (count($conditions) === 0) {
            return $this->model::min($column);
        }

        return $this->queryBuilder($conditions)->min($column);
    }

//    public function createModel(BaseModel $model): BaseModel
//    {
//        return $this->model::create();
//    }

    /**
     * $conditions = [
     *     'id <=' => 20,           // $key contains $field name and operator
     *     'id <>' => 15,           // $key contains $field name and operator
     *     'name' => 'John Doe'     // operator is set to '='
     *     'hour' => [10, 11, 15]   // operator is set to 'IN',
     *     'minute NOT' => [15, 30] // operator is set to 'NOT IN',
     * ]
     * result: WHERE id <= 20
     *             AND id <> 15
     *             AND name = 'John Doe'
     *             AND hour IN (10, 11, 15)
     *             AND minute NOT IN (15, 30, 45)
     *
     * @param array $conditions
     * @return Builder
     * @throws LogicException
     */
    private function queryBuilder(array $conditions): Builder
    {
        $builder = $this->model::where('id', '>', 0);
        if (count($conditions) === 0) {
            return $builder;
        }

        foreach ($conditions as $key => $value) {
            $key = trim($key);
            $operator = '=';

            if (Regex::isValidPattern(
                $key,
                '/^[a-zA-Z0-9_]{1,}+[ ]+(' . implode('|', self::QUERY_BUILDER_OPERATORS) . ')$/'
            )) {
                $key = str_replace(['   ', '  '], '', trim($key));
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
}
