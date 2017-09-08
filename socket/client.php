<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/19
 * Time: 15:33
 *
 * socket client code
 * socket->connect->send->receive->close
 */

set_time_limit(0);
$host = '127.0.0.1';
$port = 12387;
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)
    or die('socket_create() failed:' . socket_strerror(socket_last_error()));

printf('try to connect to %s:%s', $host, $port);
echo PHP_EOL;

socket_connect($socket, $host, $port)
    or die('socket_connect() failed:' . socket_strerror(socket_last_error()));

$in = "Hello";
if (!socket_write($socket, $in, strlen($in))) {
    echo 'socket_write() failed:' . socket_strerror(socket_last_error());
} else {
    echo '数据'. $in .'发送成功' . PHP_EOL;
}

$out = '';
while ($buf = socket_read($socket, 8192)) {
    $out .= $buf;
}
echo '服务器端返回数据为：' . $out . PHP_EOL;
socket_close($socket);