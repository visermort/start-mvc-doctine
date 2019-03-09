<?php

namespace app\console\controllers;

use app\console\Queue;
use app\console\Controller;

/**
 *  Queues
 */
class QueueController extends Controller
{

    /**
     * List of queue
     */
    public function actionList()
    {
        Queue::list();
    }

    /**
     * Clear all queues
     */
    public function actionClear()
    {
        Queue::clear();
    }

    /**
     * Serve whole queue
     */
    public function actionRun()
    {
        Queue::serve();
    }
}
