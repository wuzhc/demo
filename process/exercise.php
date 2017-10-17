<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年10月17日
 * Time: 16:09
 */

$score = 0;

/**
 * 信号处理器
 * @param $signal
 */
function signal_handle($signal)
{
    global $score;
    switch ($signal) {
        case SIGINT:
            echo "Game Over \n";
            echo "Your Score is $score";
            exit(0);
            break;
        case SIGALRM:
            echo "Time Out \n";
            posix_kill(posix_getpid(), SIGINT);
            break;
    }
}

function main()
{
    global $score;

    // 安装信号处理器
    pcntl_signal(SIGINT, 'signal_handle');
    pcntl_signal(SIGALRM, 'signal_handle');

    // 调用信号函数
    pcntl_signal_dispatch();

    // 标准输入
    $stdin = fopen('php://stdin', 'r');
    while (1) {
        $a = rand(0, 10);
        $b = rand(0, 10);
        pcntl_alarm(5); // 5秒发一次SIGALRM信号

        $answer = trim(fgets($stdin));
        if ($answer == ($a * $b)) {
            $score++;
        } else {
            echo "Wrong! Your score is $score \n";
        }
    }
}