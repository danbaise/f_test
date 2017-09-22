<?php

namespace App\Core;

use App\Core\Exception as Exception;

class Route
{
    public static $routes = [];

    public static function add($array, $middleware = [])
    {
        list($class, $method) = explode('@', end($array));
        self::$routes[key($array)] = ['class' => $class, 'method' => $method, 'middleware' => $middleware];
    }

    public static function dispatch()
    {
        if (isset(self::$routes[Request::$data['path_info']]) && $call = self::$routes[Request::$data['path_info']]) {
            empty($call['middleware'])
                ? call_user_func([new $call['class'], $call['method']])
                : Core::make('pipeline')->send(Request::$data)->through($call['middleware'])->then(function () use ($call) {
                return call_user_func([new $call['class'], $call['method']]);
            });
        } else {
            throw new Exception('The call class or method cannot be found');
        }
    }

}