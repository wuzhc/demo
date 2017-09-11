<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017��09��08��
 * Time: 10:33
 */

try {
    $pdo = new PDO('mysql:dbname=shop;host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->query('set names utf8');
} catch (PDOException $e) {
    exit($e->getMessage());
}

$redis = new Redis();
$redis->connect('127.0.0.1', 6380);
