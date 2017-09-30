<?php
/**
 * 日志的三个等级（info, warning, error）
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月30日
 * Time: 9:39
 */

require_once '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 声明direct类型的交换器
$channel->exchange_declare('direct_logs', 'direct', false, false, false);

// 日志等级，默认为info
$severity = isset($argv[1]) && !empty($argv[1]) ? $argv[1] : 'info';
$data = implode(' ', array_slice($argv, 2));
$msg = new AMQPMessage($data ?: 'hello rabbitmq');

// 路由键为日志等级
$channel->basic_publish($msg, 'direct_logs', $severity);

echo " [x] Send " . $data . "\n";
$channel->close();
$connection->close();