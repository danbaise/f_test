<?php

define('APP_PATH', dirname(__DIR__) . '/app');
require APP_PATH . "/Core/Core.php";

$app = new \App\Core\Core();
$app->loadClass();
$server = new \App\Server\ServerFactory($app);
$server->start();