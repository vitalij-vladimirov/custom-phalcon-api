<?php
declare(strict_types=1);

namespace BaseMvc;

use Phalcon\Config;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\View\Simple as View;
use Phalcon\Url as UrlResolver;
use Common\ApiException\NotFoundApiException;
use Common\Service\CacheManager;
use Common\Json;

class Bootstrap
{
    private Config $config;
    private FactoryDefault $services;
    private Loader $loader;
    private Micro $app;
    private Router $router;

    public function runApp(): Micro
    {
        $this->setupServices();
        $this->setupLoader();

        $this->app = new Micro($this->services);

        /**
         * Call default Phalcon Micro service routing
         */
        $this->defaultRouter();

        return $this->app;
    }

    public function runCli(): void
    {
        $this->setupServices();
        $this->setupLoader();
    }

    public function getConfig(): Config
    {
        if (empty($this->config)) {
            $this->setupServices();
        }

        return $this->config;
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
        $this->config = $config = $this->services->getConfig();

        /**
         * Sets the view component
         */
        $this->services->setShared('view', function () use ($config) {
            $view = new View();
            $view->setViewsDir($config->application->viewsDir);
            return $view;
        });

        /**
         * The URL component is used to generate all kind of urls in the application
         */
        $this->services->setShared('url', function () use ($config) {
            $url = new UrlResolver();
            $url->setBaseUri($config->application->baseUri);
            return $url;
        });

        /**
         * Database connection is created based in the parameters defined in the configuration file
         */
        $this->services->setShared('db', function () use ($config) {
            $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
            $params = [
                'host'     => $config->database->host,
                'username' => $config->database->username,
                'password' => $config->database->password,
                'dbname'   => $config->database->dbname,
                'charset'  => $config->database->charset
            ];

            if ($config->database->adapter == 'Postgresql') {
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

    /**
     * Default Phalcon Micro routing
     * You can setup routes here or call another Class to setup default routes
     */
    private function defaultRouter(): void
    {
        $app = $this->app;

        $this->app->get('/api/test', function () use ($app) {
            $app->response
                ->setContentType('application/json; charset=utf-8')
                ->sendHeaders()
            ;

            echo Json::encode([
                'code' => 'success',
                'message' => 'Here you can run you code.'
            ]);
        });

        /**
         * Display error in JSON as API
         */
        $this->app->notFound(function () {
            throw new NotFoundApiException();
        });

//        /**
//         * Display error in HTML as website
//         */
//        $this->app->notFound(function () {
//            $this->app->response->setStatusCode(404, "Not Found")->sendHeaders();
//            return $this->app['view']->render('404');
//        });
    }
}
