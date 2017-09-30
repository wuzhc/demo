<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月29日
 * Time: 13:51
 */

require_once '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// 新建链接
$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
// 新建通道
$channel = $connection->channel();
// 声明一个队列
$channel->queue_declare('hello', false, false, false, false);
// $msg
$msg = new AMQPMessage('hello word');
//
$channel->basic_publish($msg, '', 'hello');
echo " [x] Sent 'Hello World!'\n";
$channel->close();
$connection->close();
