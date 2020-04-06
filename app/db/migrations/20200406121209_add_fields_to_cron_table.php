<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Common\Config\Database\Migration;
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
class AddFieldsToCronTable extends Migration implements MigrationUpdateInterface
{
    protected string $table = 'cron';

    public function updateSchema(Blueprint $table): void
    {
         $table->string('new_column', 55)
             ->after('column_name')
         ;
    }

    public function rollbackSchema(Blueprint $table): void
    {
          $table->dropColumn('new_column');
    }
}
