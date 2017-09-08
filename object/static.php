<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/25
 * Time: 22:36
 *
 * 延迟静态绑定
 * 场景父类的普通函数使用self调用到了静态属性或静态方法时，这时候静态方法或属性
 * 就已经被绑定到函数，如果子类重写父类的静态方法，已经不能改变父类函数的结果
 * @link http://www.cnblogs.com/codeAB/p/5560631.html
 */

abstract class Object {

}

class Student extends Object {

    public static function create()
    {
        return new Student();
    }

}

class Teacher extends Object {

    public static function create()
    {
        return new Teacher();
    }

}

//现在把student和teacher中的create()方法提到抽象类
class Object2 {

    public static function create()
    {
        return new static();
    }

}

class Student2 extends Object2{

}

class Teacher2 extends Object2 {

}

/*************************** demo *********************************/
echo get_class(Student::create());
echo "\r\n";
echo get_class(Teacher::create());


/***************************** demo2 *****************************/
echo "\r\n";
echo get_class(Student2::create());
echo "\r\n";
echo get_class(Teacher2::create());



//另一个例子
class ParentClass {
    public static function name()
    {
        return __CLASS__;
    }
    public static function test()
    {
        return self::name(); //改为static::name() 就可以了
    }
}

class ChildClass extends ParentClass {
    public static function name()
    {
        return __CLASS__;
    }
}

echo "\r\n";
echo ChildClass::test();