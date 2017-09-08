<?php

use Workerman\Worker;
use Workerman\Lib\Timer;
require_once '../workerman/Autoloader.php';

$worker = new Worker('tcp://0.0.0.0:8585');
$worker->count = 4;
$worker->onWorkerStart = function($worker)
{
    // 只在id编号为0的进程上设置定时器，其它1、2、3号进程不设置定时器
    if($worker->id === 0)
    {
        Timer::add(3, function(){
            $time = time();
            echo "4 worker processes, only 0 timer $time\n";
        });
    }
};
// 运行worker
Worker::runAll();