#!/usr/bin/env php
<?php
//数据导出操作
$commonFile = dirname(dirname(__FILE__)) . '/common.php';
include($commonFile);

$initNum = !empty($argv[1]) ? $argv[1] : 0;

//最大合服数量 10   150/10*150 = 2250*3个洲 = 6750玩家 单区最大玩家数量
if ($initNum < 1 || $initNum > 10) {
	echo "init num need in [1, 10]\n";
	exit;
}


$s = microtime(true);

//设置拍卖行数据过期
$nowtime = time();
$expiretime = time() - T_App::ONE_HOUR;

$ret = B_DB::instance('Auction')->updateBy(array('auction_expired' => $expiretime), array('auction_expired' => array('>', $nowtime)));

//更新所有拍卖交易数据
M_Auction::updateAucInfoTimer();

//同步数据到内存
M_CacheToDB::runQueue();

M_Merge::exportData($initNum);
$e = microtime(true);
echo "cost time:" . ceil($e - $s) . "s\n";


?>