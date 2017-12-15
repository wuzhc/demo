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
        echo "pid = $process->pid run \n";
        $process->exit(0);
    });
    if ($pid = $process->start()) {
        echo "pid = $pid fork success \n";
    }
}

$total = 0;
while ($ret = swoole_process::wait(true)) {
    $total++;
}
echo "$total child process exit \n";
exit(0);

