#!/bin/bash
# 批量启动redis集群
# author: wuzhc2016@163.com
# date: 2017-09-13

confFiles=("6379.conf", "6380.conf", "6381.conf")
for file in ${confFiles}
do
    echo file
done