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
        if (isset($args[1]) && $args[1] !== 'production') {
            $this->development();

            return;
        }

        $this->production();
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
