<?php

$redis = new Redis();
$redis->connect('127.0.0.1');
$res = $redis->info('REPLICATION');
print_r($res);