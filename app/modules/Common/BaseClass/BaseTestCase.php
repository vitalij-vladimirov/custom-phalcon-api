<?php
declare(strict_types=1);

namespace Common\BaseClass;

use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\AbstractPdo as PhalconDb;
use Illuminate\Database\Capsule\Manager as EloquentDb;
use PHPUnit\Framework\TestCase;
use Common\Service\Injectable;
use Common\Entity\HttpResponse;
use Common\Service\HttpRequestManager;

abstract class BaseTestCase extends TestCase
{
    public const HEADER_TEST_KEY = 'PHP-Unit-Test-Token';

    protected Config $config;
    protected PhalconDb $db;
    protected EloquentDb $eloquent;

    private Injectable $injectable;
    private ?string $testToken;

    /** @var HttpRequestManager */
    private $requestManager;

    public function __construct()
    {
        parent::__construct();

        $this->injectable = new Injectable();

        $this->requestManager = $this->inject(HttpRequestManager::class);

        $this->config = $this->injectable->di->get('config');
        $this->db = $this->injectable->di->get('db');
        $this->eloquent = $this->injectable->di->get('eloquent');

        $this->testToken = getenv('TEST_TOKEN');
    }

    protected function inject(string $class): object
    {
        return $this->injectable->inject($class);
    }

    protected function truncate(BaseModel $model): void
    {
        $this->eloquent::table($model->getTable())->truncate();
    }

    protected function getRequest(
        string $uri,
        array $data = [],
        string $dataType = HttpRequestManager::DATA_TYPE_QUERY,
        int $timeout = 5,
        array $headers = []
    ): HttpResponse {
        return $this->requestManager->getRequest(
            $uri,
            $data,
            $dataType,
            $timeout,
            $this->resolveHeaders($headers)
        );
    }

    protected function postRequest(
        string $uri,
        array $data = [],
        string $dataType = HttpRequestManager::DATA_TYPE_JSON,
        int $timeout = 5,
        array $headers = []
    ): HttpResponse {
        return $this->requestManager->postRequest(
            $uri,
            $data,
            $dataType,
            $timeout,
            $this->resolveHeaders($headers)
        );
    }

    protected function putRequest(
        string $uri,
        array $data = [],
        string $dataType = HttpRequestManager::DATA_TYPE_JSON,
        int $timeout = 5,
        array $headers = []
    ): HttpResponse {
        return $this->requestManager->putRequest(
            $uri,
            $data,
            $dataType,
            $timeout,
            $this->resolveHeaders($headers)
        );
    }

    protected function deleteRequest(
        string $uri,
        array $data = [],
        string $dataType = HttpRequestManager::DATA_TYPE_QUERY,
        int $timeout = 5,
        array $headers = []
    ): HttpResponse {
        return $this->requestManager->deleteRequest(
            $uri,
            $data,
            $dataType,
            $timeout,
            $this->resolveHeaders($headers)
        );
    }

    private function resolveHeaders(array $headers = []): array
    {
        return array_merge(
            $headers,
            [
                'http_errors' => false,
                'headers' => [
                    self::HEADER_TEST_KEY => $this->testToken
                ]
            ]
        );
    }
}
