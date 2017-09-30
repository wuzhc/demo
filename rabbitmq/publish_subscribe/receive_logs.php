<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月29日
 * Time: 17:20
 */

require_once '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->exchange_declare('logs', 'fanout', false, false, false);

// 随机生成一个队列名
list($queue_name, ,) = $channel->queue_declare('');
// 队列绑定到交换器，不需要路由键（因为fanout类型的交换器会忽视路由键）
$channel->queue_bind($queue_name, 'logs');

echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";
$callback = function ($msg) {
    echo ' [x] ', $msg->body, "\n";
};
$channel->basic_consume($queue_name, '', false, true, false, false, $callback);
while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();