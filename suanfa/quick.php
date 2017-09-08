<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月24日
 * Time: 14:53
 *
 * 快速排序算法
 */

$arr = array(45, 78, 23, 78, 6, 4, 87, 87, 54, 56, 3, 4, 5, 64, 3, 1, 546, 89);
echo implode(' < ', $arr);
echo '<br>';
echo implode(' < ', quick($arr));

function quick($arr)
{
    $len = count($arr);
    if ($len <= 1) {
        return $arr;
    }

    $leftArr = $rightArr = array();
    for ($i = 1; $i < $len; $i++) {
        if ($arr[$i] < $arr[0]) {
            $leftArr[] = $arr[$i];
        } else {
            $rightArr[] = $arr[$i];
        }
    }

    $leftArr = quick($leftArr);
    $rightArr = quick($rightArr);
    return array_merge($leftArr, array($arr[0]), $rightArr);
}