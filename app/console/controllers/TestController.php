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
        $message = 'testController/actionTest  arg1='.$arg1. ' arg2='.$arg2."\n";
        $message .=  'is console ' . App::isConsole();
        echo $message;
//        $path = App::getRootPath() . '/runtime/test_scedule_' .
//            preg_replace('/[\s\:\-]/', '_', date('Y-m-d H:i:s', time())) .
//            '.txt';
//        file_put_contents($path, $message);

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
