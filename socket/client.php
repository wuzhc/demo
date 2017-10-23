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
//$host = '127.0.0.1';
//$port = 12387;
$host = '67.218.152.248';
$port = 30000;
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)
    or die('socket_create() failed:' . socket_strerror(socket_last_error()));

printf('try to connect to %s:%s', $host, $port);
echo PHP_EOL;

socket_connect($socket, $host, $port)
    or die('socket_connect() failed:' . socket_strerror(socket_last_error()));

$in = "Hello\r\n";
if (!socket_write($socket, $in, strlen($in))) {
    echo 'socket_write() failed:' . socket_strerror(socket_last_error());
} else {
    echo "send server with $in \r\n";
}

$out = '';
while ($buf = socket_read($socket, 1024)) {
    $out .= $buf;
}
echo 'from server : ' . $out . PHP_EOL;
socket_close($socket);