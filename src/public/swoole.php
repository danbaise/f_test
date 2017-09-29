<?php

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

require APP_PATH . "/Core/Core.php";

if (count($argv) < 2 && !in_array($argv[1], ['http', 'tcp'])) {
    die("useage: php swoole.php http|tcp" . PHP_EOL);
}

$app = new \App\Core\Core();
$app->loadClass();
$server = new \App\Server\ServerFactory($app, $argv[1]);
$server->start();