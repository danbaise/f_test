<?php

namespace App\Middleware;


class Test implements Middleware
{
    public static function handle($request, \Closure $next)
    {
        //var_dump($request);
        echo ': 通过Test' . PHP_EOL;
        $next($request);
    }

}