<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Common\Config\Database\Migration;
use Common\Interfaces\MigrationCreateInterface;

/**
 * Specify fields you want to create in new table in method `createSchema()`.
 *
 * IMPORTANT: Autoincrement field `id` in the start of table
 * and fields `created_at` and `updated_at` in the end of table
 * will be created by default, so you do not need to specify
 * these fields in schema.
 *
 * One migration can create only one table. Table dropping in case of
 * rollback is settled automatically, you do not need to write any additional
 * logic like `drop()` method.
 *
 * Laravel migration standards should be used to specify schema:
 * https://laravel.com/docs/7.x/migrations#creating-columns
 */
class CreateTestTable extends Migration implements MigrationCreateInterface
{
    protected string $table = 'test';

    public function createSchema(Blueprint $table): void
    {
        $table->string('username', 64);
        $table->string('password', 32);
    }
}
