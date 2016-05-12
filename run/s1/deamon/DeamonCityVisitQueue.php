#!/usr/bin/env php
<?php
//心跳检测用户在线
$commonFile = dirname(dirname(__FILE__)) . '/common.php';
include($commonFile);
declare(ticks = 1);
B_Utils::keepOnePid(__FILE__, getmypid());
while (1) {
	$msg = M_Deamon::cityVisit();
	//Logger::cron('cityvist', json_encode($msg));
	sleep(M_Client::VISIT_LOOP_DELAY_TIME);
}