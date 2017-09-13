#!/bin/bash
# 批量启动redis集群
# author: wuzhc2016@163.com
# date: 2017-09-13

ports=(6379 6380 6381)
REDIS_SERVER=/usr/local/redis/bin/redis-server
REDIS_CLI=/usr/local/redis/bin/redis-cli

# redis 配置文件, pid文件
i=0
for port in ${ports[@]}
do
    portFiles[$i]="/usr/local/redis/etc/${port}.conf"
    pidFiles[$i]="/var/run/${port}.pid"
    i=`expr $i + 1`
done

start(){
    for port in ${ports[@]}
    do
        confFile="/usr/local/redis/etc/${port}.conf"
        pidFile="/var/run/${port}.pid"
        if [ -e pidFile ]
        then
            echo "redis ${port} running"
        else
            $REDIS_SERVER ${confFile}
        fi
        if [ $? eq 0 ]
        then
            echo "redis ${port} has run"
        else
            echo "redis ${port} start failed"
        fi
    done
}

stop(){
    for port in ${ports[@]}
    do
        confFile="/usr/local/redis/etc/${port}.conf"
        pidFile="/var/run/${port}.pid"
        if [ -e $pidFile ]
        then
            echo "redis ${port} stopping..."
            $REDIS_CLI -p ${port} shutdown
            sleep 2
            while [ -e $pidFile ]
            do
                echo "waiting for redis ${port} to shutdown"
            done
            echo "redis ${port} has shutdown"
        else
            echo "redis ${port} has shutdown"
        fi
    done
}

restart(){
    stop
    sleep(2)
    start
}

case $1 in:
    start)
        start
        ;;
    stop)
        stop
        ;;
    restart)
        restart
        ;;
    *)
        echo $"Usage: $0 {start|stop|status|restart}"
esac

