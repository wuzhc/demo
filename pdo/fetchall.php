<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年08月07日
 * Time: 10:05
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
echo '<th>id</th><th>标题</th></tr>';

$startMemory = memory_get_usage();
$startTime = microtime(true);

//使用query方式执行SELECT语句，建议使用prepare()和execute()形式执行语句
$stmt = $dbh->prepare("select * FROM zc_goods limit 100000");
$stmt->execute();
//var_dump($stmt);
echo json_encode($stmt);
$memory = round((memory_get_usage() - $startMemory) / 1024 / 1024, 3) . 'M' . PHP_EOL;
$time = round(microtime(true) - $startTime, 6);
echo 'memory: ' . $memory;
echo 'time: ' . $time;
echo "\n";
echo "\n";
exit;

$allrows = $stmt->fetchAll(PDO::FETCH_ASSOC);       //以关联下标从结果集中获取所有数据

//以PDO::FETCH_NUM形式获取索引并遍历
foreach ($allrows as $row) {
    echo '<tr>';
    echo '<td>' . $row['id'] . '</td>';
    echo '<td>' . $row['name'] . '</td>';
    echo '</tr>';
}

echo '</table>';
//以下是在fetchAll()方法中使用两个特别参数的演示示例
$stmt->execute();
$row = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);   //从结果集中获取第二列的所有值
echo '所有标题：';
print_r($row);