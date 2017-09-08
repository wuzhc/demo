<?php

$redis = new Redis();
$redis->connect('127.0.0.1');

$startTime = microtime(true);
while ($row = $redis->lPop('goods:run')) {
    $row = json_decode($row, true);
    echo $row['id'] . ':' . $row['name'] . PHP_EOL;
}
echo microtime(true) - $startTime;
