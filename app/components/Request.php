<?php

namespace app\components;

use app\App;
use app\Component;
/**
 * Class Request
 * @package app\components
 */
class Request extends Component
{
    private $data;

    /**
     * Db init
     */
    public static function init()
    {
        $instance = parent::init();
        $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $path = explode('?', $url);
        $instance->data = [
           'server' => $_SERVER,
           'get' => $_GET,
           'post' => $_POST,
           'file' => $_FILES,
           'root_path' => realpath(__DIR__ . '/../../'),
           'url' => $url,
           'path' => rawurldecode($path[0]),
           'method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '',
           'isAjax' => !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest',
        ];
        return $instance;
    }

    public function get($key)
    {
        $keyArray = explode('.', $key);
        if (isset($keyArray[1])) {
            return isset($this->data[$keyArray[0]][$keyArray[1]]) ? $this->data[$keyArray[0]][$keyArray[1]] : null;
        }
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }


}