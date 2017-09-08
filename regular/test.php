<?php
$content = '
<p>
	<span style="font-family:宋体;font-size:16px;"><span style="font-size:14px;"><span style="font-family:SimSun;font-size:16px;"> 广州市游泳协会成立于</span><span style="font-family: SimSun; font-size: 16px;" font-size:16px;"="">1957</span><span style="font-family:SimSun;font-size:16px;">年，至今已历经半个多世纪，协会致力于游泳、跳水、水球、花样游泳、蹼泳项目的推广、普及和提高，目前会员近万人。</span></span></span>
</p>
<p>
	<span style="font-family:宋体;font-size:16px;"><span style="font-size:14px;"><span style="font-family:SimSun;font-size:16px;"> 协会自成立以来，积极组队参加国家、省举办的各类比赛和活动，连续组队参加了十八届全国成人游泳公开赛，一年一度的冬泳全国赛、澳门公开水域比赛，花样游泳、跳水、水球3个委员会组队参加一年一度的穗港澳比赛活动，取得了优异成绩。</span></span></span>
</p>
<p>
	<span style="font-family:宋体;font-size:16px;"><span style="font-size:14px;"><span style="font-family:SimSun;font-size:16px;"> 1997年，协会积极推动业余训练，从娃娃抓起，提出“让全市的小孩都学游泳”的口号，创办了广州市青少儿游泳系列赛，经过二十多年的发展，从原来只有几个市属体校参加，发展到现在近百的俱乐部（泳会）注册，从原来的一百多名运动员，到现在的四千多名运动员，规模越来越大，赛事组织越来越规范，竞技水平不断提升，成为培养运动后备人才的重要平台。</span></span></span>
</p>
<p>
	<span style="font-family:宋体;font-size:16px;"><span style="font-size:14px;"><span style="font-family:SimSun;font-size:16px;"> 多年来，向国家队培养和输送了<strong>杨景辉、陈艾森、张雁全、刘属、些旭峰、甄迎娟、郑仕斌、郑希、周嘉威、余贺新、刘湘、唐雨婷</strong>等一批优秀运动员，广州籍的运动员在奥运会、亚运会以及全运会等综合运动会上争金夺银，取得优异成绩，为国家和省市增光添彩。</span></span></span>
</p>
<p>
	<span style="font-family:宋体;font-size:16px;"><span style="font-size:14px;"><span style="font-family:SimSun;font-size:16px;"> 从2006年开始，连续十年协助广州市政府举办“爱我珠江亲水节”横渡珠江活动，各委员会积极主动参与和承担测试、选拔、招募、培训、救生等各项重要工作，为市体育局打造横渡珠江活动品牌做出了积极贡献。</span></span></span>
</p>
<p>
	<span style="font-family:宋体;font-size:16px;"><span style="font-size:14px;"><span style="font-family:SimSun;font-size:16px;"> 半个世纪以来，协会始终牢记使命，团结和组织全市从事游泳运动的工作者和爱好者，以及热心支持游泳项目的社会各界人士，促进全市游泳及相关项目的普及和提高，取得了极大的社<dds>会效益。</span></span></span>
</p>
<p>
	<br />
</p>
<p>
	<br />
</p>';
header("Content-Type:text/html;charset=utf-8");
preg_match_all('#<[^\/].*>(.*?)<\/.*?>#is', $content, $matches);

//var_dump($matches);

foreach ($matches[1] as $match) {
    echo preg_replace('#<.*?>#i','',$match);
}
