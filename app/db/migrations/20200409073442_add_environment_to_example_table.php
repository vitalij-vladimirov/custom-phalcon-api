<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Common\BaseClasses\BaseMigration;

/**
 * Specify fields you want to update/add/remove in method `migrationSchema()`.
 *
 * One migration can update only one table.
 *
 * IMPORTANT: Specify method `rollbackSchema()` correctly to revert updates
 * in case if you will need to rollback current database changes.
 *
 * If you need to run some actions before migration, implement logic in method
 * beforeMigration(). I.e., you can create table view and save current table
 * data there, then truncate data before migration.
 *
 * If you need to seed table or do any other action after migration, implement
 * logic in method afterMigration(). I.e., you can run seeds from this table,
 * update table with newly added fields data or write temporary saved in table
 * view data with appended new fields data.
 *
 * Laravel migration standards should be used to specify schema:
 * https://laravel.com/docs/7.x/migrations#creating-columns
 */
class AddEnvironmentToExampleTable extends BaseMigration
{
    protected string $table = 'example';

    protected function migrationSchema(Blueprint $table): void
    {
        $table->string('environment', 10)
            ->after('version')
            ->default('production')
        ;
    }

    protected function beforeMigration(): void
    {
        //
    }

    protected function afterMigration(): void
    {
        //(new TableSeeder())->run();
    }

    protected function rollbackSchema(Blueprint $table): void
    {
        $table->dropColumn('environment');
    }
}