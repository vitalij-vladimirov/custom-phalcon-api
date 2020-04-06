<?php
declare(strict_types=1);

namespace Common\Task;

use Common\BaseClasses\BaseTask;
use Common\Call;
use Common\Service\MigrationManager;
use Common\Console;

class MigrationTask extends BaseTask
{
    /** @var MigrationManager|object */
    private object $migrationManager;

    public function onConstruct(): void
    {
        parent::onConstruct();

        $this->migrationManager = Call::class(MigrationManager::class);
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
            '- cli migration:run $date - run migrations until exact $date' . PHP_EOL .
            '- cli migration:rollback $date - rollback migration created after $date',
            false
        );

        echo Console::messageHeader('Migration update examples:');
        echo Console::message(
            'first: cli migration:create users' . PHP_EOL .
            '- cli migration:update users add_phone_number_and_email' . PHP_EOL .
            '- cli migration:update users update_gender' . PHP_EOL .
            '- cli migration:update users remove_personal_code',
            false
        );

        echo Console::messageHeader('Migration run examples:');
        echo Console::message(
            '- cli migration:run - will create all migrations that are not created yet' . PHP_EOL .
            '- cli migration:run 20200229121530 - will create migrations until 2020-02-29 at 12:15:30, ' .
                'all newer created migrations will be rolled back' . PHP_EOL .
            '- cli migration:run 202002291215 - will create migrations until 12pm 15min of february 29th of 2020, ' .
                'all newer created migrations will be rolled back' . PHP_EOL .
            '- cli migration:run 2020022912 - will create migrations until 12pm of february 29th of 2020, ' .
                'all newer created migrations will be rolled back' . PHP_EOL .
            '- cli migration:run 20200229 - will create migrations until february 29th of 2020, ' .
                'all newer created migrations will be rolled back' . PHP_EOL .
            '- cli migration:run 202002 - will create migrations until february of 2020, ' .
                'all newer created migrations will be rolled back' . PHP_EOL .
            '- cli migration:run 2020 - will create migrations until 2020, ' .
                'all newer created migrations will be rolled back',
            false
        );

        echo Console::messageHeader('Migration rollback examples:');
        echo Console::message(
            '- cli migration:rollback 20200229121530 - will rollback migrations ' .
                'created after on 2020-02-29 at 12:15:30' . PHP_EOL .
            '- cli migration:rollback 202002291215 - will rollback all migrations ' .
                'created after 12pm 15min of february 29th of 2020' . PHP_EOL .
            '- cli migration:rollback 2020022912 - will rollback all migrations ' .
                'created after 12pm of february 29th of 2020' . PHP_EOL .
            '- cli migration:rollback 20200229 - will rollback all migrations ' .
                'created after february 29th of 2020' . PHP_EOL .
            '- cli migration:rollback 202002 - will rollback all migrations created after february of 2020' . PHP_EOL .
            '- cli migration:rollback 2020 - will rollback all migrations created after 2020'
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

    public function runAction(string $date = null): void
    {
        $this->migrationManager->runMigrations($date);
    }

    public function rollbackAction(string $date = null): void
    {
        $this->migrationManager->rollbackMigration($date);
    }
}
