<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017Äê03ÔÂ21ÈÕ
 * Time: 14:42
 */

//setcookie("test", $_GET['id'], time()+3600, "/", "wuzhc.cm");

$array = array(
    array('title'=>'wuzhc'),
    array('title'=>'make'),
    array('title'=>'libai')
);

array_walk($array,function(&$arr){$arr['type']=1;});
print_r($array);

function test1()
{
    $n = 0;
    $i = 100000000;
    while($n<$i) {
        $n++;
    }
    echo $n;
}

function test2()
{
    $n = 0;
    $i = 100000000;
    while($n<$i) {
        $n++;
    }
    echo $n;
}

test1();
test2();