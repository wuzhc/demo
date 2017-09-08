<?php
/**
 * Created by PhpStorm.
 * User: wuzc
 * Date: 17-7-1
 * Time: 下午9:43
 */


$file = date('Y/d') . '.log';
$url = sprintf('http://alisms.wm3dao.com/log.php?action=read&file=%s&user=sms', $file);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
$rs = curl_exec($ch);
curl_close($ch);

if ('fopen() failed' == $rs) {
    echo 'nothing';
} else {
    // send email
    echo 'yes';
    echo PHP_EOL;
    print_r($rs);
}