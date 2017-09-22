<?php

namespace App\Classes;

abstract class Task
{

    public static $data = [];

    abstract protected function execute($parameter);

    public static function add($info)
    {
        self::$data[] = $info;
    }

    public static function call($data)
    {
        return call_user_func_array([new $data['class'], 'execute'], $data['parameter']);
    }

}