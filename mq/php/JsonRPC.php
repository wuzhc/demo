<?php

class JsonRPC
{
    /**
     * @var resource
     */
    protected $conn = false;
    protected $address;
    protected $timeout;
    protected $errno;
    protected $errstr;

    public function __construct($address, $timeout = 30)
    {
        if (!$address) {
            throw new Exception('Address is empty');
        }

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            echo sprintf("errno:%s,errstr:%s,errfile:%s,errline:%s\n", $errno, $errstr, $errfile, $errline);
            exit(0);
        });

        $this->address = $address;
        $this->timeout = $timeout;
    }

    /**
     * 生产job
     * func push(data map[string]interface{}) (string, Errors)
     *
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function push($data)
    {
        $this->openConn();
        $data = $this->wrapPushData($data);
        return $this->call($data);
    }

    /**
     * 消费job
     * func pop(topics []string) (map[string]interface{}, Errors)
     *
     * @param $topics
     * @return mixed
     * @throws Exception
     */
    public function pop($topics)
    {
        $this->openConn();
        $data = $this->wrapPopData($topics);
        return $this->call($data);
    }

    /**
     * 确认删除job
     * func ack(jobId string) (string, Errors)
     *
     * @param $jobId
     * @return array
     */
    public function ack($jobId)
    {
        $this->openConn();
        $data = $this->wrapAckData($jobId);
        return $this->call($data);
    }

    /**
     * 获取连接失败
     *
     * @return string
     */
    public function getError()
    {
        return sprintf('errno:%s,errstr:%s\n', $this->errno, $this->errstr);
    }

    /**
     * @param $data
     * @return array
     * @throws Exception
     */
    protected function call($data)
    {
        if (fwrite($this->conn, $data) != strlen($data)) {
            throw new Exception('send failed');
        }

        $res = fgets($this->conn);
        if (is_bool($res) && $res === false) {
            throw new Exception('recv failed');
        }

        $res = json_decode($res, true);
        if (!empty($res['error'])) {
            return [$res['result'], Errors::newErr($res['error'])];
        } else {
            return [$res['result'], null];
        }
    }

    protected function openConn()
    {
        if ($this->conn !== false) {
            return;
        }
        $this->conn = stream_socket_client($this->address, $this->errno, $this->errstr, $this->timeout,
            STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT);
        stream_set_timeout($this->conn, 30);
        if (!$this->conn) {
            throw new Exception("can not connect to $this->address , $this->errno:$this->errstr");
        }
    }

    protected function wrapPushData($data)
    {
        if (empty($data['id'])) {
            throw new Exception('job.id is empty');
        }
        if (empty($data['topic'])) {
            throw new Exception('job.topic is empty');
        }
        if (empty($data['body'])) {
            throw new Exception('job.body is empty');
        }
        if (!isset($data['delay'])) {
            throw new Exception('job.delay is not set');
        }
        if (!isset($data['TTR'])) {
            throw new Exception('job.TTR is not set');
        }

        return json_encode([
                'method' => 'Service.Push',
                'params' => [$data],
                'id'     => uniqid('gmq'),
            ]) . "\n";
    }

    protected function wrapPopData($data)
    {
        return json_encode([
                'method' => 'Service.Pop',
                'params' => [$data],
                'id'     => uniqid('gmq'),
            ]) . "\n";
    }

    protected function wrapAckData($data)
    {
        return json_encode([
                'method' => 'Service.Ack',
                'params' => [$data],
                'id'     => uniqid('gmq'),
            ]) . "\n";
    }
}

class Errors
{
    public $msg;
    public $code;

    public function __construct($msg = '', $code = 1)
    {
        $this->msg = $msg;
        $this->code = $code;
    }

    public static function newErr($msg = '', $code = 1)
    {
        return new self($msg, $code);
    }

    public static function newParamErr($msg = '')
    {
        return new self($msg, 21);
    }

    public function __toString()
    {
        if ($this->msg) {
            return 'Error:' . $this->msg . ', Code: ' . $this->code;
        } else {
            return '';
        }
    }
}