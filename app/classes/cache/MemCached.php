<?php

namespace app\classes\cache;

use Desarrolla2\Cache\Memcached as DisarollaMemcached;

class MemCached extends DisarollaMemcached
{
    protected $cachePath;

    public function __construct()
    {
        $server = new \Memcached();
        $server->addServer("localhost", 11211);
        parent::__construct($server);
    }

}