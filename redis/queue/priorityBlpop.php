<?php
/**
 * 优先级队列
 * Created by PhpStorm.
 * User: wuzhc2016@163.com
 * Date: 2017-09-08
 * Time: 11:33
 */

$redis = new Redis();
$redis->connect('127.0.0.1');

// 优先级队列
$high = 'goods:high:task';
$mid = 'goods:mid:task';
$low = 'goods:low:task';

// 出队
while(true){
    // 优先级高的队列放在左侧
    $task = $redis->blPop(array($high, $mid, $low), 3);
    if ($task) {
        $task = json_decode($task[1], true);
        echo $task['id'] . ':' . $task['cid'] . ':' . 'handle success';
        echo PHP_EOL;
    } else {
        echo 'nothing' . PHP_EOL;
        sleep(5);
    }
}