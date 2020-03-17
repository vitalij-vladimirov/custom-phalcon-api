<?php
declare(strict_types=1);

namespace BaseMvc;

use Dotenv\Dotenv;
use Phalcon\Config;
use Phalcon\Di;

/**
 * Local variables
 * @var Config $config
 * @var Di $di
 */

include '/app/vendor/autoload.php';
(Dotenv::createImmutable('/app/'))->load();

include '/app/mvc/services.php';
$config = $di->getConfig();

include '/app/mvc/loader.php';
