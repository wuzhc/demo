<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月15日
 * Time: 11:14
 */

class autoloader
{
    /**
     * @param $name
     * @return bool
     */
    public static function loadByNamespace($name)
    {
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $name);
        $classFile = __DIR__ . DIRECTORY_SEPARATOR . "$classPath.php";

        if (is_file($classFile)) {
            require_once($classFile);
            if (class_exists($name, false)) {
                return true;
            }
        }
        return false;
    }
}

spl_autoload_register('autoloader::loadByNamespace');