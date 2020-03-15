<?php
declare(strict_types=1);

namespace Test\Controller;

use Common\Abstraction\AbstractController;

class NewController extends AbstractController
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