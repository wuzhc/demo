<?php
/**
 * 注册信号处理器
 * Created by PhpStorm.
 * User: wuzc
 * Date: 17-12-6
 * Time: 下午9:30
 */

function sigHandle($sig)
{
    switch ($sig) {
        case SIGUSR1:
            echo "SIGUSR1=$sig handle \n";
            break;
        case SIGUSR2:
            echo "SIGUSR2=$sig handle \n";
            break;
    }
//    sleep(3);
}

pcntl_signal(SIGUSR1, 'sigHandle');
pcntl_signal(SIGUSR2, 'sigHandle');

// 暂停进程的执行，直至信号处理器中断该调用为止
echo $pid = posix_getpid();
echo "\n";

//for ($i=0;$i<3;$i++){
//    posix_kill(posix_getpid(), SIGUSR1);
//}

sleep(30);
pcntl_signal_dispatch();