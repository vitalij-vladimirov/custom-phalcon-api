<?php
declare(strict_types=1);

namespace Mvc;

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\View\Simple as View;
use Phalcon\Url as UrlResolver;
use Illuminate\Database\Capsule\Manager as EloquentManager;
use Throwable;

class Bootstrap
{
    private FactoryDefault $services;
    private Loader $loader;

    public function runApp(): Micro
    {
        $this->setupServices();
        $this->setupLoader();

        return new Micro($this->services);
    }

    private function setupServices(): FactoryDefault
    {
        $this->services = new FactoryDefault();

        $this->services->setShared('config', function () {
            return include '/app/mvc/config.php';
        });

        $config = $this->services->getShared('config');

        $this->services->setShared('view', function () use ($config) {
            $view = new View();
            $view->setViewsDir($config->application->viewsDir);
            return $view;
        });

        $this->services->setShared('url', function () use ($config) {
            $url = new UrlResolver();
            $url->setBaseUri($config->application->baseUri);
            return $url;
        });

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

        $this->services->setShared('eloquent', function () use ($config) {
            $eloquent = new EloquentManager();
            $eloquent->addConnection([
                'driver' => $config->database->adapter,
                'host' => $config->database->host,
                'username' => $config->database->username,
                'password' => $config->database->password,
                'database' => $config->database->dbname,
                'prefix' => '',
                'charset' => $config->database->charset,
                'collation' => $config->database->collation,
            ]);
            $eloquent->setAsGlobal();
            $eloquent->bootEloquent();

            return $eloquent;
        });

        return $this->services;
    }

    private function setupLoader(): Loader
    {
        $this->loader = new Loader();

        $this->loader->registerDirs([
            $this->services->getShared('config')->application->modulesDir,
            $this->services->getShared('config')->application->mvcDir,
        ])->register();

        $this->loader->registerNamespaces($this->getNamespaces());

        return $this->loader;
    }

    private function getNamespaces(): array
    {
        $namespacesCacheLocation = $this->services->getShared('config')->application->namespacesCache;

        if (!file_exists($namespacesCacheLocation)) {
            return $this->services->getShared('config')->defaultNamespaces->toArray();
        }

        $namespacesCache = file_get_contents($namespacesCacheLocation);

        try {
            return json_decode($namespacesCache, true, JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            return $this->services->getShared('config')->defaultNamespaces->toArray();
        }
    }
}
