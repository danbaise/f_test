<?php

namespace App\Server;

use App\Classes\Task;
use App\Core\Request;
use App\Core\Response;

class Tcp
{
    /**
     * swoole tcp-server 实例
     */
    private $server = null;
    /**
     * swoole 配置
     *
     * @var array
     */
    private $setting = [];

    public $app;

    /**
     * 修改swooleTask进程名称，如果是macOS 系统，则忽略(macOS不支持修改进程名称)
     *
     * @param $name 进程名称
     *
     * @return bool
     * @throws \Exception
     */
    private function setProcessName($name)
    {
        if (PHP_OS == 'Darwin') {
            return false;
        }
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($name);
        } else {
            if (function_exists('swoole_set_process_name')) {
                swoole_set_process_name($name);
            } else {
                throw new \Exception(__METHOD__ . "failed,require cli_set_process_title|swoole_set_process_name");
            }
        }
    }


    public function setConf($conf, $app)
    {
        //TODO conf配置检查
        $this->app = $app;
        $this->setting = $conf;
    }


    public function start()
    {
        $this->server = new \swoole_server($this->setting['host'], $this->setting['port'], $mode = SWOOLE_PROCESS, $sock_type = SWOOLE_SOCK_TCP);

        $this->server->set($this->setting);
        //回调函数
        $call = [
            'connect',
            'receive',
            'task',
            'finish',
            'close',
            'start',
            'managerStart',
            'workerStart',
            'workerStop',
            'shutdown',
            'workerError',
        ];
        //事件回调函数绑定
        foreach ($call as $v) {
            $m = 'on' . ucfirst($v);
            if (method_exists($this, $m)) {
                $this->server->on($v, [$this, $m]);
            }
        }
        $this->server->start();
    }

    /**
     * swoole-server master start
     *
     * @param $server
     */
    public function onStart($server)
    {
        echo 'Date:' . date('Y-m-d H:i:s') . "\t swoole_tcp_server master worker start\n";
        $this->setProcessName($server->setting['ps_name'] . '-master');
        //记录进程id,脚本实现自动重启
        $pid = "{$this->server->master_pid}\n{$this->server->manager_pid}";
    }

    /**
     * manager worker start
     *
     * @param $server
     */
    public function onManagerStart($server)
    {
        echo 'Date:' . date('Y-m-d H:i:s') . "\t swoole_tcp_server manager worker start\n";
        $this->setProcessName($server->setting['ps_name'] . '-manager');
    }

    /**
     * swoole-server master shutdown
     */
    public function onShutdown()
    {
        echo 'Date:' . date('Y-m-d H:i:s') . "\t swoole_tcp_server shutdown\n";
    }

    /**
     * worker start 加载业务脚本常驻内存
     *
     * @param $server
     * @param $workerId
     */
    public function onWorkerStart($server, $workerId)
    {

        //加载框架
        $this->app->bootstrap();
        $this->app->setConf();
        restore_exception_handler();

        if ($workerId >= $this->setting['worker_num']) {
            $this->setProcessName($server->setting['ps_name'] . '-task');
        } else {
            $this->setProcessName($server->setting['ps_name'] . '-work');
        }
    }

    /**
     * worker 进程停止
     *
     * @param $server
     * @param $workerId
     */
    public function onWorkerStop($server, $workerId)
    {
        echo 'Date:' . date('Y-m-d H:i:s') . "\t swoole_tcp_server[{$server->setting['ps_name']}] worker:{$workerId} shutdown\n";
    }

    public function onConnect($server, $fd, $from_id)
    {
        echo "Client:Connect.\n";
    }

    public function onReceive($server, $fd, $reactor_id, $data)
    {

        Request::$data = json_decode($data, true);
        //通过管道
        $this->app->pipeline(Request::$data);
        $this->server->send($fd, 'Swoole: ' . json_encode(Response::$data));
        //$this->server->close($fd);
    }

    public function onClose($server, $fd, $reactorId)
    {
        echo "Client: Close.\n";
    }

    function onTask($server, $task_id, $src_worker_id, $data)
    {
        echo 'task';
    }

    function onFinish($server, $task_id, $data)
    {
        echo 'finish';
    }

    public function onWorkerError($server, $worker_id, $worker_pid, $exit_code, $signal)
    {
        var_dump($worker_id, $worker_pid, $exit_code, $signal);
    }


}