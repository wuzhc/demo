<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月12日
 * Time: 15:15
 */

include 'sentinel.php';

$rs = ZRedis::instance()->exec('set name kk');
print_r($rs);