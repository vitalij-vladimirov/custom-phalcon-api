#!/usr/bin/env php
<?php
declare(strict_types=1);

namespace BaseMvc;

use Phalcon\Di\FactoryDefault\Cli as CliDi;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Cli\Dispatcher;
use Phalcon\Cli\Console;

$di = new CliDi();

include '/app/mvc/bootstrap.php';

new CliTask($di, array_slice($argv, 1));

class CliTask
{
    private CliDi $di;
    private array $args;
    private string $module;
    private array $arguments;

    public function __construct(CliDi $di, array $args)
    {
        $this->di = $di;
        $this->args = $args;

        $this->collectArguments();
        $this->validateArguments();
        $this->runTask();
    }

    private function collectArguments(): void
    {
        if (!count($this->args) || in_array($this->args[0], ['-help', '--help', '-h'], true)) {
            $this->module = 'Common';
            $this->arguments['task'] = 'default';

            $this->runTask();
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
        // TODO: log exceptions

        $class = $this->module . '\Task\\' . ucfirst($this->arguments['task']) . 'Task';

        if (!class_exists($class)) {
            $this->module = 'Common';
            $this->arguments = [
                'task' => 'default',
                'action' => 'notFound',
                'params' => [
                    $this->arguments['task']
                ],
            ];

            $this->runTask();
        }

        if (!method_exists($class, $this->arguments['action'] . 'Action')) {
            $this->module = 'Common';
            $this->arguments = [
                'task' => 'default',
                'action' => 'notFound',
                'params' => [
                    $this->arguments['task'],
                    $this->arguments['action']
                ],
            ];

            $this->runTask();
        }
    }

    private function runTask(): void
    {
        $console = new ConsoleApp($this->di);
        $dispatcher = new Dispatcher();

        $dispatcher->setNamespaceName($this->module . '\Task');
        $this->di->setShared('dispatcher', $dispatcher);

        try {
            $console->handle($this->arguments);
            exit;
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            echo $e->getTraceAsString() . PHP_EOL;
            exit(255);
        }
    }
}
