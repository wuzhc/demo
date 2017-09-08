<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月20日
 * Time: 13:56
 *
 * file_get_contents('php://input')使用案例1
 *
 */

include '../log.php';
$xmldata = file_get_contents("php://input");
$data = (array)simplexml_load_string($xmldata);
var_dump($data);
log::w($data);