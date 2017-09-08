<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年03月20日
 * Time: 14:01
 */

define('ROOT', __DIR__ . '/');

class log
{

    /**
     * @var
     */
    public static $logName = 'log.log';

    /**
     * Write log
     * @param $msg
     * @param $mode
     * @return bool
     */
    public static function w($msg, $mode = 'a+')
    {
        if ($msg === null) {
            return false;
        }

        $fp = fopen(ROOT . self::$logName, $mode);
        if (!$fp) {
            echo 'fopen() failed';
            return false;
        }

        if (!is_string($msg)) {
            $msg = json_encode($msg);
        }

        $max = 4;     //重试最大次数
        $retries = 0;
        do {
            if ($retries > 0) {
                sleep(1);
            }
            $retries++;
        } while (!flock($fp, LOCK_EX) && $retries < $max); //加锁是为了并发场景的出现问题
        if ($max == $retries) {
            return false;
        }

        $traces = debug_backtrace();
        $trace = array_pop($traces);
        $header = '[' . date('Y-m-d H:i:s', time()) .' => '. $trace['file'] .']';
        $header .= PHP_EOL;

        fwrite($fp, $header);
        fwrite($fp, $msg, strlen($msg));
        fwrite($fp, "\r\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * Delete log
     * @param bool $hint
     * @return bool
     */
    public static function d($hint = false)
    {
        $res = @unlink(ROOT . self::$logName);
        if ($hint) {
            echo $res ? 'delete log success' : 'delete log fail';
        }
        return $res;
    }

    /**
     * Read log
     * @return bool
     */
    public static function r()
    {
        @ob_end_clean();
        @ob_implicit_flush(true); /* output data immediately */

        $fp = fopen(ROOT . self::$logName, 'r');
        if (!$fp) {
            echo 'fopen() failed';
            return false;
        }
        while (!feof($fp)) {
            echo fgets($fp, 8192);
            echo php_sapi_name() == 'cli' ? PHP_EOL : '<br>';
        }
        fclose($fp);
    }

    /**
     * 创建目录
     * @param $path
     * @param int $mode
     * @return bool
     */
    public static function createDir($path, $mode = 0777)
    {
        if (is_dir($path)) {
            return true;
        }
        return mkdir($path, $mode, true);
    }

    /**
     * 删除目录
     * @param $dir
     * @return bool
     */
    public static function delDir($dir)
    {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delDir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    /**
     * @param $dir
     * @return array
     */
    public static function readThreeDir($dir)
    {
        $result = array();

        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value,array('.','..'))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = self::readThreeDir($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @param $path
     * @return array
     */
    public static function readDir($path)
    {
        return array_diff(scandir($path), array('.', '..'));
    }
}
