<?php

namespace App\Server;

class ServerFactory
{

    const SWOOLE_HTTP = 'http';
    const SWOOLE_TCP = 'tcp';

    protected $serverType = 0;
    public $config = [];
    public $app;

    public function __construct($app)
    {
        $main = require_once APP_PATH . '/Config/main.php';
        $swoole = require_once APP_PATH . '/Config/swoole.php';
        $this->serverType = $main['main']['server_type'];
        $this->config = $swoole['swoole'][$this->serverType];
        $this->app = $app;
    }

    public function start()
    {
        switch ($this->serverType) {
            case self::SWOOLE_HTTP:
                require_once APP_PATH . '/Server/Http.php';
                $server = new Http();
                break;
            case self::SWOOLE_TCP:
                require_once APP_PATH . '/Server/Tcp.php';
                $server = new Tcp();
                break;
            default:
                return;
                break;
        }
        $server->setConf($this->config, $this->app);
        $server->start();
    }

}