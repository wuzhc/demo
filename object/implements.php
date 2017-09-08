<?php

/**
 * 抽象类
 * 可以定义各种类型成员变量
 * 可以有构造方法
 * 可以定义普通成员方法
 * 至少有一个方法被声明为抽象方法
 * Class Father
 */
abstract class Father {

    public $val;

    /**
     * 抽象方法
     * @return mixed
     */
    abstract function method1();

    /**
     * 抽象方法
     * @return mixed
     */
    abstract function method2();

    /**
     * 普通方法
     */
    public function method3()
    {

    }
}

/**
 * Class Son
 */
class Son extends Father {
    public function method1()
    {
        echo 'one';
    }
    public function method2()
    {
        echo 'two';
    }
}

/**
 * 接口类
 * 不能定义各种类型的成员变量
 * 可以声明类常量
 * 所有的方法都必须被实现
 * 所有的方法默认是public类型，不能使用private和protected
 * 不能有构造方法
 * 一个类可以同时实现多个接口，但一个类只能继承于一个抽象类
 * Interface IFather
 */
Interface IFather {
    const VAL = '123456';
    function method1();
    function method2();
}

/**
 * Class ISon
 */
class ISon implements IFather {
    public function method1()
    {
        echo 'one';
    }
    public function method2()
    {
        echo 'two';
    }
}