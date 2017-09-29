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
            'pipeline' => \App\Core\Pipeline::class,
            'error' => \App\Core\Error::class,
            'exception' => \App\Core\Exception::class,
            'route' => \App\Core\Route::class,
        ];

    protected $file
        = [
            APP_PATH . "/Helpers/Function.php",
            ROOT_PATH . "/config/route.php",
        ];

    public $namespace
        = [
            'Psr' => APP_PATH . "/Psr",
        ];

    public function bootstrap()
    {
        array_walk($this->file, array(Core::$loader, 'requireFile'));
        $flip = array_flip($this->namespace);
        array_walk($flip, array(Core::$loader, 'addNamespace'));
        array_walk(self::$bootstrap, array(Container::class, 'make'));
    }

    public static function provide($key)
    {
        if (isset(Kernel::$bootstrap[$key])) {
            return Container::make(Kernel::$bootstrap[$key]);
        }
        if (!isset(Container::$registry[$key]) && method_exists(Provider::class, $key)) {
            Provider::$key();
        }
        return Container::make($key);
    }

}
