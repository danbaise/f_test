<?php

namespace App\Controllers;


use App\Classes\Event;
use App\Classes\Task;
use App\Core\Config;
use App\Core\Container;
use App\Core\Exception;
use App\Core\Request;
use App\Core\Core;
use App\Core\Response;
use App\Database\MysqliDb;
use \App\Cache\RedisClient;

class Test extends Controller
{

    public function event()
    {
        Event::one('test', function ($a, $b) {
            echo $a + $b;
            return $a;
        });

        Event::one('test', function ($a, $b) {
            echo $a * $b;
            return $a;
        });
    }

    public function test()
    {
        /*        var_dump(Config::$data);
                var_dump(Request::$data);*/

        //Task::add('test', \App\Task\Test::class, ['1234']);

        /*        Core::make('logger')->log("warning","is waring");
                Core::make('logger')->error("is error");*/

        //    var_dump(Container::$registry);
/*        $result = Core::make('mysql')->get('name');
        var_dump($result);*/

/*        $result = Core::make('redis')->flushAll();
        var_dump($result);

        var_dump(Core::make('memcached'));*/

/*        Core::make("server")->send(Request::$data['fd'], Request::$data['fd'] . json_encode(Request::$data['data']) . "\n");

        foreach(Core::make("server")->connections as $tempFD)
        {
            Core::make("server")->close($tempFD);
        }*/

    //    throw new Exception("8888");

        Event::trigger('test', array(1, 2));
        var_dump(Event::$listens);

        Response::output(json_encode(Config::$data));

        echo 'Hello World!';
    }


}