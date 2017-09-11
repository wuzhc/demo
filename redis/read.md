#### （1）限制手机发送次数

##### 需求说明：
- 同个IP一天内只能发送100次
- 同个手机号一分钟内只能发送2次  

##### 命令说明：
- SET mykey "redis" EX 60 NX
>  
EX seconds − 设置指定的到期时间(以秒为单位)。
PX milliseconds - 设置指定的到期时间(以毫秒为单位)。
NX - 仅在键不存在时设置键。
XX - 只有在键已存在时才设置。    

##### 思路：
- 以ip和手机号码作为键，操作次数作为值，每次发送成功数值加一，第一次发送设置值为1
- 以期限作为键的过期时间，例如60秒内则设置过期时间为60
- 利用set nx命令特性，判断键是否已经设置过，如果设置过，则判断次数是否超过极值

```php
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
    // 设置ipKey，如果res为false，说明ipKey已经设置过，则
    // 判断发送次数加一并判断次数是否超过极值
    $res = $redis->set($ipKey, 1, array('nx', 'ex' => 864000));
    if (false === $res && $redis->incr($ipKey) > 100) {
        return false;
    }

    return true;
}
```

#### （2）购买商品

##### 需求说明：
- 100个商品被5个客户消费，
- 同个商品不能同时被多个客户购买

##### 思路：
- 对购买的商品进行加锁处理

##### 加锁需要考虑的问题
- 客户端获得锁之后崩溃，锁一直处于已被获取状态
> 对锁设置过期时间
- 持有锁的程序执行时间过长，导致锁被自动释放
> 不知道怎么做
- 客户端持有锁，其他客户端可能会擅自对锁进行修改
> 为做设置一个唯一值，释放锁的时候检测值是否相等

```php
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
```
ab模拟并发： ab -c 100 -n 5000 http://demo.cm/redis/business.php

#### （3）抢购

##### 需要考虑问题：
- 避免已抢购数量超过固有数量
- 一个用户只能抢购一次

```php
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
    $uid = rand(1, 200);
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
```