<?php

use app\App;
use Desarrolla2\Cache\Adapter\Apcu as ApcuCache;

//$cache = 'file';
$cache = 'apcu';
//$cache = 'memcached';
//$cache = 'nocache';

switch ($cache) {
    case ('file'):
        return [
            'create' => function () {
                $path = App::getRequest('root_path') . '/app/runtime/disarolla/cache';
                return new Desarrolla2\Cache\Adapter\File($path);
            },
            'duration' => '3600',
            'clear' => function () {
                $path = App::getRequest('root_path') . '/app/runtime/disarolla/cache';
                App::getComponent('fileutils')->clearDirectory($path);
            }
        ];
        break;

    case ('memcached'):
        return [
            'create' => function () {
                $server = new \Memcached();
                $server->addServer("localhost", 11211);
                return new Desarrolla2\Cache\Adapter\Memcached($server);
            },
            'duration' => '3600',
            'clear' => function () {
                App::getComponent('cache')->engine->flush();
            }
        ];
        break;

    case ('apcu'):
        return [
            'create' => function () {
                $apcu = new ApcuCache();
                return $apcu;
            },
            'clear' => function () {
                apcu_clear_cache();
            }
        ];
        break;

    default:
        //nocache
        return [
            'create' => function () {
                return new Desarrolla2\Cache\Adapter\NotCache();
            },

        ];

}
