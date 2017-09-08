<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017Äê08ÔÂ30ÈÕ
 * Time: 16:58
 */

try {
    $pdo = new PDO('mysql:dbname=shop;host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}

$ids = [75453, 93167];
$sql = 'select id, name from zc_goods where id = 75453';
$stmt = $pdo->prepare($sql);
//foreach ($ids as $k => $id) {
//    $stmt->bindValue(($k+1), $id);
//}

//$id = 75453;
//$stmt->bindParam(':id', $id);
$rs = $stmt->fetchAll();
print_r($rs);
echo $stmt->queryString;