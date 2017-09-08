<?php
/**
 * 延时队列
 * Created by PhpStorm.
 * User: wuzhc2016@163.com
 * Date: 2017-09-08
 * Time: 11:41
 */

include 'conn.php';

$stmt = $pdo->prepare('select id, cid, name from zc_goods limit 200000');
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $redis->zAdd('goods:delay:task', time() + rand(1, 60), json_encode($row));
}