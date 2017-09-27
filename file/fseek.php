<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月21日
 * Time: 13:57
 */

/**
 * 读取文件最后N行数据
 * @param string $file
 * @param int $n
 * @return array
 */
function tail($file = 'file.txt', $n = 10)
{
    $fp = fopen($file, "r");
    $line = $n;
    $pos = -2;
    $t = " ";
    $data = [];
    while ($line > 0) {
        while ($t != "\n") {
            fseek($fp, $pos, SEEK_END);
            $t = fgetc($fp);
            $pos--;
        }
        $t = " ";
        $data[] = fgets($fp);
        $line--;
    }
    fclose($fp);
    return $data;
}

function search($keyword, $file = 'file.txt')
{
    $fp = fopen($file, 'r');
    $t = ' ';
    $pos = 0;
    while (true) {
        // 读取完毕
        if (!fgetc($fp)) {
            break;
        }

        while ($t != "\n") {
            fseek($fp, $pos);
            $t = fgetc($fp);
            $pos++;
        }
        $lineData = fgets($fp);
    }

}