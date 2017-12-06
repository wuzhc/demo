<?php
include "log.php";

$n = 0;
while (true) {
    echo $n++;
    echo ' ';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://gitweike.cm/index.php?r=tms/wrong/fix');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    echo $content = curl_exec($ch);
    echo PHP_EOL;
    curl_close($ch);

    if ($content == 'nothing') {
        echo 'nothing' . PHP_EOL;
        break;
    } elseif (!preg_match('/(success|failed)$/i', $content)) {
        echo 'error' . PHP_EOL;
        break;
    }
    log::w($content);
}
