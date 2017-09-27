<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月25日
 * Time: 14:50
 */

$conn_args = array(
    'host'=>'127.0.0.1',  //rabbitmq 服务器host
    'port'=>5672,         //rabbitmq 服务器端口
    'login'=>'guest',     //登录用户
    'password'=>'guest',   //登录密码
    'vhost'=>'/'         //虚拟主机
);
$e_name = 'e_demo';
$q_name = 'q_demo';
$k_route = 'key_1';

$conn = new AMQPConnection($conn_args);
if(!$conn->connect()){
    die('Cannot connect to the broker');
}
$channel = new AMQPChannel($conn);

$ex = new AMQPExchange($channel);
$ex->setName($e_name);
$ex->setType(AMQP_EX_TYPE_DIRECT);
$ex->setFlags(AMQP_DURABLE);
$status = $ex->declareExchange();  //声明一个新交换机，如果这个交换机已经存在了，就不需要再调用declareExchange()方法了.
$q = new AMQPQueue($channel);
$q->setName($q_name);
$status = $q->declareQueue(); //同理如果该队列已经存在不用再调用这个方法了。
$q->bind($e_name, $k_route);
$ex->publish('send to wuzhc', $k_route);