<?php

namespace App\Core;

class Config
{

    public static $data = [];
    private static $ext = '.php';
    private static $configPath = ROOT_PATH . '/config/';

    public static function load($filename)
    {
        $baseConfigFilename = self::$configPath . $filename . self::$ext;

        if (file_exists($baseConfigFilename)) {
            self::$data = require $baseConfigFilename;
            return;
        }
        $env = self::get('debug', 'environment') ? 'Development' : 'Production';
        self::$data = array_merge(self::$data, require self::$configPath . $env . "/" . $filename . self::$ext);
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