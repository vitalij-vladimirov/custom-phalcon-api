#!/usr/bin/env php
<?php
declare(strict_types=1);

use GO\Scheduler;

require_once '/app/vendor/autoload.php';

new Crontab();

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