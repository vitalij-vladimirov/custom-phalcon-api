<?php
declare(strict_types=1);

namespace Mvc;

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\View\Simple as View;
use Phalcon\Url as UrlResolver;
use Illuminate\Database\Capsule\Manager as EloquentManager;
use Dotenv\Dotenv;
use Throwable;

class Bootstrap
{
    private FactoryDefault $di;

    public function runApp(): Micro
    {
        $this->setupDi();
        $this->setupLoader();
        $this->defineGlobals();

        return new Micro($this->di);
    }

    private function setupDi(): FactoryDefault
    {
        $this->di = new FactoryDefault();

        $this->di->set('config', function () {
            if (file_exists('/app/.env')) {
                (Dotenv::createImmutable('/app/'))->load();
            } else {
                (Dotenv::createImmutable('/app/config/', '.env.development'))->load();
            }

            return include '/app/config/config.php';
        });

        $config = $this->di->get('config');

        $this->di->set('view', function () use ($config) {
            $view = new View();
            $view->setViewsDir($config->application->viewsDir);
            return $view;
        });

        $this->di->set('url', function () use ($config) {
            $url = new UrlResolver();
            $url->setBaseUri($config->application->baseUri);
            return $url;
        });

        $this->di->set('db', function () use ($config) {
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

        $this->di->set('eloquent', function () use ($config) {
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

        return $this->di;
    }

    private function setupLoader(): Loader
    {
        $loader = new Loader();

        $loader->registerDirs([
            $this->di->get('config')->application->modulesDir,
            $this->di->get('config')->application->mvcDir,
        ])->register();

        $loader->registerNamespaces($this->getNamespaces());

        return $loader;
    }

    private function getNamespaces(): array
    {
        $namespacesCacheLocation = $this->di->get('config')->application->namespacesCache;

        if (!file_exists($namespacesCacheLocation)) {
            return $this->di->get('config')->defaultNamespaces->toArray();
        }

        $namespacesCache = file_get_contents($namespacesCacheLocation);

        try {
            return json_decode($namespacesCache, true, JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            return $this->di->get('config')->defaultNamespaces->toArray();
        }
    }

    private function defineGlobals(): void
    {
        define('APP_ENV', $this->di->get('config')->environment ?? 'production');

        // Load Eloquent DB
        $this->di->get('eloquent');
    }
}
