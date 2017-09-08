<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Time: 17:47
 */

$redis = new Redis();
$redis->connect('127.0.0.1');

while(true){
//    $redis->incrBy('newlock',1);
    $redis->watch('newlock');
}