<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月29日
 * Time: 16:33
 */

require_once '../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 声明一个交换器logs，类型为fanout
$channel->exchange_declare('logs', 'fanout', false, false, false);

$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
    $data = 'hello world';
}

$msg = new AMQPMessage($data);
$channel->basic_publish($msg, 'logs');

echo " [x] Send " . $data . "\n";
$channel->close();
$connection->close();
