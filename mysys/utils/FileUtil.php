<?php

namespace utils;

/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月19日
 * Time: 17:07
 */

class FileUtil
{

    /**
     * 创建目录
     * @param string $dirName 目录，./dir1/dir2/dir3
     * @param int $mode 权限
     * @param bool|true $recursive 是否递归创建子目录
     * @since 2017-05-19
     * @return bool
     */
    public static function mkdir($dirName, $mode = 0777, $recursive = true)
    {
        if (is_dir($dirName)) {
            return true;
        } else {
            return $dirName ? mkdir($dirName, $mode, $recursive) : false;
        }
    }
}