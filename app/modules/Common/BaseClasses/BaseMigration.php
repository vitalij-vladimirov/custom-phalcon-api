<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\AbstractPdo;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use Phinx\Migration\AbstractMigration;
use Common\Exception\LogicException;
use Common\Interfaces\MigrationCreateInterface;
use Common\Interfaces\MigrationUpdateInterface;

abstract class BaseMigration extends AbstractMigration
{
    protected string $table;
    protected Capsule $capsule;
    protected Builder $schema;

    private Config $config;

    protected function init(): void
    {
        $this->config = $GLOBALS['app']->di->getShared('config');

        $this->capsule = new Capsule;
        $this->capsule->addConnection([
            'driver'    => $this->config->database->adapter,
            'host'      => $this->config->database->host,
            'port'      => $this->config->database->port,
            'database'  => $this->config->database->dbname,
            'username'  => $this->config->database->username,
            'password'  => $this->config->database->password,
            'charset'   => $this->config->database->charset,
            'collation' => $this->config->database->collation,
        ]);

        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();
        $this->schema = $this->capsule->schema();
    }

    public function up(): void
    {
        if (empty($this->table)) {
            throw new LogicException('$table name must be specified.');
        }

        if (isset(class_implements($this)[MigrationCreateInterface::class])) {
            $this->schema->create($this->table, function (Blueprint $table) {
                $table->id();

                $this->createSchema($table);

                $table->timestamp('created_at')
                    ->useCurrent();
                $table->timestamp('updated_at')
                    ->useCurrent();
            });

            $this->correctUpdatedAtField();

            return;
        }

        if (isset(class_implements($this)[MigrationUpdateInterface::class])) {
            $this->schema->table($this->table, function (Blueprint $table) {
                $this->updateSchema($table);
            });

            $this->correctUpdatedAtField();

            return;
        }

        throw new LogicException(
            'Migration must implement migration \'create\' or \'update\' interface.'
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

    /**
     * Updating field `updated_at` to "DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
     * has been tested only with MySql, so I return this method at the stat if other DB is used.
     */
    protected function correctUpdatedAtField(): void
    {
        if (strtolower($this->config->database->adapter) !== 'mysql') {
            return;
        }

        $getDefault = 'CURRENT_TIMESTAMP';
        $getExtra = 'DEFAULT_GENERATED on update CURRENT_TIMESTAMP';

        /** @var AbstractPdo $this->config */
        $db = $GLOBALS['app']->di->getShared('db');

        $updatedAt = $db->query('
            SHOW COLUMNS
            FROM ' . $this->config->database->dbname . '.' . $this->table . '
            WHERE `field` = \'updated_at\'
        ')->fetch();

        if (!$updatedAt || ($updatedAt['Default'] === $getDefault && $updatedAt['Extra'] === $getExtra)) {
            return;
        }

        $db->query('
            ALTER TABLE ' . $this->config->database->dbname . '.' . $this->table . '
            CHANGE COLUMN `updated_at` `updated_at`
            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ')->execute();
    }
}
