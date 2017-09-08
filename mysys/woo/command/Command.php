<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月15日
 * Time: 14:42
 */

namespace woo\command;



use woo\controller\Request;

abstract class Command
{

    final function __construct() { } // 设置为final，任何子类不能覆盖这个构造方法

    public function execute(Request $request)
    {
        $this->doExecute($request);
    }

    abstract public function doExecute(Request $request);
}