<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月30日
 * Time: 9:48
 */

require_once '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 声明交换器
$channel->exchange_declare('direct_logs', 'direct', false, false, false);

// 声明队列,队列名由rabbitmq生成
list($queue_name, ,) = $channel->queue_declare('', false, false, false, false);

$severities = array_slice($argv, 1);
if (empty($severities)) {
    file_put_contents('php://stderr', "Usage: $argv[0] [info] [warning] [error]\n");
    exit(1);
}

// 绑定交换器和队列
foreach ($severities as $severity) {
    $channel->queue_bind($queue_name, 'direct_logs', $severity);
}

// 回调函数
$callback = function ($msg) {
    echo " [x] " . $msg->delivery_info['routing_key'] . ' : ' . $msg->body . "\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

// 开始消费
echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";
$channel->basic_consume($queue_name, '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();