<?php
declare(strict_types=1);

namespace Common\Service;

use Common\BaseClasses\BaseService;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Migrations\MigrationCreator;

class MigrationManager extends BaseService
{
    private const STUDS_PATH = '/app/mvc/custom_migration_stubs';

    private Filesystem $filesystem;

    public function __construct(
        Filesystem $filesystem
    ) {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    public function createMigration(string $table): string
    {
        $migrationCreator = new MigrationCreator($this->filesystem, self::STUDS_PATH);

        return $migrationCreator->create(
            'create_' . $table . '_table',
            $this->config->application->migrationsDir,
            $table,
            true
        );
    }

    public function updateMigration(string $table): string
    {
    }

    public function runMigrations(): string
    {
    }
}
