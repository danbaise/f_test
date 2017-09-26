<?php

namespace App\Controllers;

use App\Classes\Event;
use App\Classes\AbstractTask;
use App\Core\Config;
use App\Core\Container;
use App\Core\Exception;
use App\Core\Request;
use App\Core\Core;
use App\Core\Response;
use App\Database\MysqliDb;

class Test extends Controller
{

    public function event()
    {
        Event::listen('test', function ($a, $b) {
            echo $a + $b;
            return $a;
        });
    }

    public function test()
    {
        /*        var_dump(Config::$data);
                var_dump(Request::$data);*/

        AbstractTask::add('test', \App\Task\Test::class, ['1234']);

        /*        Core::make('logger')->log("warning","is waring");
                Core::make('logger')->error("is error");*/

        //    var_dump(Container::$registry);
        $result = Core::make('mysql')->get('name');
        var_dump($result);

        Container::bind("haha", function () {
            echo "lalala";
        });

        Container::make("haha");

        $result = Event::trigger('test', array(1, 2));
        var_dump($result);

        Response::output(json_encode(Config::$data));

        echo 'test';
    }


}