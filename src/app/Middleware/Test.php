<?php

namespace App\Middleware;


class Test implements MiddlewareInterface
{
    public static function handle($request, \Closure $next)
    {
        //var_dump($request);
        echo ': 通过Test' . PHP_EOL;
        $next($request);
    }

}