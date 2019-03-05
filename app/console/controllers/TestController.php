<?php

namespace app\console\controllers;

use app\App;
use app\console\Controller;

/**
 *  test controller description
 */
class TestController extends Controller
{
    /**
     * test action description
     */
    public function actionTest($arg1 = null, $arg2 = null)
    {
        echo 'testController/actionTest  arg1='.$arg1. ' arg2='.$arg2."\n";
        echo 'is console ' . App::isConsole();
    }
    /**
     * test-and-test action description
     */
    public function actionTestAndTest()
    {
        echo 'testContrller/actionTestAndTest'."\n";
        echo 'is console ' . App::isConsole();
    }
}
