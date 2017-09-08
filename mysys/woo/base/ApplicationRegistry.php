<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月15日
 * Time: 11:26
 */

namespace woo\base;


class ApplicationRegistry extends Registry
{
    /** @var ApplicationRegistry */
    private static $instance;
    public static $freezedir = 'data';
    private $values = array();
    private $times = array();

    private function __construct() { }

    /**
     * @return ApplicationRegistry
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $key
     * @return null
     */
    protected function get($key)
    {
        $path = self::$freezedir . DIRECTORY_SEPARATOR . $key;
        if (!file_exists($path)) {
            return null;
        }
        if (!isset($this->times[$key])) {
            $this->times[$key] = 0;
        }

        // filemtime有缓存，需要清除缓存
        clearstatcache();
        $time = filemtime($path);

        // 如果文件有更新,重新从文件加载新数据
        if ($time > $this->times[$key]) {
            $data = unserialize(file_get_contents($path));
            $this->values[$key] = $data;
            $this->times[$key] = $time;
            return $data;
        }
        return isset($this->values[$key]) ? $this->values[$key] : null;
    }

    /**
     * @param $key
     * @param $value
     * @throws \ErrorException
     */
    protected function set($key, $value)
    {
        $path = self::$freezedir . DIRECTORY_SEPARATOR . $key;
        file_put_contents($path, serialize($value), LOCK_EX);

        $this->values[$key] = $value;
        $this->times[$key] = time();
    }

    /**
     * @return null
     */
    public static function getDSN()
    {
        return self::instance()->get('dsn');
    }

    /**
     * @param $value
     */
    public static function setDSN($value)
    {
        self::instance()->set('dsn', $value);
    }

    /**
     * @param $map
     */
    public static function setControllerMap($map)
    {
        self::instance()->set('controllermap', $map);
    }
}