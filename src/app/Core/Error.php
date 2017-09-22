<?php

namespace App\Core;

class Error
{
    public function errorException($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_PARSE:
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $error = 'Fatal Error';
                $log = LOG_ERR;
                break;
            case E_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
            case E_RECOVERABLE_ERROR:
                $error = 'Warning';
                $log = LOG_WARNING;
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $error = 'Notice';
                $log = LOG_NOTICE;
                break;
            case E_STRICT:
                $error = 'Strict';
                $log = LOG_NOTICE;
                break;
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $error = 'Deprecated';
                $log = LOG_NOTICE;
                break;
            default :
                break;
        }
        $formateMessage
            = <<<eof
      
        Error Message: $errstr   
        Code: {$errno}
        Error: {$error}
        
        File: {$errfile}
        Line: {$errline}
eof;

        Core::make('logger')->error($formateMessage);

        if ($errno == E_USER_ERROR) {
            exit("fatal error, exit!");
        }
    }

    public function shutdownFunction()
    {
        $error = error_get_last();
        if ($error) {
            $date = date("Y-m-d H:i:s");
            $formateMessage
                = <<<eof
                
        Last Error Message: {$error['message']}
        File: {$error['file']}
        Line: {$error['line']}
eof;

            Core::make('logger')->error($formateMessage);
        }
    }

}