<?php
declare(strict_types=1);

namespace Test\Controller;

use Common\Defaults\BaseController;

class NewController extends BaseController
{
    private TestController $testController;

    public function __construct(TestController $testController)
    {
        parent::__construct();

        $this->testController = $testController;
    }

    public function runTest()
    {
        echo $this->testController->index();
    }
}
