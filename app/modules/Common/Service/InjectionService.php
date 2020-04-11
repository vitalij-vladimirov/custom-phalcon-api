<?php
declare(strict_types=1);

namespace Common\Service;

use Common\BaseClasses\Injectable;
use Phalcon\Mvc\Micro;
use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\AbstractPdo as PhalconDb;
use Illuminate\Database\Capsule\Manager as EloquentDb;

class InjectionService extends Injectable
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

    public function getDb(): PhalconDb
    {
        return $this->db;
    }

    public function getEloquent(): EloquentDb
    {
        return $this->eloquent;
    }
}
