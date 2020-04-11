<?php
declare(strict_types=1);

namespace Common\Task;

use Common\BaseClasses\BaseTask;
use Common\Console;
use Common\Service\SeedManager;

class SeedTask extends BaseTask
{
    /** @var SeedManager|object */
    private object $seedManager;

    public function __construct()
    {
        parent::__construct();

        $this->seedManager = $this->inject(SeedManager::class);
    }

    public function mainAction(): void
    {
        $this->helpAction();
    }

    public function helpAction(): void
    {
        echo Console::messageHeader('Seed commands:');
        echo Console::message(
            '- cli seed:create $table_name - create new seed' . PHP_EOL .
            '- cli seed:run - run seeds' . PHP_EOL .
            '- cli seed:run $table_name - run seeds'
        );
    }

    public function createAction(string $table = null): void
    {
        echo Console::success($this->seedManager->createSeed($table));
    }

    public function runAction(string $table = null): void
    {
        $this->seedManager->runSeeds($table);
    }
}
