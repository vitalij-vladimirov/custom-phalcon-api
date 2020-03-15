#!/usr/bin/env php
<?php
declare(strict_types=1);

use Phalcon\Di\FactoryDefault\Cli as CliDi;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Cli\Dispatcher;
use Phalcon\Cli\Console;

$di = new CliDi();

include '/app/mvc/bootstrap.php';

$console = new ConsoleApp($di);
$dispatcher = new Dispatcher();

if (count($argv) === 1 || in_array($argv[1], ['-help', '--help', '-h'], true)) {
    echo 'CLI call structure:' . PHP_EOL;
    echo '1. Module name' . PHP_EOL;
    echo '2. Task class name without `Task` in the end' . PHP_EOL;
    echo '3. Action name without `Action` in the end' . PHP_EOL;
    echo '... Parameters' . PHP_EOL;
    echo PHP_EOL;
    echo 'Example: `cli Test CacheBuilder Build`' . PHP_EOL;
    echo PHP_EOL;

    exit;
}

$arguments = [];
foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $module = $arg;
    } elseif ($k == 2) {
        $arguments['task'] = lcfirst($arg);
        if (substr($arguments['task'], -4) === 'Task') {
            $arguments['task'] = substr($arguments['task'], 0, -4);
        }
    } elseif ($k == 3) {
        $arguments['action'] =  lcfirst($arg);
        if (substr($arguments['action'], -6) === 'Action') {
            $arguments['action'] = substr($arguments['action'], 0, -6);
        }
    } elseif ($k >= 4) {
        $arguments['params'][] = $arg;
    }
}

$dispatcher->setDefaultNamespace($module . '\Task');
$di->setShared('dispatcher', $dispatcher);

$console = new Console($di);

try {
    $console->handle($arguments);
    echo PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(255);
}