<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\AbstractPdo;
use Phinx\Migration\AbstractMigration;
use Illuminate\Database\Capsule\Manager as EloquentDb;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use Common\Service\InjectionService;
use Common\Exception\LogicException;
use Common\Regex;
use Common\Text;

abstract class BaseMigration extends AbstractMigration
{
    private const MIGRATION_CREATE = 'create';
    private const MIGRATION_UPDATE = 'update';

    protected string $table;

    protected InjectionService $injectionService;
    protected EloquentDb $eloquent;
    protected Builder $schema;
    protected AbstractPdo $db;

    private Config $config;
    private string $migrationType;

    abstract protected function migrationSchema(Blueprint $table): void;

    public function up(): void
    {
        if (empty($this->table)) {
            throw new LogicException('$table name must be specified.');
        }

        $this->loadGlobalServices();

        if (method_exists($this, 'beforeMigration')) {
            $this->beforeMigration();
        }

        if ($this->migrationType === self::MIGRATION_CREATE) {
            $this->schema->create($this->table, function (Blueprint $table) {
                $table->id();

                $this->migrationSchema($table);

                $table->timestamp('created_at')
                    ->index()
                    ->useCurrent();
                $table->timestamp('updated_at')
                    ->index()
                    ->useCurrent();
            });
        } elseif ($this->migrationType === self::MIGRATION_UPDATE) {
            $this->schema->table($this->table, function (Blueprint $table) {
                $this->migrationSchema($table);
            });
        }

        $this->correctUpdatedAtField();

        if (method_exists($this, 'afterMigration')) {
            $this->afterMigration();
        }
    }

    public function down(): void
    {
        if (empty($this->table)) {
            throw new LogicException('$table name must be specified.');
        }

        if ($this->migrationType === self::MIGRATION_CREATE) {
            $this->schema->dropIfExists($this->table);
        } else {
            $this->schema->table($this->table, function (Blueprint $table) {
                $this->rollbackSchema($table);
            });
        }
    }

    protected function init(): void
    {
        $this->loadGlobalServices();
    }

    protected function inject(string $class): object
    {
        return $this->injectionService->inject($class);
    }

    private function setMigrationType(): string
    {
        $className = get_class($this);

        $tableNameInPascal = Text::toPascalCase($this->table);

        if ($className === 'Create' . $tableNameInPascal . 'Table') {
            return $this->migrationType = self::MIGRATION_CREATE;
        }

        $updateClassPattern = '/^(Add|Remove|Update)+[A-Za-z0-9]+(To|From|On)+' . $tableNameInPascal . 'Table$/';
        if (Regex::isValidPattern($className, $updateClassPattern)) {
            return $this->migrationType = self::MIGRATION_UPDATE;
        }

        throw new LogicException('Migration type can not be detected.');
    }

    private function loadGlobalServices(): void
    {
        $this->injectionService = new InjectionService();

        $this->config = $this->injectionService->getConfig();
        $this->db = $this->injectionService->getDb();
        $this->eloquent = $this->injectionService->getEloquent();

        $this->schema = $this->eloquent::schema();

        $this->setMigrationType();
    }

    /**
     * Updating field `updated_at` to "DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
     * has been tested only with MySql, so method is returned at the start if other DB is used.
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
            ALTER TABLE `' . $this->config->database->dbname . '`.`' . $this->table . '`
            CHANGE COLUMN `updated_at` `updated_at`
            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ')->execute();
    }
}
