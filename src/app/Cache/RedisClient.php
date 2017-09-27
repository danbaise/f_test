<?php

namespace App\Cache;

use \Redis as Redis;

class RedisClient extends Cache
{
    public static $_instance;

    public static function getInstance($config)
    {
        if (null === self::$_instance) {
            self::$_instance = new Redis();
            self::$_instance->connect($config['host'], $config['port'], $config['timeout']);
        }
        return self::$_instance;
    }

}