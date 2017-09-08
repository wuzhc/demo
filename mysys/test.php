<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月15日
 * Time: 13:39
 */

use woo\base\ApplicationRegistry;
use woo\base\RequestRegistry;
use woo\controller\Request;

include 'Autoloader.php';

//RequestRegistry::instance()->setRequest(new Request());
//$res = RequestRegistry::instance()->getRequest()->getProperty('name');
//print_r($res);
//
//echo PHP_EOL . '------------------------------------' . PHP_EOL;
//
////ApplicationRegistry::setDSN('wuzhencan');
//$res = ApplicationRegistry::getDSN();
//$res2 = ApplicationRegistry::getDSN();
//$res3 = ApplicationRegistry::getDSN();
//$res4 = ApplicationRegistry::getDSN();
//var_dump($res,$res2,$res3,$res4);


// test simplexml_load_file
//$filePath = './data/command.xml';
//$res = @simplexml_load_file($filePath);
//
//foreach ($res->control->view as $view) {
//    $s = $view['status'];
//    $v = (string)$view;
//    echo $s, $v , PHP_EOL;
//}

// test mkdir
//echo \utils\FileUtil::mkdir('/haha/sss') ? 'y' : 'n';

// test file_put_content
//$res = file_put_contents('./data/ha', 'dkfjksdfjkjkfsdfsdf'); // 文件不存在会自己创建一个新文件
//echo $res ? 'y' : 'n';
