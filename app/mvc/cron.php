#!/usr/local/bin/php
<?php
declare(strict_types=1);

namespace Mvc;

use GO\Scheduler;

// phpcs:disable
require_once '/app/vendor/autoload.php';

new Cron($argv);
// phpcs:enable

class Cron
{
    private Scheduler $scheduler;

    public function __construct(array $args)
    {
        $this->scheduler = new Scheduler();
        $this->runCronjobs($args);
        $this->scheduler->run();
    }

    private function runCronjobs(array $args): void
    {
        switch ($args[1]) {
            case 'development':
                $this->development();
                break;
            case 'production':
                $this->production();
                break;
        }
    }

    private function development(): void
    {
        $this->scheduler
            ->php('/app/mvc/cli.php Common:CacheNamespaces cron')
            ->everyMinute()
        ;

        $this->scheduler
            ->php('/app/mvc/cli.php Common:RemoveUnusedFiles')
            ->everyMinute()
        ;
    }

    private function production(): void
    {
        $this->scheduler
            ->php('/app/mvc/cli.php Common:RemoveUnusedFiles')
            ->everyMinute()
        ;
    }
}
