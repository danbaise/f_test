<?php

namespace App\Classes;

use App\Core\Exception;

class Event
{
    public static $listens = [];

    public static function listen($event, $callback, $once = false)
    {
        if (!is_callable($callback)) {
            throw new Exception("不是可以调用的结构");
        }

        self::$listens[$event][] = ['callback' => $callback, 'once' => $once];
        return true;
    }

    public static function one($event, $callback)
    {
        return self::listen($event, $callback, true);
    }

    public static function remove($event, $k = null)
    {
        if (!is_null($k)) {
            unset(self::$listens[$event][$k]);
        } else {
            unset(self::$listens[$event]);
        }
    }

    public static function trigger($event, $parameter = [])
    {
        if (!isset(self::$listens[$event])) {
            throw new Exception("该事件没有被监听");
        }

        foreach (self::$listens[$event] as $k => $v) {
            $callback = $v['callback'];
            $v['once'] && self::remove($event, $k);
            call_user_func_array($callback, $parameter);
        }
    }

}