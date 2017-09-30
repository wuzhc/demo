<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月29日
 * Time: 14:11
 */

require '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 声明一个队列，注意和发布的队列一致
// consumer声明队列，确保消费之前队列已经存在
$channel->queue_declare('hello', false, false, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

// 消息是从服务器异步发送到客户端
$callback = function ($msg) {
    echo " [x] Received ", $msg->body, "\n";
};
$channel->basic_consume('hello', '', false, true, false, false, $callback);

// 当有回调时将阻塞，接受到消息时只需回调
while (count($channel->callbacks)) {
    $channel->wait();
}