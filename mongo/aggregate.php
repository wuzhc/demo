<?php
/**
 * Created by PhpStorm.
 * User: wuzc
 * Date: 17-10-23
 * Time: 下午11:03
 */

if (extension_loaded('mongodb')) {
    include 'CMongodb.php';
    $pipeline = [
        ['$group' => ['_id' => '$phone', 'count' => ['$sum' => 1]]],
        ['$sort' => ['count' => -1]],
    ];
    $cursor = \mongo\CMongodb::instance()->aggregate('wuzhc', 'sms', $pipeline);
    foreach ($cursor as $document) {
        echo $document->_id . ' : ' . $document->count . PHP_EOL;
    }
} elseif (extension_loaded('mongo')) {
    include 'CMongo.php';
    echo 'mongo aggregate';
} else {
    echo 'Can not support mongo';
}
