<?php
$serv = new swoole_websocket_server("0.0.0.0", 9502);
$serv->set(
    array(
        'daemonize' => true,      // 是否是守护进程
        'max_request' => 10000,    // 最大连接数量
        'dispatch_mode' => 2,
        'debug_mode'=> 1,
        // 心跳检测的设置，自动踢掉掉线的fd
        'heartbeat_check_interval' => 5,
        'heartbeat_idle_time' => 600,
    )
);

$redis = new Redis();
// redis 没有设置密码
$redis->connect('127.0.0.1', 6379);

// swoole 监听一个TCP端口来处理第三方客户端的消息请求
$tcp_server = $serv->addlistener('0.0.0.0', 9503, SWOOLE_SOCK_TCP);
$tcp_server->set(array(
    'open_length_check' => true,
    'package_length_type' => 'N',
    'package_length_offset' => 0,
    'package_max_length' => 8192,
    'open_eof_check'=> true,
    'package_eof' => "\r\n"
));

// accept client connect
$serv->on('Open', function($server, $req) use($redis){
    $server->push($req->fd, responseJson(1,"success", ['method' => 'connection','status' => 1, 'error_code' => 0]));      
});

// recv data
$serv->on('Message', function($server, $frame) use($redis){
    $rev_data = json_decode($frame->data,true);
    $method = isset($rev_data['method']) ? $rev_data['method'] : '';
    $uid = isset($rev_data['uid']) ? intval($rev_data['uid']) : '';
    $key = 'user_'.$frame->fd;
    switch($method) {
        case 'join':
            $async_login_data = [
                'uid' => $uid,
                'hobby' => $rev_data['hobby'],// eat drink play more and more
                'fd' => $frame->fd
            ];
            $redis->hset("client_list", $key, json_encode($async_login_data));
            $server->push($frame->fd,responseJson(1,"success", ['method' => 'join','status' => 1]));
            break;
        default:
            break;
    }
});

/**
 * 接受 client 端信息推送,涉及问题
 * 1. 服务端认证、授权(安全)
 * 2. 推送消息落地(redis队列->MySQL)
 * 3. 推送客户端和消息中心交互, TCP
 * 4. 按照用户类型(channel)推送
 */
$tcp_server->on('connect', function($serv, $fd) use($redis){
    echo "Client:Connect\r\n";
});

/**
 * 接受 client 端信息推送,涉及问题
 * 1. 服务端认证、授权(安全)
 * 2. 推送消息落地(redis队列->MySQL)
 * 3. 推送客户端和消息中心交互, TCP
 * 4. 按照用户类型(channel)推送
 */
$tcp_server->on('receive', function($serv, $fd, $from_id, $data) use($redis) {
    $data = json_decode($data, true);
    if(empty($data['method']) && !isset($data['method'])) return;
    if(empty($data['data']) && !isset($data['data'])) return;
    $s = json_encode($data['data']);
    
    // 推送 存入redis、最后入库(MySQL)
    $async_data = $redis->rPush("message_center", $s);
    if($async_data) {
        $serv->send($fd, responseJson(1,"success", ['method' => 'receive', 'error_code' => 0, 'status' => 1]));
        $serv->close($fd);
        $user_data = $redis->hGetAll("client_list");
        if($async_data && $user_data) {
            // 广播
            foreach($user_data as $v) {
                $tmp_data = json_decode($v, true);
                $serv->push($tmp_data['fd'], responseJson(1,"success", ['method' => 'push','data' => $data['data']['message'],'status' => 1]));
            }
        }
    } else {
        $serv->send($fd, responseJson(1,"fail", ['method' => 'receive', 'error_code' => 110,'status' => 0]));
    }
    
});

$tcp_server->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

$serv->on('Close', function($server, $fd) use($redis){
    $key = 'user_'.$fd;
    $redis->hDel("client_list",$key);
    echo "connection close: ".$fd;
});

function responseJson($status = 1, $message = '', $data = array()) {
    $data = [
        'status' => $status,
        'message' => $message,
        'data' => $data,
    ];
    return json_encode($data);
}
$serv->start();
