#!/usr/bin/env php
<?php
declare(strict_types=1);

namespace Mvc;

use Phalcon\Config;
use Phalcon\Di\FactoryDefault\Cli as CliDi;
use Phalcon\Cli\Console as PhalconConsole;
use Common\Console;
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

new Cli($argv, $config);
// phpcs:enable

class Cli
{
    private CliDi $di;
    private Config $config;
    private array $args;
    private string $module;
    private array $arguments;

    public function __construct(array $args, Config $config)
    {
        $this->di = new CliDi();
        $this->config = $config;
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

        $command = $this->findCommand($this->args[0]);

        list($this->module, $this->arguments['task'], $this->arguments['action']) = explode(':', $command);

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
        $console = new PhalconConsole($this->di);
        $dispatcher = new Dispatcher();

        $dispatcher->setNamespaceName($this->module . '\Task');
        $this->di->setShared('dispatcher', $dispatcher);

        try {
            $console->handle($this->arguments);
            exit;
        } catch (Throwable $exception) {
            echo Console::error($exception->getMessage());
//            echo Console::error('Exception: ' . $exception->getMessage(), false);
//            echo Console::messageHeader('Exception trace:');
//            echo Console::message($exception->getTraceAsString());

            exit(255);
        }
    }

    private function showHelp()
    {
        echo Console::message(
            'CLI call structure: cli Module:TaskName:ActionName ...parameters' . PHP_EOL .
            '- Module folder name.' . PHP_EOL .
            '- TaskName should be without `Task` in the end.' . PHP_EOL .
            '- ActionName is optional, should be without `Action` in the end. Default action is `main`.' . PHP_EOL .
            '- ...parameters are optional, should be separated with spaces.' . PHP_EOL .
            PHP_EOL .
            'Examples:' . PHP_EOL .
            '- cli Common:CacheNamespaces:main param1 param2' . PHP_EOL .
            '- cli Common:CacheNamespaces:main' . PHP_EOL .
            '- cli Common:CacheNamespaces param1 param2' . PHP_EOL .
            '- cli Common:CacheNamespaces'
        );

        exit;
    }

    private function findCommand(string $commandArgument): string
    {
        if (isset($this->config->cliShortcuts[$commandArgument])) {
            return $this->config->cliShortcuts[$commandArgument];
        }

        $arguments = explode(':', $commandArgument);

        if (count($arguments) !== 2) {
            return $commandArgument;
        }

        if (isset($this->config->cliShortcuts[$arguments[0]])) {
            return $this->config->cliShortcuts[$arguments[0]] . ':' . $arguments[1];
        }

        return $commandArgument;
    }
}
