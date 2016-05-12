<?php

/**
 * 同步缓存到DB模块
 */
class M_CacheToDB {
	/**
	 * 添加寻将动作队列
	 * @author huwei
	 * @param string $key
	 * @return bool
	 */
	static public function addQueue($val) {
		$ret = false;
		if (!empty($val)) {
			$rc  = new B_Cache_RC(T_Key::CACHE_TO_DB_QUEUE);
			$ret = $rc->rpush($val);
			if (!$ret) {
				$msg = array(__METHOD__, 'Set Cache To DB Queue Fail', func_get_args());
				Logger::error($msg);
			}
		}
		return $ret;
	}

	static private function _getQueue() {
		$rc    = new B_Cache_RC(T_Key::CACHE_TO_DB_QUEUE);
		$queue = array();
		$i     = 0;
		while ($val = $rc->rpop()) {
			//统计代码
			if (isset($queue[$val])) {
				$queue[$val]++;
			} else {
				$queue[$val] = 0;
			}

			if ($i > 50000) {
				break;
			}
			$i++;
		}
		Logger::dev("CACHE to DB队列数据#" . count($queue) . ":" . json_encode($queue));
		return array($queue, $i);
	}

	static $funcPair = array(
		T_Key::CITY_BREAKOUT       => '_breakout',
		T_Key::CITY_INFO           => '_city',
		//T_Key::USER_INFO => '_user',
		T_Key::CITY_EXTRA_INFO     => '_extra',
		T_Key::CITY_COLONY_INFO    => '_city_colony',
		T_Key::CITY_TASK           => '_task',
		T_Key::CITY_HERO_INFO      => '_hero',
		T_Key::CITY_EQUIP_INFO     => '_equip',
		T_Key::CITY_WAR_MARCH_INFO => '_march',
		T_Key::UNION_INFO          => '_union',
		T_Key::CAMPAIGN_INFO       => '_camp',
		T_Key::WILD_MAP_INFO       => '_map',
		T_Key::UNION_MEMBER_INFO   => '_union_member',
		T_Key::CITY_COLLEGE        => '_college',
		T_Key::CITY_LOTTERY_INFO   => '_lottery',
		T_Key::CITY_MULTI_FB_INFO  => '_multifb',

	);

	/**
	 * 处理寻将动作队列
	 * @author huwei
	 * @param string $type [add/del]
	 * @return bool
	 */
	static public function runQueue() {
		list($queue, $total) = self::_getQueue();
		$stats    = array();
		$err      = array();
		$now      = time();
		$n        = 1;
		$splitNum = ceil($total / (M_Deamon::CACHE_TO_DB_TIME - 30));
		foreach ($queue as $val => $num) {
			if ($n % $splitNum == 0) {
				usleep(0.5 * 1000000);
			}
			$n++;

			list($valKey, $id) = explode(':', $val);
			if (!isset($stats[$valKey])) {
				$stats[$valKey] = 0;
			}
			$stats[$valKey]++;

			if (isset(self::$funcPair[$valKey])) {
				$funcName = self::$funcPair[$valKey];
				self::$funcName($err, $id, $now);
			}
		}

		if (!empty($err)) {
			$msg = array(__METHOD__, 'Set Cache To DB Fail', $err);
			Logger::error($msg);
		}

		return $stats;
	}


	static private function _lottery(&$err, $id, $now) {
		$rc          = new B_Cache_RC(T_Key::CITY_LOTTERY_INFO, $id);
		$lotteryData = $rc->hgetall();
		if (!empty($lotteryData['city_id'])) {
			$lotteryData['sys_sync_time'] = $now;
			$ret                          = B_DB::instance('CityLottery')->update($lotteryData, $lotteryData['city_id']);
			$msg                          = 'ok';
			if (!$ret) {
				$msg              = json_encode($lotteryData);
				$err['lottery'][] = $lotteryData['city_id'];
			}
			Logger::dev("lottery[{$id}]#{$msg}");
		}
	}

	static private function _college(&$err, $id, $now) {
		$rc          = new B_Cache_RC(T_Key::CITY_COLLEGE, $id);
		$collegeData = $rc->hgetall();
		if (!empty($collegeData['city_id'])) {
			$collegeData['sys_sync_time'] = $now;
			$ret                          = B_DB::instance('CityCollege')->update($collegeData, $collegeData['city_id']);
			$msg                          = 'ok';
			if (!$ret) {
				$msg              = json_encode($collegeData);
				$err['college'][] = $collegeData['city_id'];
			}
			Logger::dev("college[{$id}]#{$msg}");
		}
	}

	static private function _union_member(&$err, $id, $now) {
		$rc              = new B_Cache_RC(T_Key::UNION_MEMBER_INFO, $id);
		$unionMemberData = $rc->hgetall();
		if (!empty($unionMemberData['id'])) {
			$unionMemberData['sys_sync_time'] = $now;
			$ret                              = B_DB::instance('AllianceMember')->update($unionMemberData, $unionMemberData['id']);
			$msg                              = 'ok';
			if (!$ret) {
				$msg                   = json_encode($unionMemberData);
				$err['union_member'][] = $unionMemberData['id'];
			}
			Logger::dev("union_member[{$id}]#{$msg}");
		}
	}

	static private function _map(&$err, $id, $now) {
		$rc          = new B_Cache_RC(T_Key::WILD_MAP_INFO, $id);
		$wildMapData = $rc->hgetall();
		if (!empty($wildMapData['pos_no'])) {
			$wildMapData['sys_sync_time'] = $now;
			$ret                          = B_DB::instance('WildMap')->update($wildMapData, $wildMapData['pos_no']);
			Logger::dev("map{$id}#" . json_encode($wildMapData) . "#" . json_encode($ret));
			$msg = 'ok';
			if (!$ret) {
				$msg          = json_encode($wildMapData);
				$err['map'][] = $wildMapData['pos_no'];
			}
			Logger::dev("map[{$id}]#{$msg}");
		}
	}

	static private function _camp(&$err, $id, $now) {
		$rc       = new B_Cache_RC(T_Key::CAMPAIGN_INFO, $id);
		$campData = $rc->hgetall();
		if (!empty($campData['id'])) {
			$campData['sys_sync_time'] = $now;
			$ret                       = B_DB::instance('Campaign')->updateInfo($campData['id'], $campData);
			$msg                       = 'ok';
			if (!$ret) {
				$msg           = json_encode($campData);
				$err['camp'][] = $campData['id'];
			}
			Logger::dev("camp[{$id}]#{$msg}");
		}
	}

	static private function _union(&$err, $id, $now) {
		$rc        = new B_Cache_RC(T_Key::UNION_INFO, $id);
		$unionData = $rc->hgetall();
		if (!empty($unionData['id'])) {
			$unionData['sys_sync_time'] = $now;
			$ret                        = B_DB::instance('Alliance')->update($unionData, $unionData['id']);
			$msg                        = 'ok';
			if (!$ret) {
				$msg            = json_encode($unionData);
				$err['union'][] = $unionData['id'];
			}
			Logger::dev("union[{$id}]#{$msg}");
		}
	}

	static private function _march(&$err, $id, $now) {
		$rc        = new B_Cache_RC(T_Key::CITY_WAR_MARCH_INFO, $id);
		$marchData = $rc->hgetall();
		if (!empty($marchData['id'])) {
			$marchData['sys_sync_time'] = $now;
			$ret                        = B_DB::instance('WarMarch')->update($marchData, $marchData['id']);
			$msg                        = 'ok';
			if (!$ret) {
				$msg            = json_encode($marchData);
				$err['march'][] = $marchData['id'];
			}
			Logger::dev("march[{$id}]#{$msg}");
		}
	}

	static private function _equip(&$err, $id, $now) {
		$rc        = new B_Cache_RC(T_Key::CITY_EQUIP_INFO, $id);
		$equipData = $rc->hgetall();
		if (!empty($equipData['id'])) {
			$equipData['sys_sync_time'] = $now;
			$ret                        = B_DB::instance('CityEquip')->update($equipData, $equipData['id']);
			$msg                        = 'ok';
			if (!$ret) {
				$msg            = json_encode($equipData);
				$err['equip'][] = $equipData['id'];
			}
			Logger::dev("equip[{$id}]#{$msg}");
		}
	}

	static private function _hero(&$err, $id, $now) {
		$rc       = new B_Cache_RC(T_Key::CITY_HERO_INFO, $id);
		$heroData = $rc->hgetall();
		if (!empty($heroData['id'])) {
			$heroData['sys_sync_time'] = $now;
			$ret                       = B_DB::instance('CityHero')->update($heroData, $heroData['id']);
			$msg                       = 'ok';
			if (!$ret) {
				$msg           = json_encode($heroData);
				$err['hero'][] = $heroData['id'];
			}
			Logger::dev("hero[{$id}]#{$msg}");
		}
	}


	static private function _task(&$err, $id, $now) {
		//更新城市任务
		$rc       = new B_Cache_RC(T_Key::CITY_TASK, $id);
		$taskData = $rc->hgetall();
		if (!empty($taskData['city_id'])) {
			$taskData['sys_sync_time'] = $now;
			$ret                       = B_DB::instance('CityTask')->update($taskData, $taskData['city_id']);
			$msg                       = 'ok';
			if (!$ret) {
				$msg           = json_encode($taskData);
				$err['task'][] = $taskData['city_id'];
			}
			Logger::dev("task[{$id}]#{$msg}");
		}
	}


	static private function _extra(&$err, $id, $now) {
		$rc = new B_Cache_RC(T_Key::CITY_EXTRA_INFO, $id);
		//更新城市扩展信息
		$extraData = $rc->hgetall();
		if (!empty($extraData['city_id'])) {
			$extraData['sys_sync_time'] = $now;
			$ret                        = B_DB::instance('CityExtra')->update($extraData, $extraData['city_id']);
			$msg                        = 'ok';
			if (!$ret) {
				$msg            = json_encode($extraData);
				$err['extra'][] = $extraData['city_id'];
			}
			Logger::dev("extra[{$id}]#{$msg}");
		}

	}

	static private function _city_colony(&$err, $id, $now) {
		$rc = new B_Cache_RC(T_Key::CITY_COLONY_INFO, $id);
		//更新城市扩展信息
		$colonyData = $rc->hgetall();
		if (!empty($colonyData['city_id'])) {
			foreach ($colonyData as $k => $v) {
				in_array($k, T_DBField::$cityCityColonyFields) && $arr[$k] = $v;
			}
			$arr['sys_sync_time'] = $now;
			$ret                  = B_DB::instance('CityColony')->update($arr, $arr['city_id']);
			$msg                  = 'ok';
			if (!$ret) {
				$msg             = json_encode($arr);
				$err['colony'][] = $arr['city_id'];
			}
			Logger::dev("colony[{$id}]#{$msg}");
		}

	}

	static private function _user(&$err, $id, $now) {
		$rc       = new B_Cache_RC(T_Key::USER_INFO, $id);
		$userData = $rc->hgetall();
		if (!empty($userData['id'])) {
			unset($userData['last_sync_time']);
			$userData['sys_sync_time'] = $now;
			$ret                       = B_DB::instance('User')->update($userData, $userData['id']);
			$msg                       = 'ok';
			if (!$ret) {
				$msg           = json_encode($userData);
				$err['user'][] = $userData['id'];
			}
			Logger::dev("user[{$id}]#{$msg}");
		}
	}

	static private function _city(&$err, $id, $now) {
		$rc       = new B_Cache_RC(T_Key::CITY_INFO, $id);
		$cityData = $rc->hgetall();
		if (!empty($cityData['id'])) {
			unset($cityData['last_sync_time']);
			$cityData['sys_sync_time'] = $now;
			$ret                       = B_DB::instance('City')->update($cityData, $cityData['id']);
			$msg                       = 'ok';
			if (!$ret) {
				$msg           = json_encode($cityData);
				$err['city'][] = $cityData['id'];
			}
			Logger::dev("city[{$id}]#{$msg}");
		}
	}

	/** 突围 */
	static private function _breakout(&$err, $id, $now) {
		$rc       = new B_Cache_RC(T_Key::CITY_BREAKOUT, $id);
		$cityData = $rc->hgetall();
		if (!empty($cityData['city_id'])) {
			unset($cityData['last_sync_time']);
			$cityData['sys_sync_time'] = $now;
			$ret                       = B_DB::instance('CityBreakout')->update($cityData, $cityData['city_id']);
			$msg                       = 'ok';
			if (!$ret) {
				$msg           = json_encode($cityData);
				$err['city'][] = $cityData['city_id'];
			}
			Logger::dev("city[{$id}]#{$msg}");
		}
	}

	/** 多人副本 */
	static private function _multifb(&$err, $id, $now) {
		$rc       = new B_Cache_RC(T_Key::CITY_MULTI_FB_INFO, $id);
		$cityData = $rc->hgetall();
		if (!empty($cityData['city_id'])) {
			unset($cityData['last_sync_time']);
			$cityData['sys_sync_time'] = $now;
			$ret                       = B_DB::instance('CityMultiFB')->update($cityData, $cityData['city_id']);
			$msg                       = 'ok';
			if (!$ret) {
				$msg           = json_encode($cityData);
				$err['city'][] = $cityData['city_id'];
			}
			Logger::dev("city[{$id}]#{$msg}");
		}
	}
}

?>