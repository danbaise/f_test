<?php

namespace App\Core;

class Response
{
    public static $data;

    public static function output($data)
    {
        self::$data = $data;
    }

}