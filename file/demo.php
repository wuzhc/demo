<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月28日
 * Time: 13:33
 */

$filename = 'lorem_ipsum.txt';

//检查文件大小
echo filesize($filename);
echo PHP_EOL;

$file = fopen($filename, "a+");

//截取文件
ftruncate($file,100);
fclose($file);


//清除缓存并再次检查文件大小
clearstatcache();
echo filesize($filename);


$fp = fopen($filename, 'a+');
if (!$fp) {
    echo 'can\'n open filename';
    exit;
}

$retries = 0;
do {
    if ($retries > 0) {
        sleep(1);
    }
    $retries += 1;
} while (!flock($fp, LOCK_EX) && $retries < 3);

fwrite($fp, 'you content');
flock($fp, LOCK_UN);
fclose($fp);