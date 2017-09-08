<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月23日
 * Time: 8:45
 *
 * 貌似只有IE游览器才需要P3P协议，谷歌测试一下，不需要设置P3P，也能跨域
 */

header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
setcookie("test", $_GET['id'], time()+3600, "/");
