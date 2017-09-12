> 基于TCP建立协议，
#### redis命令发送格式：
*<参数数量> CRLF  
$<参数 1 的字节数量> CRLF  
<参数 1 的数据> CRLF  
...  
$<参数 N 的字节数量> CRLF  
<参数 N 的数据> CRLF  
其中CRLF表示 \r\n  

##### 举个例子：set name wuzhc
##### 格式化输出：
*3  
$3  
set  
$4  
name  
$5  
wuzhc  
##### 说明：
- \*开头，表示有多少个参数，例如*3表示有3个参数（set, name, wuzhc）
- $开头，表示参数的字节长度，例如$3表示set有3个字节，$4表示name有4个字节
- 每行\r\n结尾
##### 这个通信协议为 *3\r\n$3\r\nset\r\n$4\r\nname\r\n$5\r\nwuzhc\r\n

#### Redis 回复
- 状态回复（status reply）的第一个字节是 "+"，例如+OK\r\n
- 错误回复（error reply）的第一个字节是 "-"，例如-No such key\r\n
- 整数回复（integer reply）的第一个字节是 ":"，例如:1\r\n
- 批量回复（bulk reply）的第一个字节是 "$"，例如 $3\r\nabc\r\n
- 多条批量回复（multi bulk reply）的第一个字节是 "*"，例如\*2\r\n$3\r\nabc\r\n$3\r\nxyz\r\n

#### PHP 实现Redis客户端
```php
<?php
/**
 * Created by PhpStorm.
 * User: wuzhc2016@163.com
 * Date: 2017年09月12日
 * Time: 9:08
 */

// 与redis建立基于TCP的socket连接
$socket = stream_socket_client('tcp://127.0.0.1:6379', $errno, $errstr, 1, STREAM_CLIENT_CONNECT);
if (!$socket) {
    var_dump($errno, $errstr);
    exit;
}

// 发送 mget name age 命令到redis
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
    $line = fgets($socket);
    $type = $line[0];
    $msg = mb_substr($line, 1, -2, '8bit');

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

```
其实可以写一个类封装，再提供一个解析命令的接口，就不需要写*3{$crlf}$4{$crlf}mget{$crlf}$4{$crlf}name{$crlf}$3{$crlf}age{$crlf}的命令  
参考：yii2-redis扩展，https://github.com/yiisoft/yii2-redis/blob/master/Connection.php