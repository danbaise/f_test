<?php

namespace App\Classes;

use App\Core\Exception;

class Event
{
    public static $listens = [];

    public static function listen($event, $callback)
    {
        if (!is_callable($callback)) {
            throw new Exception("不是可以调用的结构");
        }

        self::$listens[$event] = ['callback' => $callback];
    }

    public static function trigger($event, $parameter = [])
    {
        if (!isset(self::$listens[$event])) {
            throw new Exception("该事件没有被监听");
        }

        return call_user_func_array(self::$listens[$event]['callback'], $parameter);
    }

}