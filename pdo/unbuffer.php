<?php
/**
 * 大数据结果集PHP内存溢出问题
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月27日
 * Time: 9:48
 */

try {
    $dbh = new PDO('mysql:dbname=zcshop;host=localhost', 'root', '');
    $dbh->query('set names utf8');
} catch (PDOException $e) {
    echo '数据库连接失败：' . $e->getMessage();
    exit;
}

$sql = 'select fdCreate,fdTitle from zc_goods_back';
$dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false); // 默认是使用buffer，设置为false关闭它
$stmt = $dbh->prepare($sql);
$stmt->execute();
while (list($create, $title) = $stmt->fetch(PDO::FETCH_NUM)) {
    echo $create . ' : ' . $title . PHP_EOL;
}

