<?php
/**
 * Created by PhpStorm.
 * User: wuzhc2016@163.com
 * Date: 2017年09月12日
 * Time: 9:08
 */

// 与redis建立socket连接
$socket = stream_socket_client('tcp://127.0.0.1:6379', $errno, $errstr, 1, STREAM_CLIENT_CONNECT);
if (!$socket) {
    var_dump($errno, $errstr);
    exit;
}

// 发送命令到redis
$crlf = "\r\n";
fwrite($socket, "*3{$crlf}$4{$crlf}mget{$crlf}$4{$crlf}name{$crlf}$3{$crlf}age{$crlf}");

// 客户端收到redis回复
$res = parseResponse();
var_dump($res);

// 关闭连接
fclose($socket);

/**
 * 解析redis回复
 * @return array|bool|string
 */
function parseResponse()
{
    global $socket;
    $line = fgets($socket); // 假设 $line = *3\r\n
    $type = $line[0]; // $type = *
    $msg = mb_substr($line, 1, -2, '8bit'); // $msg = 3

    switch ($type) {
        // 状态回复
        case '+':
            if ($msg == 'OK' || $msg == 'PONG') {
                return true;
            } else {
                return $msg;
            }
        // 错误回复
        case '-':
            exit($msg);
        // 整数回复
        case ':':
            return $msg;
        // 批量回复
        case '$': // $后面跟数据字节数(长度)
            $line = fread($socket, (int)$msg + 2); // 数据字节数 + (\r\n)两个字节
            return mb_substr($line, 0, -2, '8bit'); // 去除最后两个字节
        // 多条批量回复
        case '*': // *表示后面有多少个参数
            $data = [];
            for ($i = 0; $i < $msg; $i++) {
                $data[] = parseResponse();
            }
            return $data;
    }
}