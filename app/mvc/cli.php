#!/usr/bin/env php
<?php
declare(strict_types=1);

namespace Mvc;

use Phalcon\Config;
use Phalcon\Di\FactoryDefault\Cli as CliDi;
use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;
use Throwable;

// phpcs:disable
include '/app/vendor/autoload.php';
include '/app/mvc/Bootstrap.php';

$bootstrap = new Bootstrap();
$bootstrap->runCli();

/**
 * Config can be called from anywhere using $GLOBALS['config']
 *
 * @var Config $config
 */
$config = $bootstrap->getConfig();

new Cli($argv);
// phpcs:enable

class Cli
{
    private CliDi $di;
    private array $args;
    private string $module;
    private array $arguments;

    public function __construct(array $args)
    {
        $this->di = new CliDi();
        $this->args = array_slice($args, 1);

        $this->collectArguments();
        $this->validateArguments();
        $this->runTask();
    }

    private function collectArguments(): void
    {
        if (!count($this->args) || in_array($this->args[0], ['-help', '--help', '-h'], true)) {
            $this->showHelp();
        }

        list($this->module, $this->arguments['task'], $this->arguments['action']) = explode(':', $this->args[0]);

        if (!empty($this->arguments['task']) && substr($this->arguments['task'], -4) === 'Task') {
            $this->arguments['task'] = substr($this->arguments['task'], 0, -4);
        }

        if (!empty($this->arguments['action']) && substr($this->arguments['action'], -6) === 'Action') {
            $this->arguments['action'] = substr($this->arguments['action'], 0, -6);
        }

        if (count($this->args) > 1) {
            for ($i = 1; $i < count($this->args); ++$i) {
                $this->arguments['params'][] = $this->args[$i];
            }
        }

        if (!empty($this->arguments['task']) && empty($this->arguments['action'])) {
            $this->arguments['action'] = 'main';
        }
    }

    private function validateArguments(): void
    {
        if (!file_exists('/app/modules/' . $this->module)) {
            echo 'Module "' . $this->module . '" not found' . PHP_EOL;
            exit;
        }

        $taskClass = $this->module . '\Task\\' . ucfirst($this->arguments['task']) . 'Task';
        if (!class_exists($taskClass)) {
            echo 'Task "' . $this->arguments['task'] . '" not found' . PHP_EOL;
            exit;
        }

        if (!method_exists($taskClass, $this->arguments['action'] . 'Action')) {
            echo 'Action "' . $this->arguments['action'] . '" not found' . PHP_EOL;
            exit;
        }
    }

    private function runTask(): void
    {
        $console = new Console($this->di);
        $dispatcher = new Dispatcher();

        $dispatcher->setNamespaceName($this->module . '\Task');
        $this->di->setShared('dispatcher', $dispatcher);

        try {
            $console->handle($this->arguments);
            exit;
        } catch (Throwable $exception) {
            echo $exception->getMessage() . PHP_EOL;
            echo $exception->getTraceAsString() . PHP_EOL;
            exit(255);
        }
    }

    private function showHelp()
    {
        echo PHP_EOL;
        echo 'CLI call structure: cli Module:TaskName:ActionName ...parameters' . PHP_EOL;
        echo '- Module folder name.' . PHP_EOL;
        echo '- TaskName should be without `Task` in the end.' . PHP_EOL;
        echo '- ActionName is optional, should be without `Action` in the end. Default action is `main`.' . PHP_EOL;
        echo '- ...parameters are optional, should be separated with spaces.' . PHP_EOL;
        echo PHP_EOL;
        echo 'Examples:' . PHP_EOL;
        echo '- cli Common:CacheNamespaces:main param1 param2' . PHP_EOL;
        echo '- cli Common:CacheNamespaces:main' . PHP_EOL;
        echo '- cli Common:CacheNamespaces param1 param2' . PHP_EOL;
        echo '- cli Common:CacheNamespaces' . PHP_EOL;
        echo PHP_EOL;

        exit;
    }
}
