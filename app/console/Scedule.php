<?php

namespace app\console;

/**
 * Class Scedule
 * @package app\console
 */
class Scedule
{
    public $action;
    public $timeOfDay = '00:00';//default once a day at 00:00
    public $timeOfHour = null;//once a hour at this minute
    public $everyMinute;

    /**
     * @param $action
     * @return Scedule
     */
    public static function add($action)
    {
        $scedule = new self();
        $scedule->action = array_merge([null], explode(' ', $action));
        return $scedule;
    }

    /**
     * @return bool
     */
    public function isExecutableThisTime()
    {
        if ($this->everyMinute) {
            return true;
        }
        $time = time();
        $minute = date('i', $time);
        if ($this->timeOfHour == $minute) {
            return true;
        }
        $timeOfDay = date('H:i', $time);
        if ($timeOfDay == $this->timeOfDay) {
            return true;
        }
    }

    /**
     * @param $time
     * @return $this
     */
    public function daylyAt($time)
    {
        $this->timeOfDay = $time;
        return $this;
    }

    /**
     * @return $this
     */
    public function hourly()
    {
        $this->timeOfHour = 0;
        return $this;
    }

    /**
     * @param $minute
     * @return $this
     */
    public function hourlyAt($minute)
    {
        $this->timeOfHour = $minute;
        return $this;
    }

    /**
     * @return $this
     */
    public function everyMinute()
    {
        $this->everyMinute = true;
        return $this;
    }

}