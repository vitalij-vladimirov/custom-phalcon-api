<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '1G');

use Phalcon\Config;
use Mvc\Bootstrap;

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
