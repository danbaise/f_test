#!/usr/bin/env bash

if [ $# -gt 0 ]; then

    case $1 in
    start)
        echo "start"
        php -f ../src/public/swoole.php
        ;;
    stop)
        echo "stop"
            ps -eaf |grep "swoole-" | grep -v "grep"| awk '{print $2}'|xargs kill -9
        ;;
    reload-work)
        echo "reload work 进程"
        kill -USR1 `lsof -t -i:$2`
        ;;
    reload-task)
        echo "reload task 进程"
        kill -USR2 `lsof -t -i:$2`
        ;;
    status)
        echo "status"
        netstat -ntlp
        ;;
    esac

else
     echo "Usage: sh $0 start|stop|reload-task|reload-work 9501|9502"
fi

