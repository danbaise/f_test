<?php

namespace App\Server;

use App\Classes\Task;
use App\Core\Request;
use App\Core\Response;


class Http
{
    /**
     * swoole http-server 实例
     *
     * @var null | swoole_http_server
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

    public function getConf()
    {
        return $this->setting;
    }

    public function input($request)
    {
        $server_protocol = substr($request->server['server_protocol'], 0, 5) == 'HTTP/' ? "http" : "https";
        $url = $server_protocol . "://" . $request->header['host'] . $request->server['request_uri'];
        return Request::$data = [
            'get' => (isset($request->get) && !empty($request->get)) ? $request->get : null,
            'post' => (isset($request->post) && !empty($request->post)) ? $request->post : null,
            'rawData' => file_get_contents('php://input', 'r'),
            'url' => $url,
            'path_info' => Request::$root == '/' ? '/' : ltrim($request->server['path_info'], '/'),
            'request' => $request,
        ];
    }

    public function start()
    {
        $this->server = new \swoole_http_server($this->setting['host'], $this->setting['port']);

        $this->server->set($this->setting);
        //回调函数
        $call = [
            'start',
            'workerStart',
            'managerStart',
            'request',
            'task',
            'finish',
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
        echo 'Date:' . date('Y-m-d H:i:s') . "\t swoole_http_server master worker start\n";
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
        echo 'Date:' . date('Y-m-d H:i:s') . "\t swoole_http_server manager worker start\n";
        $this->setProcessName($server->setting['ps_name'] . '-manager');
    }

    /**
     * swoole-server master shutdown
     */
    public function onShutdown()
    {
        echo 'Date:' . date('Y-m-d H:i:s') . "\t swoole_http_server shutdown\n";
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
        echo 'Date:' . date('Y-m-d H:i:s') . "\t swoole_http_server[{$server->setting['ps_name']}] worker:{$workerId} shutdown\n";
    }

    /**
     * http请求处理
     *
     * @param $request
     * @param $response
     *
     * @return mixed
     */
    public function onRequest($request, $response)
    {
        //获取swoole服务的当前状态
        if (isset($request->get['cmd']) && $request->get['cmd'] == 'status') {
            $res = $this->server->stats();
            $res['start_time'] = date('Y-m-d H:i:s', $res['start_time']);
            $response->end(json_encode($res));
            return true;
        }

        //通过管道
        $this->app->pipeline($this->input($request));

        //task处理
        if (!empty(Task::$data)) {
            foreach (Task::$data as $key => $value) {
                $this->server->task($value);
            }
        }
        //$this->server->task(Task::$data);
        //TODO 非task请求处理
        $response->end(Response::$data);

        /*      $out = '[' . date('Y-m-d H:i:s') . '] ' . json_encode($request) . PHP_EOL;
              //INFO 立即返回 非阻塞
              $response->end($out);*/
        return true;
    }

    /**
     * 任务处理
     *
     * @param $server
     * @param $taskId
     * @param $fromId
     * @param $request
     *
     * @return mixed
     */
    public function onTask($server, $taskId, $fromId, $request)
    {
        //任务执行 worker_pid实际上是就是处理任务进程的task进程id
        $ret = [];

        $ret['task'] = Task::call($request);
        $ret['name'] = $request['name'];
        $ret['fromId'] = $fromId;
        $ret['taskId'] = $taskId;
        $ret['worker_pid'] = $server->worker_pid;

        //INFO swoole-1.7.18之后return 就会自动调用finish
        return $ret;
    }

    /**
     * 任务结束回调函数
     *
     * @param $server
     * @param $taskId
     * @param $ret
     */
    public function onFinish($server, $taskId, $ret)
    {
        $fromId = $server->worker_id;
        if ($ret['task']['errno'] != 0) {
            $error = PHP_EOL . var_export($ret, true);
            echo "\tTask: {$ret['name']} [taskId:$fromId#{$taskId}] failed, Error[$error]" . PHP_EOL;
        } else {
            //任务成功运行提示
            echo "\tTask: {$ret['name']} [taskId:{$taskId}] success" . PHP_EOL;
        }

    }

    public function onWorkerError( $server,  $worker_id,  $worker_pid,  $exit_code,  $signal)
    {

    }

}