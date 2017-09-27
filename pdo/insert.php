<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月21日
 * Time: 17:41
 */

header("Content-Type:text/html;charset=utf-8");
try {
    $dbh = new PDO('mysql:dbname=shop;host=localhost', 'root', '');
    $dbh->query('set names utf8');
} catch (PDOException $e) {
    echo '数据库连接失败：' . $e->getMessage();
    exit;
}

for ($i = 1; $i < 10000; $i++) {
    $stmt = $dbh->query('insert into layout_test values(:col1, :col2)');
    $stmt->bindParam(':col1', $i);
    $stmt->bindValue(':col2', rand(1, 100));
    $stmt->execute();
}

