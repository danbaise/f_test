<?php

namespace App\Cache;

use \Memcached as Memcached;

class MemcachedClient extends Cache
{
    public static $_instance;

    public static function getInstance($servers, $setOption = null)
    {
        if (null === self::$_instance) {
            self::$_instance = new Memcached();
            if ($setOption) {
                self::$_instance->setOption($setOption);
            }
            self::$_instance->addServers($servers);
        }
        return self::$_instance;
    }
}