<?php
/**
 * 多个worker启动时，rabbitmq以循环方式分配任务给每个worker
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月29日
 * Time: 14:59
 */

require '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 声明一个队列，注意和发布的队列一致
// consumer声明队列，确保消费之前队列已经存在,durable设置为true时，保证消息持久化
$channel->queue_declare('task_hello', false, true, false, false);

echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

// 消息是从服务器异步发送到客户端
$callback = function ($msg) {
    echo " [x] Received ", $msg->body, "\n";
    // 模拟一个任务执行时间（ . 出现的次数为执行秒数）
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done \n";
    // 成功执行后，发送ack，队列将安全删除消息，否则消息不会被删除，并且会被重复消费，累积很多时会耗尽内存
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

// 公平分配消息，设置basic_pos方法prefetch_count = 1
$channel->basic_qos(null, 1, null);
$channel->basic_consume('hello', '', false, false, false, false, $callback);

// 当有回调时将阻塞，接受到消息时只需回调
while (count($channel->callbacks)) {
    $channel->wait();
}