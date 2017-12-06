<?php
/**
 * Created by PhpStorm.
 * User: wuzc
 * Date: 17-12-6
 * Time: 下午10:02
 */
swoole_set_process_name("wuzhc-master");
echo "当前进程ID为：" . posix_getpid() . "\n";


swoole_process::signal(SIGINT, function($sig){
   echo "signal=$sig handle \n";
});

posix_kill(posix_getpid(), SIGINT);
