<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年12月07日
 * Time: 10:40
 */

$pid = posix_getpid();
echo "parent pid = $pid \n";

swoole_process::signal(SIGCHLD, function($sig){
    echo "caught signal $sig \n";
});

for ($i = 0; $i < 5; $i++) {
    $process = new swoole_process(function (swoole_process $process) {
        echo "child pid = $process->pid \n";
        $process->exit(0);
    });
}

$total = 0;
while ($ret = swoole_process::wait(false)) { // 回收子进程，否则子进程会变成僵尸进程浪费资源
    $total++;
}
echo "$total child process exit";
exit;

