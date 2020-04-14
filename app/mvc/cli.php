#!/usr/local/bin/php
<?php
declare(strict_types=1);

namespace Mvc;

use Common\BaseClasses\Injectable;
use Phalcon\Config;
use Common\Console;
use Throwable;

// phpcs:disable
include '/app/vendor/autoload.php';
include '/app/mvc/Bootstrap.php';

(new Bootstrap())->runApp();

new cli($argv);
// phpcs:enable

class cli
{
    private Config $config;
    private array $arguments;
    private string $module;
    private ?string $task;
    private string $taskClass;
    private ?string $action;
    private array $parameters;
    private Injectable $injectable;

    public function __construct(array $arguments)
    {
        $this->injectable = new Injectable();

        $this->config = $this->injectable->di->get('config');
        $this->arguments = $arguments;

        $this->collectArguments();
        $this->validateArguments();
        $this->runTask();
    }

    private function collectArguments(): void
    {
        if (count($this->arguments) === 1 || in_array($this->arguments[1], ['-help', '--help', '-h'], true)) {
            $this->showHelp();
        }

        [$this->module, $this->task, $this->action] = explode(':', $this->findCommand($this->arguments[1]));

        if (empty($this->task)) {
            $this->task = 'Common';
        }

        if (!empty($this->task) && substr($this->task, -4) === 'Task') {
            $this->task = substr($this->task, 0, -4);
        }

        if (!empty($this->action) && substr($this->action, -6) === 'Action') {
            $this->action = substr($this->action, 0, -6);
        }

        if (!empty($this->task) && empty($this->action)) {
            $this->action = 'main';
        }

        $this->taskClass = '\\' . $this->module . '\Task\\' . ucfirst($this->task) . 'Task';

        $this->parameters = array_slice($this->arguments, 2);
    }

    private function validateArguments(): void
    {
        if (!file_exists('/app/modules/' . $this->module)) {
            echo Console::error('Module "' . $this->module . '" not found');
            exit;
        }

        if (!class_exists($this->taskClass)) {
            echo Console::error('Task "' . $this->task . '" not found');
            exit;
        }

        if (!method_exists($this->taskClass, $this->action . 'Action')) {
            echo Console::error('Action "' . $this->action . '" not found');
            exit;
        }
    }

    private function runTask(): void
    {
        try {
            $taskCall = $this->injectable->inject($this->taskClass);
            $actionMethod = $this->action . 'Action';
            $taskCall->$actionMethod($this->parameters);
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
