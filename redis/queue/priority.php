<?php
/**
 * 优先级队列
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017-09-08
 * Time: 10:30
 */

include 'conn.php';

// 设置优先级队列
$high = 'goods:high:task';
$mid = 'goods:mid:task';
$low = 'goods:low:task';

$stmt = $pdo->prepare('select id, cid, name from zc_goods limit 200000');
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // cid 小于100放在低级队列
    if ($row['cid'] < 100) {
        $redis->rPush($low, json_encode($row));
    }
    // cid 100到600之间放在中级队列
    elseif ($row['cid'] > 100 && $row['cid'] < 600) {
        $redis->rPush($mid, json_encode($row));
    }
    // cid 大于600放在高级队列
    else {
        $redis->rPush($high, json_encode($row));
    }
}
$redis->close();