<?php
/**
 * 阻塞信号函数sigprocmask
 * Created by PhpStorm.
 * User: wuzc
 * Date: 17-12-6
 * Time: 下午9:17
 */

echo "安装信号处理器...\n";
pcntl_signal(SIGHUP,  function($signo) {
    echo "信号处理器被调用\n";
});

echo "设置阻塞SIGHUP信号 \n";
pcntl_sigprocmask(SIG_BLOCK, array(SIGHUP));

echo "为自己生成SIGHUP信号...\n";
posix_kill(posix_getpid(), SIGHUP);

echo "分发...\n";
pcntl_signal_dispatch();

echo "完成\n";
