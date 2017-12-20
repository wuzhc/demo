<?php

/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年08月15日
 * Time: 10:19
 */
class Person
{
    /** @var string 名字 */
    public $name;
    /** @var int 年龄 */
    public $age;
    /** @var object 班级对象 */
    private $_class;

    public function __construct($name = '佚名', zcClass $class = null)
    {
        $this->name = $name;
        $this->_class = $class;
    }

    public function getName()
    {
        return 'My name is ' . $this->name;
    }
}

Class zcClass
{
    /**
     * @return string
     */
    public function getName()
    {
        return '高一二班';
    }
}

$class = new ReflectionClass('Person');

// 获取公有属性和文档注释
$properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
foreach ($properties as $property) {
    echo $property->getDocComment();
    echo PHP_EOL;
    echo $property->getName();
    echo PHP_EOL;
}

echo PHP_EOL;
echo PHP_EOL;

// 获取公有方法
$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
foreach ($methods as $method) {
    echo $method->getName();
    echo PHP_EOL;
}

echo PHP_EOL;
echo PHP_EOL;

// 实例化类
$instance = $class->newInstanceArgs(['阿里']);
echo $instance->getName();

echo PHP_EOL;
echo PHP_EOL;

// 执行方法
$method = $class->getMethod('getName');
echo $method->invoke($instance);

echo PHP_EOL;
echo PHP_EOL;

// 构造器
$constructor = $class->getConstructor();
$params = $constructor->getParameters();
foreach ($params as $param) {
    if ($param->isDefaultValueAvailable()) {
        echo $param->getClass();
        echo $param->getDefaultValue();
        echo PHP_EOL;
    }
}