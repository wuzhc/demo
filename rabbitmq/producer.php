<?php
/**
 * RabbitMQ 生产者
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月25日
 * Time: 8:57
 */

//Establish connection to AMQP
$connection = new AMQPConnection();
$connection->setHost('127.0.0.1');
$connection->setLogin('guest');
$connection->setPassword('guest');
$connection->connect();

//Create and declare channel
$channel = new AMQPChannel($connection);

//AMQPC Exchange is the publishing mechanism
$exchange = new AMQPExchange($channel);
$exchange->setName('hello-exchange-1');
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$exchange->setFlags(AMQP_NOPARAM);
$exchange->setArgument('passive', false);
$exchange->declareExchange();
try {
    $routing_key = 'hello-1';
//    $queue = new AMQPQueue($channel);
//    $queue->setName($routing_key);
//    $queue->setFlags(AMQP_NOPARAM);
//    $queue->declareQueue();
    $message = 'wuzhc-howdy-do';
    $exchange->publish($message, $routing_key);
    $connection->disconnect();
} catch (Exception $ex) {
    print_r($ex);
}