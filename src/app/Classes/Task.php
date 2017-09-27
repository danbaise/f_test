<?php

namespace App\Classes;

abstract class Task
{

    public static $data = [];

    abstract protected function execute($parameter);

    public static function add($name, $class, $parameter)
    {
        self::$data[] = ['name' => $name, 'class' => $class, 'parameter' => $parameter];
    }

    public static function handle($data)
    {
        return call_user_func_array([new $data['class'], 'execute'], $data['parameter']);
    }

}