<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月21日
 * Time: 11:07
 */

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->set('s','s');

$pipe = $redis->multi(Redis::PIPELINE);
for ($i = 0; $i < 10; $i++) {
    $pipe->set("ss$i", str_pad($i, 4, '0', 0));
    $pipe->get("ss$i");
}
$replies = $pipe->exec();
echo "";
print_r($replies);