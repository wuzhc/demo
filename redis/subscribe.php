<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月21日
 * Time: 14:12
 */

// 消息发布
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$message='新年快乐';
$ret=$redis->publish('中央广播电台',$message);
