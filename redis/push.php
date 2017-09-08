<?php          //push推送配置  注:使用前请确认log文件为空       2016-04-12
include_once(dirname (__FILE__)."/../../config.inc.php");
//if(exec('ps aux | grep redis_push.php | grep -v grep | wc -l') != 0) goto check;
import('push.class.php');
import('Redis.class.php');

$time  =time();
$data  = array("apikey"=>'xxxx',"secret"=>'xxxx');
$push  = new Channel($data);
$redis = new RedisCache($Credis['host'],$Credis['port']);
if(exec('ps aux | grep redis_push.php | grep -v grep | wc -l') != 0) goto check;//如果有推送任务 直接执行监控代码

/*PUSH配置项*/
$config = array(
    "file"=>"test.txt",
    "Title"=>"sssss",
    "Content"=>"ssssssssssssssss",
    "OpenType"=>"0",    //1是  0否    是否跳转链接
    "Url"=>"",         //链接地址
    "num"=>"500",      //每次推送条数
    "s"=>"1"           //睡眠时间 （单位：秒）
);
$num = 15;            //启动进程数量
$a = $config['OpenType']==1 ? "是" : "否";
$c = json_encode($config);
$info = <<<monkey
   ************ 请确认信息是否有误*10秒后启动push任务! *************
   * 文件名称   : {$config['file']};
   * 推送标题   : {$config['Title']};
   * 推送内容   : {$config['Content']};
   * 是否跳转   : {$config['OpenType']};
   * 进程数量   : $num;(如果为单进程无视此项)
   * 睡眠时间   : {$config['s']};
   * 日志目录   : /log;
   ***************************************************************\n
monkey;
echo $info;
sleep(3);
$n = 1;
while($n<=10){
    echo (10-$n++),"秒\n";
    sleep(1);
}
echo "------------------------- 任务已启动 -------------------------\n";
if($redis->Scount('push_getchannel_success')){
    echo "队列有未完成任务\n";
}else{
    $res = exec("php redis_getchannel.php {$config['file']}");//写入redis脚本
    echo $res;
}
smtp_mail('xxxx@qq.com','推送任务已开启','请实时监测,5秒后您的手机将接收到测试推送!');//推送监控 实现定时全自动推送 
echo "\n---------------- 5秒后 test 将收到测试推送消息 ----------------\n";
sleep(5);
$re = $push->BaiduPush('xxxx','xxxxx',$config['Content'],$config['Title'],'1',$config['OpenType'],$config['Url'],'xxxxx',$push);
sleep(1);
echo "\n---------------- 测试推送已发出!如未收到,请及时终止程序! 10秒后正式推送!!! ----------------\n";
$m = 1;
while($m<=10){
    echo (10-$m++),"秒\n";
    sleep(1);
}
echo "\n---------------- 推送任务已经开始!请耐心等待! ----------------\n";
//下面设置是否多进程
for($i=1;$i<=$num;$i++){
    exec("php redis_push.php  '{$c}' > /dev/null 2>&1 &");
}

check:
while(1){
    if(exec('ps aux | grep redis_push.php | grep -v grep | wc -l') == 0){
        echo "push 发送完成 用时",time()-$time,"秒";
        die();
    }
    echo "当前进程数：",exec('ps aux | grep redis_push.php | grep -v grep | wc -l'),"个","\n";
    echo "当前剩余推送数量：".$redis->Scount('push_getchannel_success')."\n";
    sleep(10);
}