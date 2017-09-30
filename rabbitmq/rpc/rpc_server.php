<?php
/**
 * RPC优势，
 * （1） 如果rpc_server忙的时候，可以多开几个server
 *
 * 如果rpc_server没启动，怎么办？？
 * 客户端需要设置超时时间？
 * 如果rpc_server异常，是否返回给客户端？？
 *
 *
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月30日
 * Time: 11:50
 */

require_once '../vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'guest', 'guest');
$channel = $connection->channel();

// 声明队列
$channel->queue_declare('rpc_queue', false, false, false, false);

function fib($n) {
    if ($n == 0)
        return 0;
    if ($n == 1)
        return 1;
    return fib($n-1) + fib($n-2);
}

// 回调
$callback = function ($req) {
    $data = json_decode($req->body, true);
    $num = $data['num'];
    $timeout = isset($data['timeout']) ? $data['timeout'] : 0;
    $now = time();

    // 处理消息
    echo " [x] fib($num)" . PHP_EOL;

    $result = (string)fib($num);

    // $msg
    $msg = new AMQPMessage($result, array(
        'correlation_id' => $req->get('correlation_id')
    ));

    $req->delivery_info['channel']->basic_publish($msg, '', $req->get('reply_to'));
    $req->delivery_info['channel']->basic_ack($req->delivery_info['delivery_tag']);
};

echo " [x] Awaiting RPC requests\n";
$channel->basic_qos(null, 1, null);
$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();