<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\AbstractPdo as PhalconDb;
use Illuminate\Database\Capsule\Manager as EloquentDb;
use Phalcon\Mvc\Micro;
use PHPUnit\Framework\TestCase;
use Common\Service\InjectionService;

abstract class BaseTestCase extends TestCase
{
    protected Micro $app;
    protected Config $config;
    protected PhalconDb $db;
    protected EloquentDb $eloquent;

    private InjectionService $injectionService;

    public function __construct()
    {
        parent::__construct();

        $this->injectionService = new InjectionService();

        $this->app = $this->injectionService->getApp();
        $this->config = $this->injectionService->getConfig();
        $this->db = $this->injectionService->getDb();
        $this->eloquent = $this->injectionService->getEloquent();
    }

    protected function inject(string $class): object
    {
        return $this->injectionService->inject($class);
    }
}
