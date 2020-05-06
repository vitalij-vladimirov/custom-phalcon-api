<?php

use Phalcon\Config;

/** @var Config $config */
$config = require '/app/config/config.php';

$finder = PhpCsFixer\Finder::create()
    ->in([
        '/app/mvc',
        '/app/db',
        '/app/modules',
    ])
;

return PhpCsFixer\Config::create()
    ->setLineEnding("\r\n")
    ->setFinder($finder)
    ->setCacheFile($config->application->cacheDir . '/php_cs_cache.json')
;