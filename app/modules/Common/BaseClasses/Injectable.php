<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Dice\Dice;
use Phalcon\Config;
use Phalcon\Mvc\Micro;
use Phalcon\Db\Adapter\Pdo\AbstractPdo as PhalconDb;
use Illuminate\Database\Capsule\Manager as EloquentDb;

abstract class Injectable
{
    protected Micro $app;
    protected Config $config;
    protected EloquentDb $eloquent;
    protected PhalconDb $db;

    public function __construct()
    {
        $this->app = $GLOBALS['app'];
        $this->config = $this->app->di->get('config');
        $this->db = $this->app->di->get('db');
        $this->eloquent = $this->app->di->get('eloquent');
    }

    protected function inject(string $class): object
    {
        return (new Dice())->create($class);
    }
}
