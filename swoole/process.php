<?php

$process = new swoole_process(function (swoole_process $process) {
    $process->name('child_process'); // 为进程命名，是swoole_set_process_name的别名
    // do something
    $process->write('1');
    $process->exit(0); // 0表示正常退出
});

$pid = $process->start(); // 执行fork调用，成功返回pid，失败返回false
if (false == $pid) {
    exit("fork failed \n");
}

// 安装sigchld信号处理器
//        swoole_process::signal(SIGCHLD, function ($sig) {
//            //必须为false，非阻塞模式（非阻塞 + signal异步处理）
//            while ($ret = swoole_process::wait(false)) { // 回收子进程，否则子进程会变成僵尸进程浪费资源
//                echo "PID={$ret['pid']}\n";
//            }
//        });

$data = $process->read();
print_r($data);