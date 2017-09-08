<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年05月15日
 * Time: 13:34
 */

namespace woo\controller;


use utils\LogUtil;
use woo\base\ApplicationRegistry;

class ApplicationHelper
{
    /** @var  ApplicationHelper */
    private static $instance;
    private $config = 'data/config.xml';

    private function __construct() { }

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }    
        return self::$instance;
    }

    /**
     * @throws \ErrorException
     */
    public function init()
    {
        if (!ApplicationRegistry::getDSN()) {
            return ;
        }
        $this->getOptions();
    }

    /**
     * @throws \ErrorException
     */
    private function getOptions()
    {
        if (!file_exists($this->config)) {
            LogUtil::w('config file is not exist');
            throw new \ErrorException('config file is not exist');
        }
        $options = simplexml_load_file($this->config);

        // 设置DSN
        $dsn = (string)$options->dsn;
        if (!$dsn) {
            LogUtil::w('no DSN found');
            throw new \ErrorException('no DSN found');
        }
        ApplicationRegistry::setDSN($dsn);

        // 设置controllerMap
        $map = new ControllerMap();
        foreach ($options->control->view as $view) {
            $status = $view['status'];
            $map->addView('default', $status, (string)$view);
        }
        ApplicationRegistry::setControllerMap($map);

    }

}