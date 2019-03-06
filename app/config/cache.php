<?php

use app\App;
use Desarrolla2\Cache\Apcu as ApcuCache;
use app\classes\Help;

$cache = 'nocache'; //'file' 'apcu' 'memcached' 'nocache';
$fileCachePath = App::getRootPath() . '/app/runtime/disarolla/cache';

switch ($cache) {
    case ('file'):
        return [
            'create' => function () use ($fileCachePath) {
                return new Desarrolla2\Cache\File($fileCachePath);
            },
            'duration' => '3600',
            'clear' => function () use ($fileCachePath) {
                Help::clearDirectory($fileCachePath);
            }
        ];
        break;

    case ('memcached'):
        return [
            'create' => function () {
                $server = new \Memcached();
                $server->addServer("localhost", 11211);
                return new Desarrolla2\Cache\Memcached($server);
            },
            'duration' => '3600',
            'clear' => function () {
                App::getComponent('cache')->engine->clear();
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
                App::getComponent('cache')->engine->clear();
            }
        ];
        break;

    default:
        //nocache
        return [
            'create' => function () {
                return new Desarrolla2\Cache\NotCache();
            },

        ];

}
