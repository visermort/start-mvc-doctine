<?php

namespace app\console;

use app\App;
use app\classes\Help;

/**
 * Class Queue
 * @package app\console
 */
class Queue
{
    public static $path = '/runtime/queue';
    protected $instance;
    protected $action;

    /**
     * @param $instance
     * @param $action
     */
    public function __construct($instance, $action)
    {
        $this->instance = $instance;
        $this->action = $action;
        $filePath = App::getRootPath() . self::$path;
        if (!file_exists($filePath)) {
            mkdir($filePath);
        }
        $filePath .= ('/' . date('Y_m_d_H_i_s', time()) . '.txt');
        file_put_contents($filePath, serialize($this));
    }

    /**
     * @return bool
     */
    public function execute()
    {
        try {
            $method = $this->action;
            if (method_exists($this->instance, $method)) {
                $this->instance->$method();
            }
            return true;
        } catch (\Exception $e) {
            if (App::isConsole()) {
                echo $e->getMessage() . "in" . $e->getFile() . " on line " . $e->getLine() . "\n";
            }
        }
        return false;
    }

    /**
     * serve queue
     */
    public static function serve()
    {
        $path = App::getRootPath() . self::$path;
        if (!file_exists($path)) {
            return;
        }
        $files = Help::scanDirectory($path);
        foreach ($files as $file) {
            $queue = unserialize(file_get_contents($path . '/' . $file));
            if ($queue->execute()) {
                unlink($path . '/' . $file);
            }
        }
    }

    /**
     * list of queue
     */
    public static function list()
    {
        $path = App::getRootPath() . self::$path;
        if (!file_exists($path)) {
            return;
        }
        $files = Help::scanDirectory($path);
        foreach ($files as $file) {
            $queue = unserialize(file_get_contents($path . '/' . $file));
            echo 'class '. get_class($queue->instance) . ' method '.$queue->action."\n";
        }
    }

    /**
     * clear all queue
     */
    public static function clear()
    {
        $path = App::getRootPath() . self::$path;
        Help::clearDirectory($path);
    }
}