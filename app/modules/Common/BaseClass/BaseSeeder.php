<?php
declare(strict_types=1);

namespace Common\BaseClass;

use Common\Console;
use Common\Service\Injectable;
use Common\Exception\DatabaseException;
use Common\Exception\LogicException;
use Illuminate\Database\Capsule\Manager;
use Throwable;

abstract class BaseSeeder extends Injectable
{
    protected string $table;
    protected Manager $eloquent;

    abstract protected function seedTable(): void;

    public function run(): void
    {
        if (empty($this->table)) {
            throw new LogicException('$table name must be specified.');
        }

        $this->eloquent = $this->di->get('eloquent');

        try {
            if ($this->eloquent::table($this->table)->count() !== 0) {
                echo Console::warning('Table \'' . $this->table . '\' was not seeded because it contains data.');
                return;
            }
        } catch (Throwable $throwable) {
            throw new DatabaseException('Table \'' . $this->table . '\' not found in DB.');
        }

        $this->seedTable();

        echo Console::success('Table \'' . $this->table . '\' seeded.');
    }
}
