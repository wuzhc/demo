#!/bin/bash
# 批量启动redis集群
# author: wuzhc2016@163.com
# date: 2017-09-13

ports=(6379 6380 6381)
REDIS_SERVER=/usr/local/redis/bin/redis-server
REDIS_CLI=/usr/local/redis/bin/redis-cli

# redis 配置文件, pid文件
i=0
for port in ${ports}
do
    portFiles[$i]="/usr/local/redis/etc/${port}.conf"
    pidFiles[$i]="/var/run/${port}.pid"
    i=`expr $i + 1`
done

for p in ${portFiles[@]}
do
    echo $p
done

for pid in ${pidFiles[@]}
do
    echo $pid
done
