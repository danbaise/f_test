<?php

namespace App\Core;

use App\Database\MysqliDb;

class Provider
{
    public static function mysql()
    {
        Container::bind("mysql", function () {
            if (!MySqliDb::getInstance()) {
                new MysqliDb(Config::get('db', 'mysql', 'default'));
            }
            return MySqliDb::getInstance();
        });
    }

}