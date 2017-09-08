<?php
/**
 * 需求说明：
 * 1）排名前50
 * 2）随时间的流逝而不断减少分数
 * 3）文章一周之后不能在投票
 * 4）每个用户只能投一次表
 *
 * @author wuzhc2016@163.com
 * @since 2017-06-22
 */

define('VOTE_SCORE', 432);
define('ONE_WEEK_IN_SECONDS', 7 * 86400);
$redis = new Redis();
$redis->connect('127.0.0.1');

/**
 * 发布文章（自动生成）
 * hash article:id=>content
 * @param int $uid 文章作者uid
 */
function add_article($uid = 1)
{
    global $redis;
    $titles = [
        'this is a book',
        'nothing to do',
        'hello , redis',
        'can not access'
    ];
    $id = $redis->incr('article_id');
    $time = time();
    $vote = 0;
    $rs = $redis->hMset('article:'.$id, [
        'time'   => $time,
        'vote'   => $vote,
        'author' => 'user:' . $uid,
        'title'  => $titles[rand(0,count($titles)-1)],
    ]);
    if ($rs) {
        $redis->zAdd('time:', time(), 'article:'.$id);
    }
}

/**
 * 文章投票
 * zset member=>article:id , score=>vote_score
 * @param int $articleID 文章ID
 * @param int $uid 用户ID
 * @return bool
 */
function article_vote($articleID, $uid)
{
    global $redis;
    // 判断是否已经过期
    $createTime = $redis->hGet('article:'. $articleID, 'time');
    if (!$createTime) {
        return false;
    }
    $now = time();
    if ($now - ONE_WEEK_IN_SECONDS > $createTime) {
        // 已经过期，删除已投票用户集合
        $redis->del('voted:'.$articleID);
        return false;
    } else {
        // 未过期，将用户加入到已投票集合
        if ($redis->sAdd('voted:'.$articleID, $uid)) {
            // 增加文章投票分数
            $redis->zIncrBy('score:', VOTE_SCORE, 'article:'.$articleID);
            // 增加文章投票数
            $redis->hIncrBy('article:'.$articleID, 'vote', 1);
            return true;
        }
    }
    return false;
}

// 模拟发布1500遍文章
for ($i=0; $i<1500; $i++) {
    add_article();
}

// 模拟10000次对1500遍文章进行投票
for ($i=0; $i<10000; $i++) {
    article_vote(rand(1,1500), rand(1, 10000));
}
