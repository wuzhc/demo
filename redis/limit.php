<?php
/**
 * 手机验证码
 * 限制一分钟内只能发送5次
 */
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$key = 'p:limit:14718070574';
$isExist = $redis->set($key, 1, array('nx', 'ex' => 60));
if ($isExist === true || $redis->incr($key) <= 5) {
    printf('第%d次发送手机验证码', $redis->get($key));
} else {
    echo '一分钟内只能发送5次';
}