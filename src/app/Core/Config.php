<?php

namespace App\Core;

class Config
{

    public static $data = [];
    private static $ext = '.php';

    protected static $initFile
        = [
            APP_PATH . '/Config/main.php',
            APP_PATH . '/Config/debug.php',
            APP_PATH . '/Config/swoole.php',
        ];

    public function __construct()
    {
        $this->initRequire();
    }

    public static function load($filename)
    {
        $env = self::get('debug', 'environment') ? 'Development' : 'Production';
        self::$data = array_merge(self::$data, require APP_PATH . "/Config/{$env}/" . $filename . self::$ext);
    }


    public function initRequire()
    {
        foreach (self::$initFile as $value) {
            self::$data = array_merge(self::$data, require $value);
        }
    }

    public static function get(...$args)
    {
        if (!isset(self::$data[$args[0]])) {
            self::load($args[0]);
        }
        $tmp = self::$data;
        if (!empty($args)) {
            foreach ($args as $value) {
                $tmp = $tmp[$value];
            }
            return $tmp;
        }
        return null;
    }


}