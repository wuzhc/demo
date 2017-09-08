<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月20日
 * Time: 13:57
 *
 * file_get_contents('php://input')使用案例1
 *
 */
include '../log.php';

$xml = '<xml><eg>ergh</eg><ss>dddd</ss></xml>';//要发送的xml
$url = 'http://wuzhc.cm/test/input/getXml.php';//接收XML地址
$header = 'Content-type: text/xml';//定义content-type为xml

$ch = curl_init(); //初始化curl
curl_setopt($ch, CURLOPT_URL, $url);//设置链接
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息 1会返回数据，0直接显示数据
curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));//设置HTTP头
curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);//POST数据
$response = curl_exec($ch);//接收返回信息
if (curl_errno($ch)) {//出错则显示错误信息
    print curl_error($ch);
}

curl_close($ch); //关闭curl链接
//echo $response;//显示返回信息

