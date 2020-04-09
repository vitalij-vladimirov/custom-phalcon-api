<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Common\BaseClasses\BaseMigration;
use Common\Interfaces\MigrationUpdateInterface;

/**
 * Specify fields you want to update/add/remove in method `updateSchema()`.
 *
 * One migration can update only one schema.
 *
 * IMPORTANT: Specify method `rollbackSchema()` correctly to revert updates
 * in case if you will need to rollback current database changes.
 *
 * Laravel migration standards should be used to specify schema:
 * https://laravel.com/docs/7.x/migrations#creating-columns
 */
class AddVersionToExampleTable extends BaseMigration implements MigrationUpdateInterface
{
    protected string $table = 'example';

    public function updateSchema(Blueprint $table): void
    {
        $table->float('version', 8, 4)
            ->after('lib_url')
        ;
    }

    public function rollbackSchema(Blueprint $table): void
    {
        $table->dropColumn('version');
    }
}
