<?php
declare(strict_types=1);

namespace Common\Config\Database;

use Phalcon\Config;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use Phinx\Migration\AbstractMigration;
use Common\Exception\InternalErrorException;
use Common\Interfaces\MigrationCreateInterface;
use Common\Interfaces\MigrationUpdateInterface;

abstract class Migration extends AbstractMigration
{
    protected string $table;
    protected Capsule $capsule;
    protected Builder $schema;

    protected function init()
    {
        /** @var Config $config */
        $config = $GLOBALS['config'];

        $this->capsule = new Capsule;
        $this->capsule->addConnection([
            'driver'    => $config->database->adapter,
            'host'      => $config->database->host,
            'port'      => $config->database->port,
            'database'  => $config->database->dbname,
            'username'  => $config->database->username,
            'password'  => $config->database->password,
            'charset'   => $config->database->charset,
            'collation' => $config->database->collation,
        ]);

        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();
        $this->schema = $this->capsule->schema();
    }

    public function up(): void
    {
        if (empty($this->table)) {
            throw new InternalErrorException('$table name must be specified.');
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

        throw new InternalErrorException(
            'Migration must implement migration \'create\' or \'update\' interface.'
        );
    }

    public function down(): void
    {
        if (empty($this->table)) {
            throw new InternalErrorException('$table name must be specified.');
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

    protected function correctUpdatedAtField(): void
    {
        // TODO: write logic to update field "updated_at" to "CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    }
}
