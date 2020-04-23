<?php
declare(strict_types=1);

namespace Common\Task;

use Common\BaseClass\BaseTask;
use Common\Console;
use Common\Service\SeedManager;

class SeedTask extends BaseTask
{
    private SeedManager $seedManager;

    public function __construct(SeedManager $seedManager)
    {
        $this->seedManager = $seedManager;
    }

    public function mainAction(array $params = []): void
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

    public function createAction(array $params = []): void
    {
        $table = $params[0];

        echo Console::success($this->seedManager->createSeed($table));
    }

    public function runAction(array $params = []): void
    {
        $table = $params[0];

        $this->seedManager->runSeeds($table);
    }
}
