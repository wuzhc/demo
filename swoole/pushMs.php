<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年11月06日
 * Time: 17:52
 */

// redis实例
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$ws = new swoole_websocket_server('0.0.0.0', 9505);
$ws->set([
    'daemonize' => false, // 守护进程运行
    'max_request' => 10000, // 最大连接数
    'debug_mode' => 1,
    'heartbeat_check_interval' => 5,
    'heartbeat_idle_time' => 600,
]);

// swoole 监听一个TCP端口来处理第三方客户端的消息请求
$tcpServer = $ws->addlistener('0.0.0.0', 9506, SWOOLE_SOCK_TCP);
// 保证worker进程onReceive每次都会收到一个完整的数据包
$tcpServer->set([
    'open_length_check' => true,
    'package_length_type' => 'N',
    'package_length_offset' => 0,
    'package_max_length' => 2048,
    'open_eof_check' => true,
    'package_eof' => "\r\n"
]);

// 当WebSocket客户端与服务器建立连接并完成握手后会回调此函数
$ws->on('open', function (swoole_websocket_server $server, swoole_http_request $request) {
    echo "server: handshake success with fd{$request->fd}\n";
});

// 服务器收到来自客户端的数据帧时会回调此函数
$ws->on('message', function (swoole_websocket_server $server, swoole_websocket_frame $frame) use ($redis) {
    // 把TCP客户端连接标识符保存在redis用于广播
    $redis->hSet('subscriber_list', 'user_' . $frame->fd, json_encode(['fd' => $frame->fd]));
    $server->push($frame->fd, json_encode(['msg' => 'welcome you']));
});

$tcpServer->on('connect', function ($serv, $fd) {
    echo "Client:Connect\r\n";
});

/**
 * @param swoole_server $serv
 * @param int $fd TCP客户端连接的唯一标识符
 * @param int $reactor_id TCP连接所在的Reactor线程ID
 * @param string $data 收到的数据内容，可能是文本或者二进制内容
 */
$tcpServer->on('receive', function (swoole_websocket_server $serv, $fd, $reactor_id, $data) use ($redis) {
    $data = json_decode($data, true);
    if (!DataUtil::issetVal($data['msg'])) {
        $serv->send($fd, json_encode(['status' => 1, 'msg' => 'push into redis fail']));
        return;
    }

    // 将第三方推送数据保存在redis中
    $msgStr = $data['msg'];
    $res = $redis->rPush('message_center', $msgStr);

    // 如果入库成功，则进行广播
    if (!$res) {
        $serv->send($fd, json_encode(['status' => 1, 'msg' => 'push into redis fail']));
        return;
    }
    $serv->close($fd);

    // 广播
    $subscribers = $redis->hGetAll('subscriber_list');
    print_r($subscribers);
    if (is_array($subscribers)) {
        foreach ($subscribers as $k => $v) {
            $vObj = json_decode($v, true);
            // 向websocket客户端连接推送数据，长度最大不得超过2M。
            $serv->push($vObj['fd'], json_encode(['msg' => $msgStr]));
        }
    }
});

$tcpServer->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

// 关闭客户端时候，删除redis内容
$ws->on('close', function ($ser, $fd) use ($redis) {
    $redis->hDel('subscriber_list', 'user_' . $fd);
    echo "client {$fd} closed\n";
});

$ws->start();