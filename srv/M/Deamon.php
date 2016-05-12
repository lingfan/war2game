<?php

/**
 * 后台守护进程模块
 */
class M_Deamon {
	const LOOP_DELAY_TIME  = 10;
	const CACHE_TO_DB_TIME = 120;

	static public function cronPerMin() {
		$now = time();

		//每分钟执行
		$msg = self::_min();
		Logger::cron('min', json_encode($msg));
		//定时执行 同步内存 => 数据库
		$sync_db_key = M_Deamon::CACHE_TO_DB_TIME . 's';
		$rc_db       = new B_Cache_RC(T_Key::CRON_EXPIRE_TIME, $sync_db_key);
		$expireTime  = intval($rc_db->get());
		if ($expireTime < $now) {
			$rc_db->set($now + M_Deamon::CACHE_TO_DB_TIME);

			$start_db = microtime(true);
			$stats    = M_CacheToDB::runQueue();
			$end_db   = microtime(true);
			$costTime = $end_db - $start_db;
			$dbmsg    = "[M_CacheToDB::runQueue]#CostTime: {$costTime} [" . json_encode($stats) . "]\n\n";

			Logger::cron($sync_db_key, $dbmsg);
		}
		//每天执行
		$rc_day     = new B_Cache_RC(T_Key::CRON_EXPIRE_TIME, 'DAY');
		$expireTime = intval($rc_day->get());
		if ($expireTime < $now) {
			//每天零点10分
			$rc_day->set(mktime(0, 10, 0, date('m'), date('d') + 1, date('Y')));

			$daymsg = self::_day();
			Logger::cron('day', json_encode($daymsg));
		}

		//每周执行
		if (ETC_NO == 'tw') {
			$rc_week    = new B_Cache_RC(T_Key::CRON_EXPIRE_TIME, 'WEEK');
			$expireTime = intval($rc_week->get()); //时间为下周日的时间

			if ($expireTime <= $now) {
				//每周日24点00分
				$day   = 0;
				$month = 0;
				$year  = 0;
				$week  = 8;
				$week  = date('w', $now);
				if ($week == 1) {
					$day   = date('d');
					$month = date('m');
					$year  = date('Y');
					$rc_week->set(mktime(0, 0, 0, $month, $day + 7, $year));
					$weekmsg  = array();
					$start_db = microtime(true);
					M_Ranking::runFBPass();
					$end_db    = microtime(true);
					$costTime  = $end_db - $start_db;
					$weekmsg[] = "[M_Ranking::runFBPass]#CostTime: {$costTime} \n\n";
					Logger::cron('week', json_encode($weekmsg));
				}
			}
		}
	}

	static private function _min() {
		$start = microtime(true);

		$data                                 = M_Auction::updateAucInfoTimer();
		$end1                                 = microtime(true);
		$diff                                 = sprintf('%.3f', $end1 - $start);
		$msg['M_Auction::updateAucInfoTimer'] = $diff;

		$data                         = M_Client::todayOnline();
		$end2                         = microtime(true);
		$diff                         = sprintf('%.3f', $end2 - $end1);
		$msg['M_Client::todayOnline'] = $diff;

		$data                 = M_Rmon::redis();
		$end3                 = microtime(true);
		$diff                 = sprintf('%.3f', $end3 - $end2);
		$msg['M_Rmon::redis'] = $diff;

		$data                             = M_Horse::updateHorseTimer();
		$end4                             = microtime(true);
		$diff                             = sprintf('%.3f', $end4 - $end3);
		$msg['M_Horse::updateHorseTimer'] = $diff;

		//战役循环脚本
		M_Campaign::run();
		$end5                   = microtime(true);
		$diff                   = sprintf('%.3f', $end5 - $end4);
		$msg['M_Campaign::run'] = $diff;

		//临时NPC脚本
		M_NPC::refreshRandTempNpc();
		$end6                  = microtime(true);
		$diff                  = sprintf('%.3f', $end6 - $end5);
		$msg['M_NPC::refresh'] = $diff;

		//临时NPC脚本
		M_NPC::refreshFixedTempNpc();
		$end7                         = microtime(true);
		$diff                         = sprintf('%.3f', $end7 - $end6);
		$msg['M_NPC::refreshFascist'] = $diff;

		M_Cron::syncPayLog();
		$end8                      = microtime(true);
		$diff                      = sprintf('%.3f', $end8 - $end7);
		$msg['M_Cron::syncPayLog'] = $diff;

		return $msg;
	}

	static private function _day() {
		$start                              = microtime(true);
		$data                               = M_Client::yestodayOnline();
		$end1                               = microtime(true);
		$diff                               = sprintf('%.3f', $end1 - $start);
		$daymsg['M_Client::yestodayOnline'] = $diff;

		$data                              = M_Client::activeUserNum();
		$end2                              = microtime(true);
		$diff                              = sprintf('%.3f', $end2 - $end1);
		$daymsg['M_Client::ActiveUserNum'] = $diff;

		$data                            = M_Cron::syncCityLevel();
		$end3                            = microtime(true);
		$diff                            = sprintf('%.3f', $end3 - $end2);
		$daymsg['M_Cron::syncCityLevel'] = $diff;

		$data                                  = M_City::removeNewProtection();
		$end4                                  = microtime(true);
		$diff                                  = sprintf('%.3f', $end4 - $end3);
		$daymsg['M_City::removeNewProtection'] = $diff;


		M_Cron::cleanReportLog();
		$end5                             = microtime(true);
		$diff                             = sprintf('%.3f', $end5 - $end4);
		$daymsg['M_Cron::cleanReportLog'] = $diff;

		return $daymsg;
	}

	static public function cityVisit() {
		$msg = array();
		$now = microtime(true);
		//城市数据更新
		$list = M_Client::runCityVisitQueue();
		//echo date('H:i:s')."#".json_encode($list)."\n";
		$end                                = microtime(true);
		$costTime                           = $end - $now;
		$msg['M_Client::runCityVisitQueue'] = sprintf('%.3f', $costTime);

		//公告发送
		M_Chat::runSendSysNotice();
		$end1                            = microtime(true);
		$costTime                        = $end1 - $end;
		$msg['M_Chat::runSendSysNotice'] = sprintf('%.3f', $costTime);

		//用户心跳守护进程
		$list                      = M_Client::keeplive();
		$end2                      = microtime(true);
		$costTime                  = $end2 - $end1;
		$msg['M_Client::keeplive'] = sprintf('%.3f', $costTime);

		//运行清理占领野地过期数据进程
		M_March_Hold::run();
		$end3                 = microtime(true);
		$costTime             = $end3 - $end2;
		$msg['M_Client::run'] = sprintf('%.3f', $costTime);

		//被占领城市资源放到临时仓库
		M_ColonyCity::setWareHouse();
		$end4                              = microtime(true);
		$costTime                          = $end4 - $end3;
		$msg['M_ColonyCity::setWareHouse'] = sprintf('%.3f', $costTime);

		return $msg;
	}
}