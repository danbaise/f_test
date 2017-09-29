<?php

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

require APP_PATH . "/Core/Core.php";

$app = new \App\Core\Core();
$app->run();