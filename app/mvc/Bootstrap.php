<?php
declare(strict_types=1);

namespace Mvc;

use Phalcon\Config;
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\View\Simple as View;
use Phalcon\Url as UrlResolver;
use Exception;
use Throwable;

class Bootstrap
{
    private Config $config;
    private FactoryDefault $services;
    private Loader $loader;
    private Micro $app;

    public function runApp(): Micro
    {
        $this->setupServices();
        $this->setupLoader();

        $this->app = new Micro($this->services);

        if (!empty($this->config->customRouter)) {
            if (!isset(class_implements($this->config->customRouter)[RouterInterface::class])) {
                throw new Exception('CustomRouter must implement \Mvc\RouterInterface');
            }

            return (new $this->config->customRouter())
                ->getRoutes($this->app, $this->config);
        }

        return (new Routes())->getRoutes($this->app, $this->config);
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

            if ($config->database->adapter === 'Postgresql') {
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

        $namespacesCacheLocation = $this->config->application->namespacesCache;

        if (!file_exists($namespacesCacheLocation)) {
            return $this->config->defaultNamespaces->toArray();
        }

        $namespacesCache = file_get_contents($namespacesCacheLocation);

        try {
            return json_decode($namespacesCache, true, JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            return $this->config->defaultNamespaces->toArray();
        }
    }
}
