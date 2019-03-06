<?php

namespace app\components;

use app\App;
use app\Component;

/**
 * Class Help
 * @package app\components
 */
class Cache extends Component
{
    public $engine;

    /**
     * Db init
     */
    public static function init()
    {
        $instance = parent::init();

        $config = App::getComponent('config');
        $cacheName = $config->get('app.cache');
        $instance->engine = new $cacheName();

        return $instance;
    }

    /**
     * @param $key
     * @param $callback
     * @return mixed
     */
    public function getOrSet($key, $callback)
    {
        $value = $this->engine->get($key);
        if (!$value) {
            $value = $callback();
            $this->engine->set($key, $value);
        }
        return $value;
    }

    /**
     * clear cache
     */
    public function clear()
    {
        $this->engine->clear();
    }
}