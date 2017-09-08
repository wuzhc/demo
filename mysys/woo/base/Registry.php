<?php

/**
 * 注册表抽象类
 * 为应用的各个层提供接口调用
 *
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月15日
 * Time: 10:28
 */

namespace woo\base;

abstract class Registry
{
    abstract protected function get($key);
    abstract protected function set($key, $value);
}


