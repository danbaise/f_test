<?php

return [
    'db' => [
        'mysql' => [
            'default' => [
                'host' => "192.168.1.197",
                'username' => "root",
                'password' => "zhoujian",
                'db' => "test",
                'port' => 3306,
                'socket' => null,
                'charset' => "utf8",
            ]
        ],
        'redis' => [
            'host' => "127.0.0.1",
            'port' => 6379,
            'timeout' => 2.5,
        ]
    ]
];