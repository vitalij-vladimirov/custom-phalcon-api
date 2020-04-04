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
            '- cli migration:update table_name action_to_do - update existing table' . PHP_EOL .
            '- cli migration:run - run migrations',
            false
        );

        echo Console::messageHeader('Migration update examples:');
        echo Console::message(
            '1) cli migration:create users' . PHP_EOL .
            '2.a) cli migration:update users add_phone_number_and_email' . PHP_EOL .
            '2.b) cli migration:update users update_gender' . PHP_EOL .
            '2.c) cli migration:update users remove_personal_code'
        );
    }

    public function createAction(string $table = null): void
    {
        if ($table === null) {
            throw new BadRequestException('Table argument must be specified!');
        }

        echo Console::success($this->migrationManager->createMigration($table));
    }

    public function updateAction(string $table = null, string $action = null): void
    {
        if ($table === null) {
            throw new BadRequestException('First argument $table must be string, null given');
        }

        if ($action === null) {
            throw new BadRequestException('Second argument $action must be string, null given');
        }

        echo Console::success($this->migrationManager->updateMigration($table, $action));
    }

    public function runAction(string $table = null): void
    {
        echo Console::success($this->migrationManager->runMigrations());
    }
}
