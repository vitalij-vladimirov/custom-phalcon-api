<?php
declare(strict_types=1);

namespace Common\BaseClass;

use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\AbstractPdo as PhalconDb;
use Illuminate\Database\Capsule\Manager as EloquentDb;
use PHPUnit\Framework\TestCase;
use Common\Service\Injectable;

abstract class BaseTestCase extends TestCase
{
    protected Config $config;
    protected PhalconDb $db;
    protected EloquentDb $eloquent;

    private Injectable $injectable;

    public function __construct()
    {
        parent::__construct();

        $this->injectable = new Injectable();

        $this->config = $this->injectable->di->get('config');
        $this->db = $this->injectable->di->get('db');
        $this->eloquent = $this->injectable->di->get('eloquent');
    }

    protected function inject(string $class): object
    {
        return $this->injectable->inject($class);
    }
}
