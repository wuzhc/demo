<?php

use Workerman\Worker;

require_once '../workerman/Autoloader.php';
$global_uid = 0;

$text_worker = new Worker('text://0.0.0.0:2347');
$text_worker->count = 1;

// 客户端链接
$text_worker->onConnect = function ($connection) {
    global $global_uid;
    $connection->uid = ++$global_uid;
};

// 客户端发送信息
$text_worker->onMessage = function ($connection, $data) {
    global $text_worker;
    foreach ($text_worker->connections as $conn) {
        $conn->send("user[{$connection->uid}] said: $data");
    }
};

// 客户端离开
$text_worker->onClose = function ($connection) {
    global $text_worker;
    foreach ($text_worker->connections as $conn) {
        $conn->send("user[{$connection->uid}] has leave");
    }
};

Worker::runAll();

