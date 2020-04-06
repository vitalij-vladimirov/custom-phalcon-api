<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Dice\Dice;
use Phalcon\Config;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected Config $config;
    protected Dice $di;

    public function __construct()
    {
        parent::__construct();

        $this->config = $GLOBALS['config'];
        $this->di = new Dice();
    }
}
