<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月29日
 * Time: 14:50
 */

require_once '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare('task_hello', false, true, false, false);

$data = implode(' ', array_slice($argv, 1));
$data = $data ?: 'hello word';
$msg = new AMQPMessage($data, array(
    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
));

$channel->basic_publish($msg, '', 'hello');
echo "[x] Send " . $data . "\n";