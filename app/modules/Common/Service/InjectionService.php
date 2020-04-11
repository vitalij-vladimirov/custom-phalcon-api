<?php
declare(strict_types=1);

namespace Common\Service;

use Common\BaseClasses\BaseService;
use Illuminate\Database\Capsule\Manager;
use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\AbstractPdo;
use Phalcon\Mvc\Micro;

class InjectionService extends BaseService
{
    public function inject(string $class): object
    {
        return parent::inject($class);
    }

    public function getApp(): Micro
    {
        return $this->app;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getDb(): AbstractPdo
    {
        return $this->db;
    }

    public function getEloquent(): Manager
    {
        return $this->eloquent;
    }
}
