<?php
/**
 * 限流例子
 */

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

/**
 * 手机发送限制
 * @param $phone
 * @return bool
 */
function isSendPhoneLimit($phone)
{
    global $redis;
    $phoneKey = 'phone:limit:' . $phone;
    // 设置phoneKey，如果res为false，说明phoneKey已经设置过，则
    // 判断发送次数加一并判断次数是否超过极值
    $res = $redis->set($phoneKey, 1, array('nx', 'ex' => 60));
    if (false === $res && $redis->incr($phoneKey) > 2) {
        return false;
    }

    $ip = getClientIP();
    $ipKey = 'ip:limit:' . $ip;
    // 设置phoneKey，如果res为false，说明phoneKey已经设置过，则
    // 判断发送次数加一并判断次数是否超过极值
    $res = $redis->set($ipKey, 1, array('nx', 'ex' => 864000));
    if (false === $res && $redis->incr($ipKey) > 100) {
        return false;
    }

    return true;
}

function getClientIp()
{
    $ip = '';
    $xip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    $cip = $_SERVER['HTTP_CLIENT_IP'];
    $rip = $_SERVER['REMOTE_ADDR'];

    //使用代理服务器情况
    if ($xip && strcasecmp($xip, 'unknown')) {
        if (false !== strpos($xip, ',')) {
            $ipArr = explode(',', $xip);
            foreach ($ipArr as $val) {
                if (trim(strtolower($val)) != 'unknown') {
                    $ip = $val;
                    break;
                }
            }
        } else {
            $ip = $xip;
        }
    } elseif ($cip && strcasecmp($cip, 'unknown')) {
        $ip = $cip;
    } elseif ($rip && strcasecmp($rip, 'unknown')) {
        $ip = $rip;
    }

    preg_match("/[\d\.]{7,15}/", $ip, $match);
    return $match[0] ? $match[0] : 'unknown';
}

/**
 * 商场产品交易
 * @since 2017-09-11
 */
function business()
{
    global $redis;
    $uid = rand(1, 5);
    $marget = 'market:goods';
    $user = 'user:' . $uid;

    $goodsID = rand(1, 100);
    $goods = 'market:goods:' . $goodsID;

    if (false === $redis->sIsMember($marget, $goodsID)) {
        exit("nothing \n");
    }

    // 对操作的商品加锁
    if ($identify = acquireLock($goods)) {
        try {
            if ($redis->sRem($marget, $goodsID)) {
                // 移动商品到用户集合
                $redis->sAdd($user, $goodsID);
                // do something
            }
        } catch (RedisException $e) {
            echo $e->getMessage() . PHP_EOL;
        } finally {
            releaseLock($goods, $identify);
        }
    }
}

business();

/**
 * 交易初始化
 */
function businessInit()
{
    global $redis;
    $redis->multi(Redis::PIPELINE);
    for ($i = 1; $i <= 100; $i++) {
        $redis->sAdd('market:goods', $i);
    }
    $redis->exec();
}

//businessInit();

/**
 * 抢购初始化
 */
function buyingInit()
{
    global $redis;
    $redis->multi(Redis::PIPELINE);
    for ($i = 1; $i <= 100; $i++) {
        $redis->rPush('goods:total', $i);
    }
    $redis->exec();
}

/**
 * 抢购
 */
function buying()
{
    global $redis;
    $uid = rand(1, 99);
    $key = 'success:buy';

    if ($redis->lPop('goods:total')) {
        // 用集合保存用户ID，可以保证值是唯一的
        if (empty($redis->sAdd($key, $uid))) {
            // 如果sadd失败，则重新加入到goods:total队列
            $redis->rPush('goods:total', $uid);
        }
    } else {
        echo "empty \n";
    }
}

//buyingInit();
//buying();

/**
 * 加锁
 * @param string $lockName 锁名称
 * @param int $timeout 获取失败时重试超时时间
 * @return bool|string
 */
function acquireLock($lockName, $timeout = 1)
{
    global $redis;
    $identifier = uniqid();
    $end = time() + $timeout;
    while (time() <= $end) {
        if ($redis->set($lockName, $identifier, array('nx', 'ex' => 60))) {
            return $identifier;
        }
    }
    return false;
}

/**
 * 释放锁
 * @param string $lockName 锁名称
 * @param string $identifier 标识
 * @return bool
 */
function releaseLock($lockName, $identifier)
{
    global $redis;
    while (true) {
        // 监控锁，如果有人改动锁，则重试
        $redis->watch($lockName);
        if ($redis->get($lockName) == $identifier) {
            $redis->multi(Redis::MULTI);
            $redis->del($lockName);
            $res = $redis->exec();
            if (isset($res[0]) && $res[0] == 1) {
                return true;
            }
        } else {
            $redis->unwatch();
            return false;
        }
    }
}
