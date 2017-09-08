<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月24日
 * Time: 11:06
 *
 * redis应用场景样式案例
 */


$redis = new Redis();
$redis->connect('127.0.0.1');


//10秒内评论次数不得超过2次
$res = $redis->zRangeByScore('user:12:comment',time()-10 ,time());
$count = count($redis->zRangeByScore('user:12:comment',time()-10 ,time()));
if ($count == 2) {
    echo '10秒之内不能评论2次'; exit;
} else {
    $redis->zAdd('user:12:comment', time(), $count);
}


//记录用户喜欢商品排序 其实可以用mongo
$redis->zAdd('user:1000:product:like', time(), '3002');
$redis->zAdd('user:1000:product:like', time(), '3001');
$redis->zAdd('user:1000:product:like', time(), '3004');
$redis->zAdd('user:1000:product:like', time(), '3003');
//$redis->zDelete('user:1000:product:like',3003);
//默认喜欢时间升序序排列
$products = $redis->zRange('user:1000:product:like', 0, -1,true);
var_dump($products);
//以喜欢时间降序排列
$products = $redis->zRevRange('user:1000:product:like', 0, -1,true);
var_dump($products);

//或者用列表实现
$redis->lPush('user:1000:product:like', '3002');
$redis->lPush('user:1000:product:like', '3001');
$redis->lPush('user:1000:product:like', '3004');
$redis->lPush('user:1000:product:like', '3003');
$redis->lRange('user:1000:product:like', 0, -1);

//自增数加一
$redis->hSet('user:1000:message:notice', 'system', 1);
$redis->hIncrBy('user:1000:message:notice', 'system', 1);
$redis->hSet('user:1000:message:notice', 'comment', 1);
$redis->hIncrBy('user:1000:message:notice', 'comment', 1);
$redis->hGetAll('user:1000:message:notice');
