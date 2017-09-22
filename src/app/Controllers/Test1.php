<?php

namespace App\Controllers;

use App\Classes\Task;
use App\Core\Config;
use App\Core\Request;
use App\Core\Core;
use App\Core\Response;

class Test1 extends Controller
{
    public function test()
    {
        //   var_dump(Config::$data);
        //var_dump(Request::$data);

        Task::add(['name'=>'test','class' => \App\Task\Test::class, 'parameter' => ['1234']]);
        Task::add(['name'=>'test2','class' => \App\Task\Test::class, 'parameter' => ['3333']]);

        Response::output('21111222');
        echo 'test';
    }

}