<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年10月23日
 * Time: 16:49
 */

if (extension_loaded('mongodb')) {
    include 'CMongodb.php';
} elseif (extension_loaded('mongo')) {
    include 'CMongo.php';
} else {
    exit('Can not support mongo');
}

function request($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

$data = [];
for ($i=21; $i<24; $i++) {
//    $records = array_filter(explode('<br>', request($url)));
    $records = ['[2017-10-23 20:29:36] [kouzi] 17322535854 : Unrepeatable request'];
    foreach ($records as $record) {
        preg_match('/\[([\s\S]+)\]\s\[(\w+)\]\s(\w+)\s:\s([\s\S]+)/', $record, $match);
        array_shift($match);
        list($time, $from, $phone, $msg) = $match;
        $data[] = [
            'time' => $time,
            'from' => $from,
            'phone' => $phone,
            'msg' => rtrim($msg)
        ];
    }
}

if (class_exists('MongoDB\Driver\Manager')) {
    \mongo\CMongodb::instance()->insert($data, 'wuzhc.sms');
} else {
    \mongo\CMongo::instance()->batchInsert('sms', $data);
}
