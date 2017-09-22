<?php

namespace App\Controllers;

use App\Classes\Task;
use App\Core\Config;
use App\Core\Container;
use App\Core\Exception;
use App\Core\Request;
use App\Core\Core;
use App\Core\Response;

class Test extends Controller
{
    public function test()
    {
        //   var_dump(Config::$data);
        //var_dump(Request::$data);

        Task::add(['name'=>'test','class' => \App\Task\Test::class, 'parameter' => ['1234']]);
        Task::add(['name'=>'test2','class' => \App\Task\Test::class, 'parameter' => ['3333']]);

/*        Core::make('logger')->log("warning","is waring");
        Core::make('logger')->error("is error");*/

        Response::output(json_encode(Config::$data));
        echo 'test';
    }
}