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

return PhpCsFixer\Config::create()
    ->setLineEnding("\n")
    ->setFinder($finder)
    ->setCacheFile($config->application->cacheDir . '/php_cs.json')
;