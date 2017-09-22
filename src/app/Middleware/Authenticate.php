<?php

namespace App\Middleware;

use App\Core\Request;

class Authenticate
{
    public static function handle($request, \Closure $next)
    {
        echo  ': 通过Authenticate' . PHP_EOL;
        $next($request);
    }

}