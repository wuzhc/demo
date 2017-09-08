<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/21
 * Time: 13:32
 */

$requestUrls = [];

for ($i=0; $i<10; $i++) {
    array_push($requestUrls, 'http://104.194.84.229/zcshop/frontend/web/test/sms');
}


$mh = curl_multi_init();
$chs = [];
foreach ($requestUrls as $url) {
    $chs[] = $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3000);
    curl_setopt($ch, CURLOPT_NOSIGNAL, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
    curl_multi_add_handle($mh, $ch);
}

do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
} while ($running > 0);

$res = [];
foreach ($chs as $k => $ch) {
    $res[] = curl_multi_getcontent($ch);
    curl_multi_remove_handle($mh, $ch);
    echo $k . ' success' . PHP_EOL;
}

curl_multi_close($mh);