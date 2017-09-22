<?php

namespace App\Core;

class Kernel
{
    /**
     * The application's middleware.
     *
     * @var array
     */
    public static $middleware
        = [
            \App\Middleware\Validate::class,
            //\App\Middleware\Authenticate::class,
        ];


    public static $bootstrap
        = [
            'core' => \App\Core\Core::class,
            'request' => \App\Core\Request::class,
            'config' => \App\Core\Config::class,
            'pipeline' => \App\Core\Pipeline::class,
            'error' => \App\Core\Error::class,
            'exception' => \App\Core\Exception::class,
            'logger' => \App\Classes\Logger::class,
            'route' => \App\Core\Route::class,
        ];

    protected $file
        = [
            APP_PATH . "/Helpers/Function.php",
            APP_PATH . "/Route/Web.php",
        ];

    public $namespace
        = [
            'Psr' => APP_PATH . "/Psr",
        ];

    public function bootstrap()
    {
        $this->loadFile();
        $flip = array_flip($this->namespace);
        array_walk($flip, array(Core::$loader, 'addNamespace'));
        array_walk(self::$bootstrap, array(Container::class, 'make'));
    }

    public function loadFile()
    {
        foreach ($this->file as $value) {
            require $value;
        }
    }

}
