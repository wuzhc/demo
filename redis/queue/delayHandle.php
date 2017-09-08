<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月08日
 * Time: 11:46
 */
define('DIR_ROOT', dirname(__DIR__));
include DIR_ROOT . '/lock/lockFunc.php';

$redis = new Redis();
$redis->connect('127.0.0.1');

while (true) {
    // 因为是有序集合，只要判断第一条记录的延时时间，例如第一条未到执行时间
    // 相对说明集合的其他任务未到执行时间
    $rs = $redis->zRange('goods:delay:task', 0, 0, true);
    // 集合没有任务，睡眠时间设置为5秒
    if (empty($rs)) {
        echo 'no tasks , sleep 5 seconds' . PHP_EOL;
        sleep(5);
        continue;
    }

    $taskJson = key($rs);
    $delay = $rs[$taskJson];
    $task = json_decode($taskJson, true);
    $now = time();

    // 到时间执行延时任务
    if ($delay <= $now) {
        // 对当前任务加锁，避免移动移动延时任务到任务队列时被其他客户端修改
        if (!($identifier = acquireLock($task['id']))) {
            continue;
        }

        // 移动延时任务到任务队列
        $redis->zRem('goods:delay:task', $taskJson);
        $redis->rPush('goods:task', $taskJson);
        echo $task['id'] . ' run ' . PHP_EOL;

        // 释放锁
        releaseLock($task['id'], $identifier);
    } else {
        // 延时任务未到执行时间
        $sleep = $delay - $now;
        // 最大值设置为2秒，保证如果有新的任务（延时时间1秒）进入集合时能够及时的被处理
//        $sleep = $sleep > 2 ? 2 :$sleep;
        echo 'wait ' . $sleep . ' seconds ' . PHP_EOL;
        sleep($sleep);
    }
}
