<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月24日
 * Time: 15:04
 *
 * 选择排序算法
 */

$arr = array(45, 78, 23, 78, 6, 4, 87, 87, 54, 56, 3, 4, 5, 64, 3, 1, 546, 89);
echo implode(' < ', $arr);
echo '<br>';

$len = count($arr);
for ($i = 0; $i < $len - 1; $i++) {
    $mixPos = $i;
    for ($j = $i + 1; $j < $len; $j++) {
        if ($arr[$j] < $arr[$mixPos]) {
            $mixPos = $j;
        }
    }
    if ($mixPos != $i) {
        $temp = $arr[$mixPos];
        $arr[$mixPos] = $arr[$i];
        $arr[$i] = $temp;
    }
}

echo implode(' < ', $arr);