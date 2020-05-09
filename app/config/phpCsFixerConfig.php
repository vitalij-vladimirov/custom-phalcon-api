<?php

use Phalcon\Config;

/** @var Config $config */
$config = require 'config.php';

$finder = PhpCsFixer\Finder::create()
    ->in([
        'mvc',
        'db',
        'modules',
    ])
;

$lineEnding = "\n";

// TODO: find better way to resolve Win/Unix files EOL problem
try {
    $hostOsFile = file_get_contents('/var/cache/host_os.txt');
    [$hostOs] = explode("\n", $hostOsFile);
    if ($hostOs === 'msys') {
        $lineEnding = "\r\n";
    }
} catch (Throwable $throwable) {
}

return PhpCsFixer\Config::create()
    ->setLineEnding($lineEnding)
    ->setFinder($finder)
    ->setCacheFile($config->application->cacheDir . '/php_cs.json')
;