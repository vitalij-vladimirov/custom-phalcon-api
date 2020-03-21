#!/usr/bin/env php
<?php
declare(strict_types=1);

namespace BaseMvc;

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
        $this->runCronjobs($this->scheduler, $args);
        $this->scheduler->run();
    }

    private function runCronjobs(Scheduler $cron, array $args): Scheduler
    {
        if (isset($args[1]) && $args[1] === 'development') {
            return $this->development($cron);
        }

        return $this->production($cron);
    }

    private function development(Scheduler $cron): Scheduler
    {
        $cron->php('/app/mvc/cli.php Common:CacheNamespaces cron')->everyMinute();

        return $cron;
    }

    private function production(Scheduler $cron): Scheduler
    {
        return $cron;
    }
}
