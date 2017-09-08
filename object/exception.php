<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/25
 * Time: 23:16
 *
 * 异常未捕获会发生一个严重的错误
 * 1、使用 set_exception_handler() 处理
 * 2、使用 try catch
 */

//create function with an exception function
function checkNum($number)
{
    try {
        if ($number > 1) {
            throw new Exception('Value must be 1 or below');
        }
        echo "\r\n";
        echo '抛出异常后是不是还会继续执行呢';
        return true;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

//trigger exception
//checkNum(2);

//set_exception_handler custom handle exception
set_exception_handler(function($e){echo $e->getMessage();});
throw new Exception('set_exception_handler测试');

