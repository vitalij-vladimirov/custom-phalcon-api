<?php
declare(strict_types=1);

namespace Common\Task;

use Common\BaseClasses\BaseTask;
use Common\Exception\BadRequestException;
use Common\Service\MigrationManager;
use Common\Console;

class MigrationTask extends BaseTask
{
    /** @var MigrationManager|object */
    private object $migrationManager;

    public function onConstruct(): void
    {
        parent::onConstruct();

        $this->migrationManager = $this->di->create(MigrationManager::class);
    }

    public function mainAction(): void
    {
        $this->helpAction();
    }

    public function helpAction(): void
    {
        echo Console::messageHeader('Migration commands:');
        echo Console::message(
            '- cli migration:create table_name - create new table' . PHP_EOL .
            '- cli migration:update table_name - update existing table' . PHP_EOL .
            '- cli migration:run - run migrations'
        );
    }

    public function createAction(string $table = null): void
    {
        if ($table === null) {
            throw new BadRequestException('Table argument must be specified!');
        }

        echo Console::success($this->migrationManager->createMigration($table));
    }

    public function updateAction(string $table = null): void
    {
        if ($table === null) {
            throw new BadRequestException('Table argument must be specified!');
        }

        echo Console::success($this->migrationManager->updateMigration($table));
    }

    public function runAction(string $table = null): void
    {
        echo Console::success($this->migrationManager->runMigrations());
    }
}
