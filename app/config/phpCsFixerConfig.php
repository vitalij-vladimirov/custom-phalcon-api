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

//TODO: resolve HOST_OS
//if (getenv('HOST_OS') === 'UNIX') {
//    $lineEnding = "\r\n";
//}

return PhpCsFixer\Config::create()
    ->setLineEnding($lineEnding)
    ->setFinder($finder)
    ->setCacheFile($config->application->cacheDir . '/php_cs.json')
;