<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年08月07日
 * Time: 10:03
 */

header("Content-Type:text/html;charset=utf-8");
try {
    $dbh = new PDO('mysql:dbname=shop;host=localhost', 'root', '');
    $dbh->query('set names utf8');
} catch (PDOException $e) {
    echo '数据库连接失败：' . $e->getMessage();
    exit;
}

echo '<table border="1" align="center" width=90%>';
echo '<caption><h1>列表</h1></caption>';
echo '<tr bgcolor="#cccccc">';
echo '<th>UID</th><th>标题</th></tr>';

//使用query方式执行SELECT语句，建议使用prepare()和execute()形式执行语句
$stmt = $dbh->query("select id,name FROM zc_goods limit 10");

//以PDO::FETCH_NUM形式获取索引并遍历
while (list($id, $name) = $stmt->fetch(PDO::FETCH_NUM)) {
    echo '<tr>';
    echo '<td>' . $id . '</td>';
    echo '<td>' . $name . '</td>';
    echo '</tr>';
}

echo '</table>';