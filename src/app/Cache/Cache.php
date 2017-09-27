<?php

namespace App\Cache;

class Cache
{
    public static $handle = [];

    public static function set($cache)
    {
        self::$handle[get_class($cache)] = $cache;
    }

    public static function get($class)
    {
        return self::$handle[$class];
    }

}
