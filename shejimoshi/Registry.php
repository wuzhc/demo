<?php

/**
 * 注册数模式（一种容易理解的模式）
 * 把对象实例注册到一个全局的对象集合上，它扮演类似于全局变量的角色
 *
 * @author wuzhc2016@163.com
 */
class Registry
{
    private static $_objects = [];

    /**
     * @param $alias
     * @param $object
     */
    public static function add($alias, $object)
    {
        self::$_objects[$alias] = $object;
    }

    /**
     * @param $alias
     */
    public static function remove($alias)
    {
        if (isset(self::$_objects[$alias])) {
            unset(self::$_objects[$alias]);
        }
    }

    /**
     * @param $alias
     * @return null
     */
    public static function get($alias)
    {
        return isset(self::$_objects[$alias])
            ? self::$_objects[$alias] : null;
    }
}


//------------- test -----------

class App
{
    public function run()
    {
        echo 'this is an app';
    }
}

Registry::add('app', new App());
/** @var App $app */
$app = Registry::get('app');
$app->run();