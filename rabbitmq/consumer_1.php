<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月25日
 * Time: 14:49
 */

$conn_args = array(
    'host'=>'127.0.0.1',
    'port'=>5672,
    'login'=>'guest',
    'password'=>'guest',
    'vhost'=>'/'
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

$q = new AMQPQueue($channel);
var_dump($q);
$q->setName($q_name);
// $q->bind($e_name, $k_route);

$arr = $q->get();
var_dump($arr);
$res = $q->ack($arr->getDeliveryTag());
$msg = $arr->getBody();
var_dump($msg);