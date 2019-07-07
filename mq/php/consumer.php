<?php
// 消费者
// php consumer.php
// by wuzhc
include 'JsonRPC.php';

$n = 0;
$topics = ['topic_1', 'topic_2', 'topic_3', 'topic_4', 'topic_5'];
while (true) {
    try {
        $rpc = new JsonRPC('tcp://127.0.0.1:9503', 30);
        list($res, $err) = $rpc->pop($topics); // 同时订阅多个topic
        if ($err) {
            echo sprintf("err:%s, sleep 3s\n", $err->msg);
            sleep(3);
        } else {
            $n++;
            echo sprintf("%d success \n", $n);
            echo sprintf("content:%s \n", implode(',', $res));

            // 如果有设置超时时间,一定要显示调用ack确认,否则job将重复被消费
            if ($res['TTR'] > 0) {
                list(, $err) = $rpc->ack($res['id']);
                if ($err) {
                    echo sprintf("ack err:%s\n", $err->msg);
                } else {
                    echo sprintf("%s ack success\n", $res['id']);
                }
            }
            echo PHP_EOL;
        }
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
        sleep(3);
    }
}

