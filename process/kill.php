<?php
/**
 * Created by PhpStorm.
 * User: wuzc
 * Date: 17-12-6
 * Time: 下午9:36
 */

if ($argc < 2 || $argv[1] == '--help') {
    exit("Usage: $argv[0] pid [num]");
}

$num = !empty($argv[2]) ? $argv[2] : 1;
$num = max($num, 1);

for ($i=0;$i<$num;$i++){
    posix_kill($argv[1], SIGINT);
//    posix_kill($argv[1], SIGUSR2);
}