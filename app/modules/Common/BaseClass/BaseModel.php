<?php
declare(strict_types=1);

namespace Common\BaseClass;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Collection;
use Closure;
use Common\Variable;
use Common\Regex;
use Common\Text;

/**
 * phpcs:disable
 *
 * @property int $id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static QueryBuilder whereId($value)
 * @method static QueryBuilder whereIdNot($value)
 * @method static QueryBuilder whereCreatedAt($value)
 * @method static QueryBuilder whereUpdatedAt($value)
 *
 * @method static QueryBuilder select(array $columns = ['*'])
 * @method static QueryBuilder selectSub($query, $as)
 * @method static QueryBuilder selectRaw($expression, array $bindings = [])
 * @method static QueryBuilder fromSub($query, $as)
 * @method static QueryBuilder fromRaw($expression, $bindings = [])
 * @method static QueryBuilder join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method static QueryBuilder joinWhere($table, $first, $operator, $second, $type = 'inner')
 * @method static QueryBuilder joinSub($query, $as, $first, $operator = null, $second = null, $type = 'inner', $where = false)
 * @method static QueryBuilder leftJoin($table, $first, $operator = null, $second = null)
 * @method static QueryBuilder leftJoinWhere($table, $first, $operator, $second)
 * @method static QueryBuilder leftJoinSub($query, $as, $first, $operator = null, $second = null)
 * @method static QueryBuilder rightJoin($table, $first, $operator = null, $second = null)
 * @method static QueryBuilder rightJoinWhere($table, $first, $operator, $second)
 * @method static QueryBuilder rightJoinSub($query, $as, $first, $operator = null, $second = null)
 * @method static QueryBuilder crossJoin($table, $first = null, $operator = null, $second = null)
 * @method static QueryBuilder newJoinClause(self $parentQuery, $type, $table)
 * @method static QueryBuilder addArrayOfWheres(string $column, string $boolean, $method = 'where')
 * @method static QueryBuilder whereColumn($first, $operator = null, $second = null, string $boolean = 'and')
 * @method static QueryBuilder whereRaw(string $sql, $bindings = [], string $boolean = 'and')
 * @method static QueryBuilder whereIn(string $column, array $values, string $boolean = 'and', $not = false)
 * @method static QueryBuilder whereNotIn(string $column, array $values, string $boolean = 'and')
 * @method static QueryBuilder whereIntegerInRaw(string $column, array $values, string $boolean = 'and', $not = false)
 * @method static QueryBuilder whereIntegerNotInRaw(string $column, $values, string $boolean = 'and')
 * @method static QueryBuilder whereNull($columns, string $boolean = 'and', $not = false)
 * @method static QueryBuilder whereNotNull($columns, string $boolean = 'and')
 * @method static QueryBuilder whereBetween(string $column, array $values, string $boolean = 'and', $not = false)
 * @method static QueryBuilder whereNotBetween(string $column, array $values, string $boolean = 'and')
 * @method static QueryBuilder whereDate(string $column, $operator, $value = null, string $boolean = 'and')
 * @method static QueryBuilder whereTime(string $column, $operator, $value = null, string $boolean = 'and')
 * @method static QueryBuilder whereDay(string $column, $operator, $value = null, string $boolean = 'and')
 * @method static QueryBuilder whereMonth(string $column, $operator, $value = null, string $boolean = 'and')
 * @method static QueryBuilder whereYear(string $column, $operator, $value = null, string $boolean = 'and')
 * @method static QueryBuilder whereSub(string $column, $operator, Closure $callback, string $boolean)
 * @method static QueryBuilder whereExists(Closure $callback, string $boolean = 'and', $not = false)
 * @method static QueryBuilder whereNotExists(Closure $callback, string $boolean = 'and')
 * @method static QueryBuilder addWhereExistsQuery(self $query, string $boolean = 'and', $not = false)
 * @method static QueryBuilder whereRowValues($columns, $operator, $values, string $boolean = 'and')
 * @method static QueryBuilder whereJsonContains(string $column, $value, string $boolean = 'and', $not = false)
 * @method static QueryBuilder whereJsonDoesntContain(string $column, $value, string $boolean = 'and')
 * @method static QueryBuilder whereJsonLength(string $column, $operator, $value = null, string $boolean = 'and')
 * @method static QueryBuilder groupBy(...$groups)
 * @method static QueryBuilder groupByRaw($sql, array $bindings = [])
 * @method static QueryBuilder having(string $column, $operator = null, $value = null, string $boolean = 'and')
 * @method static QueryBuilder havingBetween(string $column, array $values, string $boolean = 'and', $not = false)
 * @method static QueryBuilder havingRaw($sql, array $bindings = [], string $boolean = 'and')
 * @method static QueryBuilder orderBy(string $column, $direction = 'asc')
 * @method static QueryBuilder latest(string $column = 'created_at')
 * @method static QueryBuilder oldest(string $column = 'created_at')
 * @method static QueryBuilder inRandomOrder($seed = '')
 * @method static QueryBuilder skip($value)
 * @method static QueryBuilder limit($value)
 * @method static QueryBuilder forPage($page, $perPage = 15)
 * @method static QueryBuilder forPageBeforeId($perPage = 15, $lastId = 0, $column = 'id')
 * @method static QueryBuilder forPageAfterId($perPage = 15, $lastId = 0, $column = 'id')
 * @method static QueryBuilder removeExistingOrdersFor($column)
 *
 * @method static EloquentBuilder query()
 * @method static EloquentBuilder where(string $column, $operator = null, $value = null, string $boolean = 'and')
 * @method static EloquentBuilder with($relations)
 * @method static EloquentBuilder without($relations)
 *
 * @method static BaseModel firstWhere(string $column, $operator = null, $value = null, string $boolean = 'and')
 * @method static BaseModel find(int $id, array $columns = ['*'])
 * @method static BaseModel findOrFail(int $id, array $columns = ['*'])
 * @method static BaseModel findOrNew(int $id, array $columns = ['*'])
 * @method static BaseModel first(array $columns = ['*'])
 * @method static BaseModel firstOrNew(array $attributes = [], array $values = [])
 * @method static BaseModel firstOrCreate(array $attributes, array $values = [])
 * @method static BaseModel firstOrFail(array $columns = ['*'])
 * @method static BaseModel create(array $attributes = [])
 * @method static BaseModel forceCreate(array $attributes)
 * @method static BaseModel insert(array $values)
 * @method static BaseModel insertOrIgnore(array $values)
 * @method static BaseModel update(array $values)
 * @method static BaseModel updateOrInsert($attributes, array $values = [])
 *
 * @method static Collection|BaseModel[] all()
 * @method static Collection|BaseModel[] fromQuery($query, array $bindings = [])
 * @method static Collection|BaseModel[] findMany(array $ids, array $columns = ['*'])
 * @method static Collection|BaseModel[] get(array $columns = ['*'])
 * @method static Collection|BaseModel[] pluck(string $column, $key = null)
 *
 * @method static LengthAwarePaginator paginate(int $perPage = null, array $columns = ['*'], string $pageName = 'page', int $page = null)
 * @method static Paginator simplePaginate(int $perPage = null, array $columns = ['*'], string $pageName = 'page', int $page = null)
 *
 * @method static bool exists()
 * @method static bool doesntExist()
 * @method static int count($columns = '*')
 * @method static mixed min($column)
 * @method static mixed max($column)
 * @method static float sum($column)
 * @method static string avg($column)
 * @method static string average($column)
 * @method static int insertGetId(array $values, $sequence = null)
 *
 * @method static string toSql()
 * @method static dd()
 *
 * @mixin Model
 *
 * phpcs:enable
 */
abstract class BaseModel extends Model
{
    public const DATE_FORMAT = 'Y-m-d';
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    public const TIMESTAMP_FORMAT = 'U';

    public $timestamps = true;

    protected $dateFormat = self::DATE_TIME_FORMAT;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($this->table === null) {
            $this->setTableName();
        }

        $this->casts['created_at'] = 'datetime:' . self::TIMESTAMP_FORMAT;
        $this->casts['updated_at'] = 'datetime:' . self::TIMESTAMP_FORMAT;
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->created_at ?? null;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at ?? null;
    }

    public function toArray(): array
    {
        return Variable::restoreArrayTypes(
            parent::toArray(),
            true,
            true,
            false,
            ['created_at', 'updated_at']
        );
    }

    private function setTableName(): void
    {
        $modelClass = last(explode('\\', get_class($this)));
        if (Regex::isValidPattern($modelClass, '/(Model)$/')) {
            $modelClass = substr($modelClass, 0, -5);
        }

        $this->table = Text::toSnakeCase($modelClass);
    }
}
