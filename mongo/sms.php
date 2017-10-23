<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年10月23日
 * Time: 16:49
 */

include 'CMongo.php';

function request($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

function parse($content)
{
    return explode('#\s+#i', $content);
}

for ($i=23; $i<24; $i++) {
    $records = array_filter(explode('<br>', request($url)));
    foreach ($records as $record) {
        preg_match('/\[([\s\S]+)\]\s\[(\w+)\]\s(\w+)\s:\s([\s\S]+)/', $record, $match);
        array_shift($match);
        list($time, $from, $phone, $msg) = $match;
        \mongo\CMongo::instance()->reset();
        \mongo\CMongo::instance()->insert('foo', [
            'time' => $time,
            'from' => $from,
            'phone' => $phone,
            'msg' => rtrim($msg)
        ]);
    }
}
