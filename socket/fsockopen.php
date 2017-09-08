<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/19
 * Time: 21:01
 */

function check_url($url)
{
    //解析url
    $url_pieces = parse_url($url);
    //设置正确的路径和端口号
    $path = (isset($url_pieces['path'])) ? $url_pieces['path'] : '/';
    $port = (isset($url_pieces['port'])) ? $url_pieces['port'] : '80';
    //用fsockopen()尝试连接
    if ($fp = fsockopen($url_pieces['host'], $port, $errno, $errstr, 30)) {
        //建立成功后，向服务器写入数据
        $send = "HEAD $path HTTP/1.1\r\n";
        $send .= "HOST:" . $url_pieces['host'] . "\r\n";
        $send .= "CONNECTION: CLOSE\r\n\r\n";

        stream_set_blocking($fp, 1);
//        stream_set_timeout($fp, 10);
        fwrite($fp, $send);

        $res = stream_get_meta_data($fp);
        print_r($res);exit;

//        while (!feof($fp)) {
//            if (($head = @fgets($fp)) && ($head == "\r\n" || $head == "\n")) {
//                echo $head.'sss';
//                echo php_sapi_name() == 'cli' ? PHP_EOL : '<br>';
//                break;
//            }
//        }

        //检索HTTP状态码
        $data = fgets($fp, 128);

//        $data = '';
//        while (!feof($fp)) {
//            $data .= @fgets($fp);
//        }

//        echo $data; exit;


        //关闭连接
        fclose($fp);
        //返回状态码和类信息
        list($response, $code) = explode(' ', $data);
        if ($code == 200) {
            return array($code, 'good');
        } else {
            return array($code, 'bad');//数组第二个元素作为css类名
        }
    } else {
        //没有连接
        return array($errstr, 'bad');
    }

}

//创建URL列表
$urls = array(
//    'http://xinxin54.pw/search.asp?searchword=%B0%D7%C4%BE%D3%C5%D7%D3',
//    'http://www.example.com',
    'http://www.cnblogs.com/baocheng/p/5902560.html'
);
//调整PHP脚本的时间限制：
//set_time_limit(0);//无限长时间完成任务
//逐个验证url：
foreach ($urls as $url) {
    list($code, $class) = check_url($url);
    echo "<p><a href =\"$url\">$url</a>(<span class =\"$class\">$code</span>)</p>";

}
?>