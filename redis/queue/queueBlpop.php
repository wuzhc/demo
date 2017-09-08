<?php

$redis = new Redis();
$redis->connect('127.0.0.1');

// 出队
while (true) {
    // 阻塞设置超时时间为3秒
    $task = $redis->blPop(array('goods:task'), 3);
    if ($task) {
        $redis->rPush('goods:success:task', $task[1]);
        $task = json_decode($task[1], true);
        echo $task['id'] . ':' . $task['cid'] . ':' . 'handle success';
        echo PHP_EOL;
    } else {
        echo 'nothing' . PHP_EOL;
        sleep(5);
    }
}