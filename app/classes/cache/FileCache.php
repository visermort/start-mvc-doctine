<?php

namespace app\classes\cache;

use Desarrolla2\Cache\File;
use app\App;
use app\classes\Help;

class FileCache extends File
{

    public function __construct()
    {
        $directory = App::getRootPath() . '/app/runtime/disarolla/cache';
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        parent::__construct($directory);
    }

    public function clear()
    {
        Help::clearDirectory($this->cacheDir);
    }
}