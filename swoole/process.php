<?php
include dirname(__DIR__) . "/log.php";

if ($argc < 2 || $argv[1] == '--help') {
    printf("Usage: %s num \n", $argv[0]);
    exit;
}

$numLiveChildren = $argv[1]; // 还活着的子进程数

//ob_end_clean();

// 安装sigchld信号处理器
swoole_process::signal(SIGCHLD, function ($sig) use (&$data) {
    global $numLiveChildren;
//    必须为false，非阻塞模式（非阻塞 + signal异步处理）
    while ($ret = swoole_process::wait(false)) { // 回收子进程，否则子进程会变成僵尸进程浪费资源
        $pid = $ret['pid']; // 子进程ID
        echo "PID=$pid has exec \n";
        unset($data[$ret['pid']]);
        $numLiveChildren--;
        echo "num $numLiveChildren children live \n";
    }
    sleep(3);
});

$data = [];
for ($i = 0; $i < $argv[1]; $i++) {
    $process = new swoole_process(function (swoole_process $process) use ($i) {
        $process->name('child_process_' . $i); // 为进程命名，是swoole_set_process_name的别名
        log::w("child_process_$i");
        $process->exit(0);
    });
    $pid = $process->start(); // 执行fork调用，成功返回pid，失败返回false
    echo "PID=$pid fork success \n";
}

echo "waiting... \n";

// 阻塞等待子进程结束
//swoole_process::wait(true);


echo "end \n";