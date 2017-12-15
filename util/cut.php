<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年12月15日
 * Time: 10:23
 */

header("Content-Type:text/html;charset=utf-8");

$org = 650;
$begin = '2017-12-14';
$beginTimestamp = strtotime($begin);
$cur = date('Y-m-d');

echo sprintf('<b>%s交易金额为：%d元</b><br>', $begin, $org);
echo sprintf('<b>掉价规则：一天掉5元，满七天后掉50元，例如650六天后剩余620，七天后剩余600</b><br><br>');

$days = (strtotime($cur) - strtotime($begin)) / (60 * 60 * 24);
for ($i = 1; $i <= $days; $i++) {
    if ($i % 7 === 0) {
        $org -= 20;
        echo sprintf('<b style="color: orange">[%s] 满7天掉价50元，剩余金额为：<b style="color:red">%d</b>元</b>', date('Y-m-d', strtotime("+ $i days", $beginTimestamp)), $org);
        echo '<br>';
    } else {
        $org -= 5;
        echo sprintf('[%s] 掉价5元，剩余金额为：<b style="color:red">%d</b>元', date('Y-m-d', strtotime("+ $i days", $beginTimestamp)), $org);
    }
    echo '<br>';
}

