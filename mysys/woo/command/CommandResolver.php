<?php
/**
 * 命令解析器，用于请求解析命令
 *
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月15日
 * Time: 14:30
 *
 */

namespace woo\command;


use woo\controller\Request;

class CommandResolver
{

    private static $baseCmd;
    private static $defaultCmd;

    /**
     * 实例化默认cmd
     */
    public function __construct()
    {
        if (!self::$baseCmd) {
            self::$baseCmd = new \ReflectionClass('\woo\command\Command');
            self::$defaultCmd = new DefaultCommand();
        }
    }

    /**
     * 根据请求获取执行命令
     * @param Request $request
     * @return object|DefaultCommand
     */
    public function getCommand(Request $request)
    {
        $cmd = $request->getProperty('cmd');
        $sep = DIRECTORY_SEPARATOR;
        if (!$cmd) {
            return self::$defaultCmd;
        }
        $cmd = str_replace(array('.', $sep), '', $cmd);
        $filepath = "woo{$sep}command{$sep}{$cmd}.php";
        $classname = "woo\\command\\{$cmd}";
        if (file_exists($filepath)) {
            require_once $filepath;
            if (class_exists($classname)) {
                $cmdClass = new \ReflectionClass($classname);
                if ($cmdClass->isSubclassOf(self::$baseCmd)) {
                    return $cmdClass->newInstance();
                } else {
                    $request->addFeedback("command '$cmd' is not a command'");
                }
            }
        }
        $request->addFeedback("command '$cmd' not found");
        return clone self::$defaultCmd;
    }

}