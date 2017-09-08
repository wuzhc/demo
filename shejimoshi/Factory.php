<?php

/**
 * 工厂模式 （创建型模式）
 * 说明：将创建对象的过程封装起来，通过传递不同的参数可以实例化不同的类
 * 例子：YII创建组件对象，PHP需要连接多个数据库
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年06月14日
 * Time: 8:55
 */
class Factory
{
    /**
     * 根据不同的参数实例化不同的数据库类
     * @param $name
     * @return Mysql|null|Oracle
     */
    public static function instance($name)
    {
        switch ($name) {
            case 'mysql':
                return new Mysql();
            case 'oracle':
                return new Oracle();
            default:
                return null;
        }
    }
}

$db = Factory::instance('mysql');
echo $db->getDBName();

/**
 * Class DB
 */
abstract class DB
{
    abstract public function getDBName();
}

/**
 * Class Mysql
 */
class Mysql extends DB
{
    public function getDBName()
    {
        return 'Mysql';
    }
}

/**
 * Class Oracle
 */
class Oracle extends DB
{
    public function getDBName()
    {
        return 'Oracle';
    }
}