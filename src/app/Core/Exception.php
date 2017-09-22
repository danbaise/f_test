<?php

namespace App\Core;


use App\Classes\Logger;

class Exception extends \Exception
{
    public function __construct($message = "", $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function handleException($e)
    {
        $formateMessage = $this->formateTrace($e);
        Core::make('logger')->emergency($formateMessage);
    }

    public function formateTrace($e)
    {
        $trace
            = <<<eof
        
        Exception Message: {$e->getMessage()}
        Code: {$e->getCode()}
        File: {$e->getFile()}
        Line: {$e->getLine()}
        Trace:{$e->getTraceAsString()}
eof;
        return $trace;
    }

}