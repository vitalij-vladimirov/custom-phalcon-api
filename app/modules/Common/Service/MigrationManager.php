<?php
declare(strict_types=1);

namespace Common\Service;

use Common\BaseClasses\BaseService;
use Common\Exception\BadRequestException;
use Common\File;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Migrations\MigrationCreator;

class MigrationManager extends BaseService
{
    private const STUDS_PATH = '/app/modules/Common/Config/Database/migration_stubs';

    private MigrationCreator $migrationCreator;

    public function __construct(
        Filesystem $filesystem
    ) {
        parent::__construct();

        $this->migrationCreator = new MigrationCreator($filesystem, self::STUDS_PATH);
    }

    public function createMigration(string $table): string
    {
        return $this->migrationCreator->create(
            'create_' . $table . '_table',
            $this->config->application->migrationsDir,
            $table,
            true
        );
    }

    public function updateMigration(string $table, string $action): string
    {
        if (!$this->ensurePrimaryMigrationExist($table)) {
            throw new BadRequestException('Table \'' . $table . '\' not found in migrations');
        }

        list($prefix) = explode('_', $action);

        switch ($prefix) {
            case 'add':
                $actionDirection = '_to_';
                break;
            case 'update':
                $actionDirection = '_on_';
                break;
            case 'remove':
                $actionDirection = '_from_';
                break;
            default:
                throw new BadRequestException(
                    '$action must start with one of these prefixes: \'add_\', \'update_\', \'remove_\''
                );
        }

        return $this->migrationCreator->create(
            $action . $actionDirection . $table . '_table',
            $this->config->application->migrationsDir,
            $table,
            false
        );
    }

    public function runMigrations(): string
    {
//        require_once '/app/db/migrations/2020_04_05_111557_create_user_table.php';
//
//        try {
//            (new \CreateUserTable())->up();
//        } catch (\Throwable $e) {
//            echo $e->getMessage() . PHP_EOL;
//            echo $e->getTraceAsString();
//
//            exit;
//        }

        return '';
    }

    private function ensurePrimaryMigrationExist(string $table): bool
    {
        $fileName = 'create_' . $table . '_table';

        $migrationsList = File::readDirectory(
            $this->config->application->migrationsDir,
            false,
            false
        );

        foreach ($migrationsList as $file => $filePath) {
            if (substr($file, 18, strlen($fileName)) === $fileName) {
                return true;
            }
        }

        return false;
    }
}
