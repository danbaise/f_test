<?php

namespace App\Route;

use App\Core\Route;
use \App\Middleware\Authenticate;
use \App\Middleware\Test;

//Route::add(array('index/test' => '\App\Controllers\Test@test'), $middleware = array(Authenticate::class, Test::class));
Route::add(array('index/test'=>'\App\Controllers\Test@test'));
Route::add(array('index/test3'=>'\App\Controllers\Test1@test'));
Route::add(array('/'=>'\App\Controllers\Test@test'));
