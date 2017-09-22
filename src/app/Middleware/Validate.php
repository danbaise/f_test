<?php

namespace App\Middleware;

use App\Core\Request;

class Validate implements Middleware
{
    public static function handle($request, \Closure $next)
    {
        //var_dump($request);
        //Request::set('p', 11, 'GET');
        echo ': 通过Validate' . PHP_EOL;
        $next($request);
    }

}