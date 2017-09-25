<?php

namespace App\Middleware;

interface MiddlewareInterface
{
    public static function handle($request, \Closure $closure);
}