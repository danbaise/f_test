<?php

use App\Core\Route;
use \App\Middleware\Authenticate;
use \App\Middleware\Test;

//Route::add(array('index/test' => '\App\Controllers\Test@test'), $middleware = array(Authenticate::class, Test::class));
Route::add(['index/test' => '\App\Controllers\Test@test']);
Route::add(['/' => '\App\Controllers\Test@test'], $middleware = [Authenticate::class, Test::class]);
