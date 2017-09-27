<?php

namespace App\Core;

use App\Database\MysqliDb;
use App\Cache\Cache;
use App\Cache\RedisClient;
use App\Cache\MemcachedClient;
use \Redis as Redis;
use \Memcached as Memcached;

class Provider
{
    public static function mysql()
    {
        Container::bind("mysql", function () {
            if (!MySqliDb::getInstance()) {
                new MysqliDb(Config::get('db', 'mysql', 'default'));
            }
            return MySqliDb::getInstance();
        });
    }

    public static function redis()
    {
        Container::bind("redis", function () {
            Cache::set(RedisClient::getInstance(Config::get('db', 'redis')));
            return Cache::get(Redis::class);
        });
    }

    public static function memcached()
    {
        Container::bind("memcached", function () {
            Cache::set(MemcachedClient::getInstance(Config::get('cache', 'memcached', 'servers'), Config::get('cache', 'memcached', 'setOption')));
            return Cache::get(Memcached::class);
        });
    }


}