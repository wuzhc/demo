<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月21日
 * Time: 11:44
 */
namespace redis;

class Object {

    /**
     * __get()拦截器，访问未定义的属性被调用
     * @param string $name
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this,$method)) {
            return $this->$method();
        } else {
            printf('%s isn\'t exist',$method);
        }
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name,$value)
    {
        $method = 'set' . ucfirst($name);
        if (method_exists($this,$method)) {
            return $this->$method($value);
        } else {
            printf('%s isn\'t exist',$method);
        }
    }
}