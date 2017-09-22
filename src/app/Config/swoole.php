<?php


return [
    'swoole' => [
        'http' => [
            'ps_name' => 'swoole-http',
            'host' => '0.0.0.0',
            'port' => 9501,
            'worker_num' => 8,
            'task_worker_num' => 4,
            //'daemonize' => true,
            'pid_file' => '../../bin/server.pid',
        ],
        'tcp' => [
            'ps_name' => 'swoole-tcp',
            'host' => '0.0.0.0',
            'port' => 9502,
            'worker_num' => 8,
            'task_worker_num' => 4,
            'pid_file' => '../../bin/server.pid',
/*            'heartbeat_check_interval' => 5,
            'heartbeat_idle_time' => 10,*/
            //'daemonize' => true,
        ]
    ]
];