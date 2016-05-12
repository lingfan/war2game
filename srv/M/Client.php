<?php

/**
 * 在线用户模块
 */
class M_Client {
	/** 在线超时时间(秒) */
	const Timeout = 600;

	/** 统计超时时间(秒) */
	const OnlieTimeout = 120;

	/** 访问队列循环时间(秒) */
	const VISIT_LOOP_DELAY_TIME = 10;

	/**
	 * 获取在线用户信息
	 * @author huwei
	 * @param int $userId
	 * @return array
	 */
	static public function get($cityId) {
		$ret = false;
		if (!empty($cityId)) {
			$rc  = new B_Cache_RC(T_Key::ONLINE_USER, $cityId);
			$ret = $rc->hgetall();
		}
		return $ret;
	}

	/**
	 * 添加在线用户
	 * @author huwei
	 * @param int $userId
	 * @param int $cityId
	 * @param string $sessId
	 * @param string $ip
	 * @return bool
	 */
	static public function add($consumerId, $serverId, $cityId, $sessId) {
		$ret      = false;
		$infoData = array(
			'sess_id'         => $sessId,
			'city_id'         => $cityId,
			'server_id'       => $serverId,
			'consumer_id'     => $consumerId,
			'ip_addr'         => B_Utils::getIp(),
			'last_visit_time' => time(),
		);
		$rc       = new B_Cache_RC(T_Key::ONLINE_USER, $cityId);
		$rc1      = new B_Cache_RC(T_Key::ONLINE_USER_LIST);

		$bSet = $rc->hmset($infoData, M_Client::Timeout);
		if ($bSet) {
			$ret = $rc1->sadd($cityId);
		}
		return $ret;
	}

	/**
	 * 删除在线用户
	 * @author huwei
	 * @param int $userId
	 * @return bool
	 */
	static public function del($cityId) {
		$ret = false;
		if (!empty($cityId)) {
			$rc  = new B_Cache_RC(T_Key::ONLINE_USER, $cityId);
			$rc1 = new B_Cache_RC(T_Key::ONLINE_USER_LIST);

			$ret = $rc1->srem($cityId);
			$ret && $rc->delete();
		}
		return $ret;
	}

	static public function isOnline($cityId) {
		$rc = new B_Cache_RC(T_Key::ONLINE_USER_LIST);
		return $rc->sismember($cityId);
	}

	/**
	 * 获取在线用户id列表
	 * @author huwei
	 * @param int $userId
	 * @return array
	 */
	static public function keepliveList() {
		$rc = new B_Cache_RC(T_Key::ONLINE_USER_LIST);
		return $rc->smembers();
	}

	static public function keepLiveNum() {
		$rc = new B_Cache_RC(T_Key::ONLINE_USER_LIST);
		return $rc->scard();
	}

	static public function getList() {
		$rc = new B_Cache_RC(T_Key::STATS_ONLINE_USER_LIST);
		return $rc->jsonget();
	}

	static public function setList($list) {
		M_Client::setOnlineNum(count($list));
		$rc = new B_Cache_RC(T_Key::STATS_ONLINE_USER_LIST);
		return $rc->jsonset($list);
	}

	/**
	 * 获取在线用户数量
	 * @author huwei
	 * @return int
	 */
	static public function getTotal() {
		$ids = M_Client::getList();
		$num = count($ids);
		return $num;
	}

	static public function setOnlineNum($num) {
		$rc = new B_Cache_RC(T_Key::ONLINE_NUM);
		return $rc->set($num);
	}

	static public function getOnlineNum() {
		$rc = new B_Cache_RC(T_Key::ONLINE_NUM);
		return $rc->get();
	}

	/**
	 * 心跳检测
	 * @author huwei
	 * @return array
	 */
	static public function keeplive() {
		$now    = time();
		$list   = self::keepliveList();
		$online = array();
		foreach ($list as $cityId) {
			$isOnlie = false;
			$rc      = new B_Cache_RC(T_Key::ONLINE_USER, $cityId);
			if ($rc->exists()) { //存在信息key
				$lastVistTime = $rc->hget('last_visit_time');
				$diffTimeout  = $now - $lastVistTime;
				if (!empty($lastVistTime) && $diffTimeout < M_Client::Timeout) { //在线
					//echo "Timeout#{$userId}#{$diff}\n";
					$isOnlie = true;
					$rc->expire(M_Client::Timeout);
					$online[$cityId] = $diffTimeout;
				}
			}

			if (!$isOnlie) {
				//同步离线用户缓存 到 数据库
				M_Client::del($cityId);
			}
		}

		M_Client::setList(array_keys($online));

		return $online;
	}

	/**
	 * 更新最后访问时间
	 * @author huwei
	 * @param int $userId
	 * @return bool
	 */
	static public function upVisitTime($cityId) {
		$ret = false;
		if ($cityId > 0) {
			$rc      = new B_Cache_RC(T_Key::ONLINE_USER, $cityId);
			$upField = array('last_visit_time' => time());
			$ret     = $rc->hmset($upField, T_App::ONE_DAY);
		}
		return $ret;
	}

	/**
	 * 是否间隔时间更新
	 * @author huwei
	 * @param int $cityId
	 * @return boolean
	 */
	static public function checkVisitDiffTime($cityId) {
		$ret = false;
		$now = time();
		$rc  = new B_Cache_RC(T_Key::CITY_VISIT_DIFF, $cityId);
		$t   = $rc->get();
		if ($t < $now + 1) {
			$rc->set($now, T_App::ONE_DAY);
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 添加到访问队列
	 * @author huwei
	 * @param int $cityId
	 * @return boolean
	 */
	static public function addCityVisitQueue($cityId) {
		$ret = false;
		if (!empty($cityId)) {
			$bUp = M_Client::checkVisitDiffTime($cityId);
			if ($bUp) {
				$rc  = new B_Cache_RC(T_Key::CITY_VISIT_QUEUE);
				$ret = $rc->rpush($cityId);
				if (!$ret) {
					$msg = array(__METHOD__, 'Set CITY VISIT QUEUE Fail', func_get_args());
					Logger::error($msg);
				}
			}
		}
		return $ret;
	}

	/**
	 * 获取访问队列 去掉冗余
	 * @author huwei
	 * @return array
	 */
	static private function _getCityVisitQueue() {
		$queue = array();
		$i     = 0;
		$rc    = new B_Cache_RC(T_Key::CITY_VISIT_QUEUE);

		while ($val = $rc->rpop()) {
			//统计代码
			if (isset($queue[$val])) {
				$queue[$val]++;
			} else {
				$queue[$val] = 0;
			}

			/**
			 * if ($i > 50000)
			 * {
			 * break;
			 * }
			 **/
			$i++;
		}
		//Logger::dev(__METHOD__."队列数据#".count($queue).":".json_encode($queue));
		return array($queue, $i);
	}

	/**
	 * 运行到访问队列
	 * @author huwei
	 * @return void
	 */
	static public function runCityVisitQueue() {
		list($queue, $total) = self::_getCityVisitQueue();
		$err = array();
		$now = time();
		$n   = 1;
		//计算资源
		foreach ($queue as $cityId => $num) {
			$objPlayer = new O_Player($cityId);
			//更新最后访问水花四溅
			M_Client::upVisitTime($cityId);
			//恢复活力军令
			$objPlayer->City()->relifeEnergyOrder();

			//更新在线奖励时间 防沉迷相关
			$objPlayer->City()->upLastVisit();

			//更新在线资源
			$objPlayer->Res()->upGrow();
			$objPlayer->Res()->calc();

			$objPlayer->save();
			$n++;
		}
		return $queue;
	}


	static public function todayOnline() {
		$num = M_Client::keepLiveNum();
		$rc  = new B_Cache_RC(T_Key::STATS_ONLINE_USER_NUM, date('Ymd'));
		$val = date('Hi') . '|' . $num;
		$ret = $rc->sadd($val);
		//$rc->sMembers($key); //取值
		return $ret;
	}

	static public function yestodayOnline() {
		$id   = 0;
		$date = date('Ymd', time() - 86400);
		$rc   = new B_Cache_RC(T_Key::STATS_ONLINE_USER_NUM, $date);
		$data = $rc->smembers();
		if (!empty($data)) {
			$maxNum = 0;
			foreach ($data as $val) {
				$tmp = explode('|', $val);
				if ($tmp[1] > $maxNum) {
					$maxNum = $tmp[1];
				}
			}

			$serverId = B_Cache_File::server(SERVER_NO);
			$txt      = json_encode($data);
			$setArr   = array(
				'day'        => $date,
				'txt'        => $txt,
				'max_people' => $maxNum,
				'server_id'  => $serverId,
			);
			$id       = B_DBStats::insert('stats_online_people', $setArr);
		}
		return $id;
	}

	static public function activeUserNum() {
		$rc   = new B_Cache_RC(T_Key::CITY_ACTIVE_NUM, date('Ymd', time() - 86400));
		$data = $rc->hgetall();
		$date = $rc->get_key();
		$id   = 0;
		if (!empty($data) && !empty($date)) {
			$date = str_replace(array(T_Key::CITY_ACTIVE_NUM, ':'), array('', ''), $date);
			$num  = count($data);

			$serverId = B_Cache_File::server(SERVER_NO);
			$txt      = json_encode($data);
			$setArr   = array(
				'day'       => $date,
				'city_num'  => $num,
				'city_list' => base64_encode(gzcompress($txt)),
				'server_id' => $serverId,
			);
			$id       = B_DBStats::insert('stats_active_num', $setArr);
		}
		return $id;
	}
}

?>