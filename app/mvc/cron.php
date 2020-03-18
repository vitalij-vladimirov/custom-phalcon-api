#!/usr/bin/env php
<?php
declare(strict_types=1);

namespace BaseMvc;

use GO\Scheduler;

// phpcs:disable
require_once '/app/vendor/autoload.php';

new Crontab();
// phpcs:enable

class Crontab
{
    private Scheduler $scheduler;

    public function __construct()
    {
        $this->scheduler = new Scheduler();
        $this->setupCronjobs($this->scheduler);
        $this->scheduler->run();
    }

    private function setupCronjobs(Scheduler $cron): Scheduler
    {
        $cron->php('/app/mvc/cli.php Common:CacheNamespaces')->everyMinute();

        return $cron;
    }
}
