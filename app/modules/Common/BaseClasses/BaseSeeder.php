<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Db\Adapter\Pdo\AbstractPdo;
use Illuminate\Database\Capsule\Manager as EloquentManager;
use Common\Console;
use Common\Exception\DatabaseException;
use Common\Exception\LogicException;
use Throwable;

abstract class BaseSeeder
{
    protected string $table;
    protected AbstractPdo $db;
    protected EloquentManager $eloquent;

    public function __construct()
    {
        $this->db = $GLOBALS['app']->di->getShared('db');
        $this->eloquent = $GLOBALS['app']->di->getShared('eloquent');
    }

    public function run(): void
    {
        if (empty($this->table)) {
            throw new LogicException('$table name must be specified.');
        }

        try {
            $tableRowsCount = (int)$this->db
                ->query('SELECT COUNT(1) as `count` FROM ' . $this->table)
                ->fetch()['count']
            ;
        } catch (Throwable $throwable) {
            throw new DatabaseException('Table \'' . $this->table . '\' not found or database error happen.');
        }

        if ($tableRowsCount !== 0) {
            echo Console::warning('Table \'' . $this->table . '\' was not seeded because it contains data.');
            return;
        }

        $this->seedTable();

        echo Console::success('Table \'' . $this->table . '\' seeded.');
    }
}
