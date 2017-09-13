<?php
/**
 * Created by PhpStorm.
 * User: wuzhc
 * Date: 2017年09月12日
 * Time: 14:11
 */

// 遍历sentinel节点获取一个可用的sentinel节点
// sentinel get-master-addr-by-name <master-name> 获取主节点信息
// 验证主节点是否真正的主节点

include 'base.php';

class ZRedis
{
    private static $instance;

    private function __construct()
    {

    }

    private function __clone()
    {

    }

    /**
     * @param $flag
     * @return PRedis
     */
    public static function instance($flag = null)
    {
        if (!self::$instance[$flag]) {
            if ($flag === 'master') {
                self::$instance['master'] = self::getMaster();
            } else {
                $slaves = self::getSlaves();
                return array_rand($slaves);
            }
        }

        return self::$instance[$flag];
    }

    /**
     * 获取主节点
     * @return bool|PRedis
     */
    public static function getMaster()
    {
        // sentinel节点集合
        $sentinels = [
            ['127.0.0.1', 26379],
            ['127.0.0.1', 26380],
            ['127.0.0.1', 26381]
        ];

        $sentinelObj = null;
        foreach ($sentinels as $sentinel) {
            $sentinelObj = new PRedis($sentinel[0], $sentinel[1]);
            if ($sentinelObj->exec('ping')) {
                break;
            }
        }

        if ($sentinelObj === null) {
            return false;
        }

        $getMasterCommand = 'sentinel get-master-addr-by-name mymaster';
        list($masterIP, $masterPort) = $sentinelObj->exec($getMasterCommand);
        return new PRedis($masterIP, $masterPort);
    }

    /**
     * 获取从节点
     * @return array|bool|string
     */
    public static function getSlaves()
    {
        /** @var PRedis $master */
        $master = self::instance('master');
        $info = $master->exec('info replication');
        return $info;
    }
}