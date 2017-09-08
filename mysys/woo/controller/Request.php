<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月15日
 * Time: 11:10
 */

namespace woo\controller;


use woo\base\RequestRegistry;

class Request
{
    /**
     * 存放请求参数
     * @var array
     */
    private $properties = array();

    /**
     * 反馈信息
     * @var array
     */
    private $feedbacks = array();


    public function __construct()
    {
        $this->init();
        RequestRegistry::setRequest($this);
    }

    public function init()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->properties = $_REQUEST;
            return ;
        }

        // cli mode 命令行模式
        foreach ($_SERVER['argv'] as $arg) {
            if (strpos($arg, '=')) {
                list($key, $val) = explode('=', $arg);
                $this->setProperty($key, $val);
            }
        }
    }
    
    /**
     * @param $key
     * @return null
     */
    public function getProperty($key)
    {
        return isset($this->properties[$key]) ? $this->properties[$key] : null;
    }

    public function setProperty($key, $val)
    {
        $this->properties[$key] = $val;
    }

    public function addFeedback($msg)
    {
        $this->feedbacks[] = $msg;
    }

    public function getFeedbacks()
    {
        return $this->feedbacks;
    }

    public function getFeedbacksStr($step = "\n")
    {
        return implode($step, $this->feedbacks);
    }
}