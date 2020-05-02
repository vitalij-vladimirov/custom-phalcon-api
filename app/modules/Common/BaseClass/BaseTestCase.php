<?php
declare(strict_types=1);

namespace Common\BaseClass;

use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\AbstractPdo as PhalconDb;
use Illuminate\Database\Capsule\Manager as EloquentDb;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Common\Service\Injectable;

abstract class BaseTestCase extends TestCase
{
    private const ALLOWED_METHODS = ['GET', 'POST', 'PUT', 'DELETE'];

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

    protected function truncate(BaseModel $model): void
    {
        $this->eloquent::table($model->getTable())->truncate();
    }

    protected function sendRequest(
        string $method,
        string $uri,
        array $parameters = [],
        array $headers = [],
        int $timeout = 3
    ) {
        $client = new Client();

        $request = $client->request($method, $uri, [
            'query' => $parameters
        ]);
    }
}
