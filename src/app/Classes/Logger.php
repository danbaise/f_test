<?php

namespace App\Classes;

use App\Core\Config;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    public function log($level, $message, array $context = array())
    {
        // TODO: Implement log() method.

        if (!empty($context)) {
            $message = $this->interpolate($message, $context);
        }
        $formateMessage = $this->formateMessage($level, $message);

        if (Config::get('debug', 'environment')) {
            var_dump($formateMessage);
        }
        return $this->write($formateMessage, $this->getPath());
    }

    /**
     * 用上下文信息替换记录信息中的占位符
     */
    public function interpolate($message, $context = array())
    {
        // 构建一个花括号包含的键名的替换数组
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // 替换记录信息中的占位符，最后返回修改后的记录信息。
        return strtr($message, $replace);
    }

    public function formateMessage($level, $message)
    {
        $date = date("Y-m-d H:i:s");
        $formateMessage
            = <<<eof
            
begin:Logger
Level: {$level}
Date: {$date}
Message: {$message}

end:
----------------------------------------------------------------

eof;
        return $formateMessage;
    }

    public function write($formateMessage, $dst)
    {
        $fp = fopen($dst, "a+");
        $written = fwrite($fp, $formateMessage);
        fclose($fp);
        return $written;
    }

    public function getPath()
    {
        $log_path = Config::get('main', 'log_path');
        $dir = $log_path . "/" . date("Ym");
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $file = $dir . "/" . date('d') . ".log";
        return $file;
    }


}