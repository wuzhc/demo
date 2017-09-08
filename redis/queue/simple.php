<?php
/**
 * 基于redis列表数据结构的简单队列
 * Created by PhpStorm.
 * User: wuzhc163.com
 * Date: 2017年09月08日
 * Time: 10:30
 */

$stmt = $pdo->prepare('select id, cid, name from zc_goods limit 200000');
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $redis->rPush('goods:task', json_encode($row));
}

$redis->close();