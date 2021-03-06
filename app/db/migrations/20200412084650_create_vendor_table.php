<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Common\BaseClass\BaseMigration;
use Seeds\VendorSeeder;

/**
 * Specify fields you want to create in new table in method `migrationSchema()`.
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
 * If you need to run some actions before migration, implement logic in method
 * beforeMigration(). Any logic can be implemented here.
 *
 * If you need to seed table after migration, create new seeder and run it from
 * method afterMigration(). Any other logic can be implemented here.
 *
 * Laravel migration standards should be used to specify schema:
 * https://laravel.com/docs/7.x/migrations#creating-columns
 */
class CreateVendorTable extends BaseMigration
{
    protected string $table = 'vendor';

    protected function migrationSchema(Blueprint $table): void
    {
        $table->string('lib_name', 255);
        $table->string('lib_url', 255);
        $table->string('version', 10);
        $table->string('environment', 20);
        $table->text('description');
    }

    protected function afterMigration(): void
    {
        (new VendorSeeder())->run();
    }
}
