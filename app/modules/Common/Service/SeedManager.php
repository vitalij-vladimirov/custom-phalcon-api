<?php
declare(strict_types=1);

namespace Common\Service;

use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Common\Exception\LogicException;
use Common\Text;
use Common\File;

final class SeedManager extends Injectable
{
    private const STUB_PATH = '/app/modules/Common/Config/Database/seed_stubs';

    private MigrationCreator $migrationCreator;
    private MigrationManager $migrationManager;
    
    private string $seedsDir;

    public function __construct(
        Filesystem $filesystem,
        MigrationManager $migrationManager
    ) {
        $this->migrationManager = $migrationManager;

        $this->migrationCreator = new MigrationCreator($filesystem, self::STUB_PATH);

        $this->seedsDir = $this->di->get('config')->application->seedsDir;
        if (substr($this->seedsDir, -1) === '/') {
            $this->seedsDir = substr($this->seedsDir, 0, -1);
        }
    }

    public function createSeed(string $table = null): string
    {
        if ($table === null) {
            throw new LogicException('Argument $table must be specified.');
        }

        if (!$this->migrationManager->ensurePrimaryMigrationExist($table)) {
            throw new LogicException('Table \'' . $table . '\' migration not found.');
        }

        if (class_exists($this->getSeedClassByTable($table))
            || File::exists($this->getSeedPathByTable($table))
        ) {
            throw new LogicException('Destination file already exists.');
        }

        $deleteStubAfterCreation = false;
        if (!File::exists(self::STUB_PATH . '/migration.create.stub')) {
            File::copy(
                self::STUB_PATH . '/seeder.create.stub',
                self::STUB_PATH . '/migration.create.stub'
            );

            $deleteStubAfterCreation = true;
        }

        $seeder = $this->migrationCreator->create(
            $table . '_seeder',
            $this->seedsDir,
            $table,
            true
        );

        if ($deleteStubAfterCreation) {
            File::delete(self::STUB_PATH . '/migration.create.stub');
        }

        return $this->correctSeedName($seeder, $table);
    }

    public function runSeeds(string $table = null): void
    {
        if ($this->di->get('config')->environment === 'production') {
            throw new LogicException('Seeds can\'t be used in production environment.');
        }

        if ($table !== null) {
            $this->runSeed($table);
            return;
        }

        $seedsList = File::readDirectory($this->seedsDir, false, false);

        foreach ($seedsList as $file => $path) {
            $cutEnding = strlen('Seeder.php') * -1;
            $table = Text::toSnakeCase(substr($file, 0, $cutEnding));
            $this->runSeed($table);
        }
    }
    
    private function correctSeedName(string $filename, string $table): string
    {
        $newName = $this->seedsDir . '/' . Text::toPascalCase($table . '_seeder') . '.php';

        File::move($filename, $newName);
        
        return $newName;
    }

    private function getSeedPathByTable(string $table): string
    {
        return $this->seedsDir . '/' . Text::toPascalCase($table) . 'Seeder.php';
    }

    private function getSeedClassByTable(string $table): string
    {
        return '\\Seeds\\' . Text::toPascalCase($table) . 'Seeder';
    }

    private function runSeed(string $table)
    {
        $seedClass = $this->getSeedClassByTable($table);
        if (!class_exists($seedClass)) {
            throw new LogicException('Seeder not found.');
        }

        (new $seedClass())->run();
    }
}
