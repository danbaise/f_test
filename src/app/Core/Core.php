<?php

namespace App\Core;

class Core
{
    public static $loader;

    public function run()
    {
        $this->loadClass();
        $this->bootstrap();
        $this->setConf();
        $this->pipeline(Core::make('request')->input());
    }

    public function setConf()
    {
        Core::make('request')->setRoot(Core::make('config')->get('main', 'web_path'));
        set_exception_handler(array(Core::make('exception'), "handleException"));
        set_error_handler(array(Core::make('error'), "errorException"));
        register_shutdown_function(array(Core::make('error'), "shutdownFunction"));
    }

    public function bootstrap()
    {
        Container::make(Kernel::class)->bootstrap();
    }

    public function pipeline($input)
    {
        Core::make('pipeline')->send($input)->through(Kernel::$middleware)->then(function () {
            return Route::dispatch();
        });
    }

    public static function make($key)
    {
        return Container::make(Kernel::$bootstrap[$key]);
    }

    public function loadClass()
    {
        require_once APP_PATH . "/Psr/Psr4Autoloader.php";
        self::$loader = new \Psr4Autoloader();
        self::$loader->register();
        self::$loader->addNamespace('App', APP_PATH);
    }

}