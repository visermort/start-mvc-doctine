<?php

namespace app\components;

use app\App;
use app\Component;
/**
 * Class Config
 * @package app\components
 */
class Config extends Component
{
    private $config = [];

    /**
     * Db init
     */
    public static function init()
    {
        $instance = parent::init();
        $configDirectory = App::getRootPath() . '/app/config';
        $configFiles = array_diff(scandir($configDirectory), ['.', '..']);
        foreach ($configFiles as $configFile) {
            if (is_file($configDirectory . '/' . $configFile)) {
                $fileKey = strtolower(str_replace('.php', '', $configFile));
                $configs = include $configDirectory . '/' . $configFile;
                foreach ($configs as $key => $config) {
                    $instance->config[$fileKey . '.' . $key] = $config;
                }
            }
        }
        return $instance;
    }

    /**
     * get config by file.config
     * @param $name
     * @return mixed|null
     */
    public function get($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : null;
    }

    /**
     * get a whole section of config
     * @param $sectionName
     * @return array
     */
    public function getSection($sectionName)
    {
        $out = [];
        foreach ($this->config as $key => $config) {
            if (strpos($key, $sectionName . '.') === 0) {
                $out[$key] = $config;
            }
        }
        return $out;
    }

    /**
     * meta config can changed from outside
     * @param $name
     * @param $value
     */
    public function setMeta($name, $value)
    {
        $this->config['meta.'.$name] = $value;
    }

}