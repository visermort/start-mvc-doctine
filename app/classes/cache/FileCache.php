<?php

namespace app\classes\cache;

use Desarrolla2\Cache\File;
use app\App;
use app\classes\Help;

class FileCache extends File
{

    public function __construct()
    {
        parent::__construct(App::getRootPath() . '/app/runtime/disarolla/cache');
    }

    public function clear()
    {
        Help::clearDirectory($this->cacheDir);
    }
}