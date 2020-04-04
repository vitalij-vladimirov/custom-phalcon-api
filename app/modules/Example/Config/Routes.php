<?php
declare(strict_types=1);

namespace Example\Config;

use Common\BaseClasses\BaseRoutes;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;

class Routes extends BaseRoutes
{
    public function get()
    {
        /** @var MigrationCreator $migration */
        $migration = new MigrationCreator(
            new Filesystem(),
            '/app/mvc/custom_migration_stubs'
        );

        dd($migration->create('add_test_migration', '/app/db/migrations', 'test_table', true));
    }
}
