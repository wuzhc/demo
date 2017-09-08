<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月15日
 * Time: 13:32
 */

namespace woo\controller;


use woo\command\CommandResolver;

class Controller
{
    private function __construct() { }

    /**
     * 入口
     */
    public static function run()
    {
        $controller = new Controller();
        $controller->init();
        $controller->handleRequest();
    }

    /**
     * 主要用于加载配置文件
     */
    protected function init()
    {
        ApplicationHelper::instance()->init();
    }

    /**
     * 执行请求
     */
    protected function handleRequest()
    {
        // 实例化Request对象，并注册到requestRegistry
        $request = new Request();
        // 命令解析器
        $cmdResolver = new CommandResolver();
        // 根据请求解析命令，获取相对的命令对象
        $cmd = $cmdResolver->getCommand($request);
        // 执行业务逻辑
        $cmd->execute($request);
    }

}