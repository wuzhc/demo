<?php

/**
 * 策略模式（行为型模式）
 * 对一组算法进行封装，简单来说，就是一个问题有多个方法（策略）解决，策略模式就是将多个方法
 * 分别封装到一个类，并继承一个公共的抽象基类
 *
 * 角色：
 * 抽象策略类：定义了实现抽象策略类必须实现方法
 * 策略类一：具体的策略业务
 * 策略类二：具体的策略业务
 * 客户端：调用具体的策略
 *
 * @author wuzhc2016@163.com
 */

/**
 * 抽象策略基类
 * Class Strategy
 */
abstract class Strategy
{
    abstract public function doSomething();
}

/**
 * 具体策略一
 * Class StrategyOne
 */
class StrategyOne extends Strategy
{
    public function doSomething()
    {
        return 'bus';
    }
}

/**
 * 具体策略二
 * Class StrategyTwo
 */
class StrategyTwo extends Strategy
{
    public function doSomething()
    {
        return 'plane';
    }
}

/**
 * 客户端
 * Class User
 */
class Client
{
    private $_tool;

    public function __construct(Strategy $tool)
    {
        $this->_tool = $tool;
    }

    public function run()
    {
        echo 'I choose the ' . $this->_tool->doSomething();
    }
}


//------------ test --------------

$func = function (Strategy $strategy) {
    $client = new Client($strategy);
    $client->run();
    echo PHP_EOL;
};

$func(new StrategyOne());
$func(new StrategyTwo());
