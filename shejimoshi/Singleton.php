<?php

/**
 * 单例模式 （创建型模式）
 * 说明：确保某个类只有一个实例，并且向整个系统全局地提供这个实例
 * 例子：PHP与数据库的交互，控制配置信息
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年06月14日
 * Time: 9:15
 */
class Singleton
{

    /**
     * @var Singleton 保存类的实例
     */
    private static $_instance;

    /**
     * 设置为private,防止外部实例化对象
     */
    private function __construct()
    {
        echo "This is a Constructed method;";
    }

    /**
     * 防止对象被克隆
     */
    public function __clone()
    {
        trigger_error('Clone is not allow !', E_USER_ERROR);
    }

    /**
     * @return Singleton
     */
    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * test method
     */
    public function test()
    {
        echo '调用方法成功';
    }
}

// 正确的调用方法
$singleton = Singleton::getInstance();
$singleton->test();

// 试图clone对象时，会触发一个错误
$singleton_clone = clone $singleton;
