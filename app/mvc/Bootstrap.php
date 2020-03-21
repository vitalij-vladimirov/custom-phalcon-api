<?php
declare(strict_types=1);

namespace BaseMvc;

use Common\ApiException\NotFoundApiException;
use Dice\Dice;
use Phalcon\Config;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\View\Simple as View;
use Phalcon\Url as UrlResolver;
use Common\Service\CacheManager;
use Common\Json;

class Bootstrap
{
    private Config $config;
    private FactoryDefault $services;
    private Loader $loader;
    private Micro $app;
    private Dice $di;

    public function runApp(): Micro
    {
        $this->setupServices();
        $this->setupLoader();

        $this->di = new Dice();

        $this->app = new Micro($this->services);

        $this->app->get('/', function () {
            $run = $this->di->create(\Example\Controller\ExampleController::class);
            echo $run->getJoke(2);
        });

        $this->app->notFound(function () {
            throw new NotFoundApiException();
        });

        return $this->app;
    }

    public function runCli(): void
    {
        $this->setupServices();
        $this->setupLoader();
    }

    private function setupServices(): FactoryDefault
    {
        $this->services = new FactoryDefault();

        /**
         * Shared configuration service
         */
        $this->services->setShared('config', function () {
            return include '/app/mvc/config.php';
        });
        $this->config = $this->services->getConfig();

        /**
         * Sets the view component
         */
        $this->services->setShared('view', function () {
            $view = new View();
            $view->setViewsDir($this->config->application->viewsDir);
            return $view;
        });

        /**
         * The URL component is used to generate all kind of urls in the application
         */
        $this->services->setShared('url', function () {
            $url = new UrlResolver();
            $url->setBaseUri($this->config->application->baseUri);
            return $url;
        });

        /**
         * Database connection is created based in the parameters defined in the configuration file
         */
        $this->services->setShared('db', function () {
            $class = 'Phalcon\Db\Adapter\Pdo\\' . $this->config->database->adapter;
            $params = [
                'host'     => $this->config->database->host,
                'username' => $this->config->database->username,
                'password' => $this->config->database->password,
                'dbname'   => $this->config->database->dbname,
                'charset'  => $this->config->database->charset
            ];

            if ($this->config->database->adapter == 'Postgresql') {
                unset($params['charset']);
            }

            return new $class($params);
        });

        return $this->services;
    }

    private function setupLoader(): Loader
    {
        if (empty($this->config)) {
            $this->setupServices();
        }

        $this->loader = new Loader();

        $this->loader->registerDirs([
            $this->config->application->modulesDir,
            $this->config->application->mvcDir,
        ])->register();

        $this->loader->registerNamespaces($this->getNamespaces());

        return $this->loader;
    }

    private function getNamespaces(): array
    {
        if (empty($this->config)) {
            $this->setupServices();
        }

        /**
         * Loading namespaces from cache
         */
        $namespacesCache = file_exists(CacheManager::NAMESPACES_CACHE_FILE) ?
            (file_get_contents(CacheManager::NAMESPACES_CACHE_FILE) ?? null) :
            null
        ;

        if (!$namespacesCache) {
            /**
             * Loading namespaces necessary to run namespaces cache manager
             */
            return [
                'BaseMvc' => substr($this->config->application->mvcDir, 0, -1),
                'Common' => $this->config->application->modulesDir . 'Common',
                'Common\Task' => $this->config->application->modulesDir . 'Common/Task',
                'Common\Service' => $this->config->application->modulesDir . 'Common/Service',
            ];
        }

        return Json::decode($namespacesCache);
    }
}
