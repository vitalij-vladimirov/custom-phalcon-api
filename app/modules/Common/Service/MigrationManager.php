<?php
declare(strict_types=1);

namespace Common\Service;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Migrations\MigrationCreator;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Phinx\Console\PhinxApplication;
use Common\BaseClasses\BaseService;
use Common\Exception\LogicException;
use Common\File;
use Common\Regex;

class MigrationManager extends BaseService
{
    private const STUB_PATH = '/app/modules/Common/Config/Database/migration_stubs';
    private const ORM_CONFIG = '/app/modules/Common/Config/Database/laravel-orm-config.php';
    private const FORBIDDEN_TABLE_NAMES = ['migration', 'phinx'];

    private MigrationCreator $migrationCreator;
    private ConsoleOutput $consoleOutput;
    private PhinxApplication $phinxApplication;
    
    private string $migrationsDir;

    public function __construct(
        Filesystem $filesystem,
        ConsoleOutput $consoleOutput,
        PhinxApplication $phinxApplication
    ) {
        parent::__construct();

        $this->consoleOutput = $consoleOutput;
        $this->phinxApplication = $phinxApplication;

        $this->migrationCreator = new MigrationCreator($filesystem, self::STUB_PATH);

        $this->migrationsDir = $this->config->application->migrationsDir;
        if (substr($this->migrationsDir, -1) === '/') {
            $this->migrationsDir = substr($this->migrationsDir, 0, -1);
        }
    }

    public function createMigration(string $table = null): string
    {
        if ($table === null) {
            throw new LogicException('Argument $table must be specified.');
        }

        if (in_array($table, self::FORBIDDEN_TABLE_NAMES, true)) {
            throw new LogicException('Table name \'' . $table . '\' is forbidden.');
        }

        return $this->correctMigrationName(
            $this->migrationCreator->create(
                'create_' . $table . '_table',
                $this->migrationsDir,
                $table,
                true
            )
        );
    }

    public function updateMigration(string $table = null, string $action = null): string
    {
        if ($table === null) {
            throw new LogicException('Argument $table must be string, null given.');
        }

        if ($action === null) {
            throw new LogicException('Argument $action must be string, null given.');
        }

        if (!$this->ensurePrimaryMigrationExist($table)) {
            throw new LogicException('Table \'' . $table . '\' not found in migrations.');
        }

        list($prefix) = explode('_', $action);

        switch ($prefix) {
            case 'add':
                $actionDirection = '_to_';
                break;
            case 'update':
                $actionDirection = '_in_';
                break;
            case 'remove':
                $actionDirection = '_from_';
                break;
            default:
                throw new LogicException(
                    '$action must start with one of these prefixes: \'add_\', \'update_\', \'remove_\''
                );
        }

        return $this->correctMigrationName(
            $this->migrationCreator->create(
                $action . $actionDirection . $table . '_table',
                $this->migrationsDir,
                $table,
                false
            )
        );
    }

    public function runMigrations(string $date = null): void
    {
        if ($date !== null && !Regex::isValidPattern($date, '/^[0-9]{4,14}$/')) {
            throw new LogicException('Argument $date must contain from 4 to 14 numerical characters.');
        }

        if ($date !== null) {
            $input = new ArgvInput([null, 'migrate', '-d', $date, '-c', self::ORM_CONFIG]);
        } else {
            $input = new ArgvInput([null, 'migrate', '-c', self::ORM_CONFIG]);
        }

        $this->phinxApplication->doRun($input, $this->consoleOutput);
    }

    public function rollbackMigration(string $date = null): void
    {
        if ($date === null || !Regex::isValidPattern($date, '/^[0-9]{4,14}$/')) {
            throw new LogicException(
                'Argument $date is required and must contain from 4 to 14 numerical characters.'
            );
        }

        $input = new ArgvInput(['phinx', 'rollback', '-d', $date, '-c', self::ORM_CONFIG]);

        $this->phinxApplication->doRun($input, $this->consoleOutput);
    }

    public function ensurePrimaryMigrationExist(string $table): bool
    {
        $fileName = 'create_' . $table . '_table';

        $migrationsList = File::readDirectory(
            $this->migrationsDir,
            false,
            false
        );

        foreach ($migrationsList as $file => $filePath) {
            if (substr($file, 15, strlen($fileName)) === $fileName) {
                return true;
            }
        }

        return false;
    }
    
    private function correctMigrationName(string $filename): string
    {
        $pathLength = strlen($this->migrationsDir) + 1;
        
        $fileVersion = substr(
            $filename,
            $pathLength,
            17
        );

        $fileVersion = str_replace('_', '', $fileVersion);
        $newMigrationFile = $this->migrationsDir . '/' . $fileVersion . substr($filename, $pathLength + 17);

        File::move($filename, $newMigrationFile);
        
        return $newMigrationFile;
    }
}
