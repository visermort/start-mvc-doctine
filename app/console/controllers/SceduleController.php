<?php

namespace app\console\controllers;

use app\App;
use app\console\Scedule;
use app\console\Controller;

/**
 *  Schedule. To run by "Cron"
 */
class SceduleController extends Controller
{
    protected $scedules;

    /**
     * Action for "Cron". Put in to at every minute
     */
    public function actionRun()
    {
        $this->addScedules();
        $this->execute();
    }

    /**
     * add scedules here
     */
    protected function addScedules()
    {
        //daylyAt('20:05') default '00:00'
        //hourly()
        //hourlyAt(17)
        //everyMinute()
        $this->scedules[] = Scedule::add('test/test xxx bbb')->everyMinute();
    }

    protected function execute()
    {
        foreach ($this->scedules as $scedule) {
            if ($scedule->isExecutableThisTime()) {
                App::getInstance()->runConsole($scedule->action);
            }
        }
    }
}
