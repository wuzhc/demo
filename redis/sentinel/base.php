<?php
/**
 * Created by PhpStorm.
 * User: wuzhc2016@163.com
 * Date: 2017年09月12日
 * Time: 9:08
 */

class PRedis
{
    private $_socket = null;

    private $_error = null;
    
    public function __construct($ip, $port) 
    {
        $this->_socket = stream_socket_client(
            "tcp://{$ip}:{$port}",
            $errno,
            $errstr,
            1,
            STREAM_CLIENT_CONNECT
        );
        if (!$this->_socket) {
            $this->_error = $errstr;
        }
    }

    /**
     * 执行redis命令
     * @param $command
     * @return array|bool|string
     */
    public function exec($command)
    {
        if ($this->_error) {
            return false;
        }

        // 拼装发送命令格式
        $command = $this->_execCommand($command);

        // 发送命令到redis
        fwrite($this->_socket, $command);

        // 解析redis响应内容
        return $this->_parseResponse();
    }

    /**
     * 将字符改为redis通讯协议格式
     * 例如mget name age 格式化为 *3\r\n$4\r\nmget\r\n$4\r\nname\r\n$3\r\nage\r\n
     * @param $command
     * @return bool|string
     */
    private function _execCommand($command)
    {
        $line = '';
        $crlf = "\r\n";
        $params = explode(' ', $command);
        if (empty($params)) {
            return $line;
        }

        // 参数个数
        $line .= '*' . count($params) . $crlf;

        // 各个参数拼装
        foreach ((array)$params as $param) {
            $line .= '$' . mb_strlen($param, '8bit') . $crlf;
            $line .= $param . $crlf;
        }

        return $line;
    }

    /**
     * 解析redis回复
     * @return array|bool|string
     */
    private function _parseResponse()
    {
        $line = fgets($this->_socket); // 假设 $line = *3\r\n
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
                $line = fread($this->_socket, (int)$msg + 2); // 数据字节数 + (\r\n)两个字节
                return mb_substr($line, 0, -2, '8bit'); // 去除最后两个字节
            // 多条批量回复
            case '*': // *表示后面有多少个参数
                $data = [];
                for ($i = 0; $i < $msg; $i++) {
                    $data[] = $this->_parseResponse();
                }
                return $data;
        }
    }

    /**
     * 错误信息
     * @return null
     */
    public function getError()
    {
        return $this->_error;
    }
}
