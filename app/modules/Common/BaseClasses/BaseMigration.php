<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\AbstractPdo;
use Phinx\Migration\AbstractMigration;
use Illuminate\Database\Capsule\Manager as EloquentManager;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use Common\Exception\LogicException;
use Common\Interfaces\MigrationCreateInterface;
use Common\Interfaces\MigrationUpdateInterface;

abstract class BaseMigration extends AbstractMigration
{
    protected string $table;
    protected EloquentManager $eloquent;
    protected Builder $schema;
    protected AbstractPdo $db;

    private Config $config;

    public function up(): void
    {
        if (empty($this->table)) {
            throw new LogicException('$table name must be specified.');
        }

        $this->loadGlobalServices();

        if (isset(class_implements($this)[MigrationCreateInterface::class])) {
            if (method_exists($this, 'beforeMigration')) {
                $this->beforeMigration();
            }

            $this->schema->create($this->table, function (Blueprint $table) {
                $table->id();

                $this->createSchema($table);

                $table->timestamp('created_at')
                    ->useCurrent();
                $table->timestamp('updated_at')
                    ->useCurrent();
            });

            $this->correctUpdatedAtField();

            if (method_exists($this, 'afterMigration')) {
                $this->afterMigration();
            }

            return;
        }

        if (isset(class_implements($this)[MigrationUpdateInterface::class])) {
            if (method_exists($this, 'beforeMigration')) {
                $this->beforeMigration();
            }

            $this->schema->table($this->table, function (Blueprint $table) {
                $this->updateSchema($table);
            });

            $this->correctUpdatedAtField();

            if (method_exists($this, 'afterMigration')) {
                $this->afterMigration();
            }

            return;
        }

        throw new LogicException(
            'Migration must implement \'MigrationCreateInterface\' or \'MigrationUpdateInterface\'.'
        );
    }

    public function down(): void
    {
        if (empty($this->table)) {
            throw new LogicException('$table name must be specified.');
        }

        if (isset(class_implements($this)[MigrationCreateInterface::class])) {
            $this->schema->dropIfExists($this->table);

            return;
        }

        if (isset(class_implements($this)[MigrationUpdateInterface::class])) {
            $this->schema->table($this->table, function (Blueprint $table) {
                $this->rollbackSchema($table);
            });

            return;
        }
    }

    protected function init(): void
    {
        $this->loadGlobalServices();
    }

    private function loadGlobalServices(): void
    {
        $this->config = $GLOBALS['app']->di->getShared('config');
        $this->db = $GLOBALS['app']->di->getShared('db');
        $this->eloquent = $GLOBALS['app']->di->getShared('eloquent');
        $this->schema = $this->eloquent::schema();
    }

    /**
     * Updating field `updated_at` to "DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
     * has been tested only with MySql, so I return this method at the start if other DB is used.
     * Update this method in case of using other DB than MySql.
     */
    private function correctUpdatedAtField(): void
    {
        if (strtolower($this->config->database->adapter) !== 'mysql') {
            return;
        }

        $getDefault = 'CURRENT_TIMESTAMP';
        $getExtra = 'DEFAULT_GENERATED on update CURRENT_TIMESTAMP';

        $updatedAt = $this->db->query('
            SHOW COLUMNS
            FROM `' . $this->config->database->dbname . '`.`' . $this->table . '`
            WHERE `field` = \'updated_at\'
        ')->fetch();

        if (!$updatedAt || ($updatedAt['Default'] === $getDefault && $updatedAt['Extra'] === $getExtra)) {
            return;
        }

        $this->db->query('
            ALTER TABLE ' . $this->config->database->dbname . '.' . $this->table . '
            CHANGE COLUMN `updated_at` `updated_at`
            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ')->execute();
    }
}
