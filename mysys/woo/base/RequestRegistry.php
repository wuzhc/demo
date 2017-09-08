<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月15日
 * Time: 10:59
 */

namespace woo\base;


use woo\controller\Request;

class RequestRegistry extends Registry
{
    /** @var RequestRegistry */
    protected static $instance;
    private $values = array();

    private function __construct() { }

    /**
     * @return RequestRegistry
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
        if (isset($this->values[$key])) {
            return $this->values[$key];
        } else {
            return null;
        }
    }

    /**
     * @param $key
     * @param $value
     */
    protected function set($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * @return Request
     */
    public static function getRequest()
    {
        return self::instance()->get('request');
    }

    /**
     * @param Request $request
     */
    public static function setRequest(Request $request)
    {
        self::instance()->set('request', $request);
    }
}
