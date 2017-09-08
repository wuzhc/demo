<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月21日
 * Time: 11:30
 */


class RedisObj
{

    /** @var object Redis */
    public $redis;

    public function __construct($host='127.0.0.1', $port=6379, $pwd='')
    {
        $this->redis = new Redis();
        $this->redis->connect($host, $port) or die ('connect redis failed');
        if ($pwd) {
            $this->redis->auth($pwd);
        }
    }

    /**
     * @param $key
     * @param $val
     * @return bool
     */
    public function setStr($key, $val)
    {
        return $this->redis->set($key, $val);
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function getStr($key)
    {
        return $this->redis->get($key);
    }

    /**
     * @param $key
     * @return int
     */
    public function delStr($key)
    {
        return $this->redis->delete($key);
    }

    /**
     * 设置列表
     * @param $key
     * @param $val
     * @return int
     */
    public function setList($key, $val)
    {
        $n = 0;
        $val = (array)$val;
        if (is_array($val)) {
            foreach ($val as $v) {
                $n += $this->redis->lPush($key, $v);
            }
        }
        return $n;
    }

    /**
     * 获取列表
     * @param $key
     * @param int $start
     * @param int $end
     * @return array
     */
    public function getList($key, $start = 0, $end = 20)
    {
        return $this->redis->lRange($key, $start, $end);
    }

    /**
     * 列表长度
     * @param $key
     * @return int
     */
    public function listLen($key)
    {
        return $this->redis->lLen($key);
    }

    /**
     * @param string $pattern
     * @return array
     */
    public function getKeys($pattern = '*')
    {
        return $this->redis->keys($pattern);
    }

    /**
     * 获取指定健的值，如果键不存在，对应返回false，只对字符串类型有用
     * @param $keys
     * @return array
     */
    public function getMultiple($keys)
    {
        return $this->redis->getMultiple($keys);
    }

    //region 消息订阅

    /**
     * @param $channel
     * @param $message
     */
    public function publish($channel,$message)
    {
       $this->redis->publish($channel,$message);
    }

    /**
     * @param $channel
     * @param $callback
     */
    public function subscribe($channel, $callback)
    {
        $this->redis->subscribe($channel,$callback);
    }

    //endregion 消息订阅

}

