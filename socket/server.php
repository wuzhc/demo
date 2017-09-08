<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/19
 * Time: 15:33
 *
 * socket·þÎñ¶Ë´úÂë
 * socket->bind->listen->accept->send->close
 */


set_time_limit(0); /* ensure client connect do not timeout */
$host = '127.0.0.1';
$port = 12387;

// create a tcp stream
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)
    or die('socket create() failed:' . socket_strerror(socket_last_error()));

// set block
socket_set_block($socket)
    or die('socket_set_block() failed:' . socket_strerror(socket_last_error()));

// bind port
socket_bind($socket, $host, $port)
    or die('socket_bind() failed:' . socket_strerror(socket_last_error()));

// listen
socket_listen($socket, 1)
    or die('socket_listen() failed: ' . socket_strerror(socket_last_error()));

echo 'Binding the socket on ' . $host . ':' . $port . '...' . PHP_EOL;

// socket accept
do {
    if (($msgSock = socket_accept($socket)) < 0) {
        echo 'socket_accept() failed:' . socket_strerror(socket_last_error());
    } else {
        $receiveContent = strrev(socket_read($msgSock, 8912)); /* reverse client data */
        socket_write($msgSock, $receiveContent, strlen($receiveContent));
    }
    socket_close($msgSock);
} while (true);

// close
socket_close($socket);