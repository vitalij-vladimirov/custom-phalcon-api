<?php
declare(strict_types=1);

namespace Common\Task;

use Common\BaseClasses\BaseTask;
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
            '- cli migration:create $table_name - create new table' . PHP_EOL .
            '- cli migration:update $table_name $action_to_do - update existing table' . PHP_EOL .
            '- cli migration:run - run migrations' . PHP_EOL .
            '- cli migration:rollback $date - rollback migration by version (date or datetime)',
            false
        );

        echo Console::messageHeader('Migration update examples:');
        echo Console::message(
            '1) cli migration:create users' . PHP_EOL .
            '2.a) cli migration:update users add_phone_number_and_email' . PHP_EOL .
            '2.b) cli migration:update users update_gender' . PHP_EOL .
            '2.c) cli migration:update users remove_personal_code',
            false
        );

        echo Console::messageHeader('Migration rollback examples:');
        echo Console::message(
            'a) cli migration:rollback 20200229121530 - will rollback migration ' .
                'created exactly on 2020-02-29 at 12:15:30' . PHP_EOL .
            'b) cli migration:rollback 202002291215 - will rollback all migrations ' .
                'created at 12pm 15min on february 29th of 2020' . PHP_EOL .
            'c) cli migration:rollback 2020022912 - will rollback all migrations ' .
                'created from 12pm to 1pm on february 29th of 2020' . PHP_EOL .
            'd) cli migration:rollback 20200229 - will rollback all migrations ' .
                'created on february 29th of 2020' . PHP_EOL .
            'e) cli migration:rollback 202002 - will rollback all migrations created on february of 2020' . PHP_EOL .
            'f) cli migration:rollback 2020 - will rollback all migrations of 2020'
        );
    }

    public function createAction(string $table = null): void
    {
        echo Console::success($this->migrationManager->createMigration($table));
    }

    public function updateAction(string $table = null, string $action = null): void
    {
        echo Console::success($this->migrationManager->updateMigration($table, $action));
    }

    public function runAction(): void
    {
        $this->migrationManager->runMigrations();
    }

    public function rollbackAction(string $date = null): void
    {
        $this->migrationManager->rollbackMigration($date);
    }
}
