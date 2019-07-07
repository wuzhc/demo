<?php

include 'JsonRPC.php';

$n = 0;
$topics = ['topic_1', 'topic_2', 'topic_3', 'topic_4', 'topic_5'];
while (true) {
    try {
        $rpc = new JsonRPC('tcp://127.0.0.1:9503', 30);
        $id = 'xxxx_id' . microtime(true) . rand(1, 999999999);
        list($res, $err) = $rpc->push([
            'id'    => $id,
            'topic' => $topics[array_rand($topics, 1)],
            'body'  => 'this is a rpc test',
            'delay' => (string)rand(1, 60),
            'TTR'   => (string)rand(0, 30)
        ]);
        if ($err) {
            echo sprintf("err:%s \n", $err->msg);
        } else {
            echo sprintf("%s success \n", $id);
        }

         $n++;
         if ($n > 10000) {
            break;
         }
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
        sleep(3);
    }
}

