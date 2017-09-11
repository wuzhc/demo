<?php
/**
 * Created by PhpStorm.
 * User: wuzhc2016@163.com
 * Date: 2017��09��08��
 * Time: 16:28
 */

$redis = new Redis();
$redis->connect('127.0.0.1');

/**
 * 尝试3秒内获取锁
 * @param string $lockName
 * @param int $timeout
 * @return bool|string
 */
function acquireLock($lockName, $timeout = 3)
{
    global $redis;
    $identifier = uniqid();
    $end = time() + $timeout;
    while ($end >= time()) {
        if ($redis->set($lockName, $identifier, array('nx'))) {
            return $identifier;
        }
        usleep(1000);
    }
    return false;
}

/**
 * 释放锁
 * @param $lockName
 * @param $identifier
 * @return bool
 */
function releaseLock($lockName, $identifier)
{
    global $redis;
    while (true) {
        $redis->watch($lockName);
        if ($redis->get($lockName) == $identifier) {
            $redis->multi(Redis::MULTI);
            $redis->del($lockName);
            $res = $redis->exec();
            if (isset($res[0]) && $res[0] == 1) {
                return true;
            }
        } else {
            $redis->unwatch();
            return false;
        }
    }
}
