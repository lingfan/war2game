<?php

/**
 * 排行模块
 */
class M_Ranking {

	/** 查看排行榜最少需要的军功 */
	static $getRankMinMilMedal = 1000;

	/** 排行最大查询记录数 */
	CONST HERO_TOTAL_LIMIT = 100;
	/** 排行最大查询记录数 */
	CONST CITY_TOTAL_LIMIT = 200;

	/** 繁体版几天未登录则不显示在排行榜 */
	CONST DAY_NO_LOGIN = 7; //7天

	static $pageSize = 9;

	/** 威望排行 */
	const RANKINGS_RENOWN = 1;
	/** 军功排行 */
	const RANKINGS_MILMEDAL = 2;
	/** 联盟排行 */
	const RANKINGS_UNION = 3;
	/** 军官等级排行 */
	const RANKINGS_HERO_LEVEL = 4;
	/** 军官统帅排行 */
	const RANKINGS_HERO_LEAD = 5;
	/** 军官指挥排行 */
	const RANKINGS_HERO_COMMAND = 6;
	/** 军官军事排行 */
	const RANKINGS_HERO_MILITARY = 7;
	/** 军官胜利排行 */
	const RANKINGS_HERO_WIN = 8;
	/** 战级值排行 */
	const RANKINGS_RECORD = 9;

	static $funcArr = array(
		self::RANKINGS_RENOWN        => 'getRenown',
		self::RANKINGS_MILMEDAL      => 'getMilMedal',
		self::RANKINGS_UNION         => 'getUnion',
		self::RANKINGS_HERO_LEVEL    => 'getHeroLevel',
		self::RANKINGS_HERO_LEAD     => 'getHeroLead',
		self::RANKINGS_HERO_COMMAND  => 'getHeroCommand',
		self::RANKINGS_HERO_MILITARY => 'getHeroMilitary',
		self::RANKINGS_HERO_WIN      => 'getHeroWin',
		self::RANKINGS_RECORD        => 'getRecord',
	);

	static public function getSyncTime($name) {
		$ret = false;
		if (isset(self::$funcArr[$name])) {
			$rc  = new B_Cache_RC(T_Key::RANKINGS_SYNC_TIME);
			$t   = $rc->hget('Rank' . $name);
			$ret = !empty($t) ? $t : 0;
		}
		return $ret;
	}

	static public function setSyncTime($name, $time) {
		$ret = false;
		if (isset(self::$funcArr[$name])) {
			$rc  = new B_Cache_RC(T_Key::RANKINGS_SYNC_TIME);
			$up  = array("Rank{$name}");
			$ret = $rc->hmset($up);
		}
		return $ret;
	}

	/**
	 * 获取威望排行榜
	 * @author HeJunyun
	 * @return array $data
	 */
	static public function getRenown($page = 1) {
		$mcTimeKey = self::RANKINGS_RENOWN;
		$now       = time();
		$syncTime  = M_Ranking::getSyncTime($mcTimeKey);
		$needSync  = false;

		$rc = new B_Cache_RC(T_Key::RANKINGS_RENOWN);
		if (!$rc->exists()) {
			$needSync = true;
		} else if (empty($syncTime)) {
			$needSync = true;
		} else if (!empty($syncTime) && $now > $syncTime) {
			$needSync = true;
		}

		if ($needSync) {
			$list = M_Cron::syncRankingsByRenown();
			M_Ranking::setSyncTime($mcTimeKey, $now + T_App::ONE_MINUTE * 5);
		} else {
			$list = $rc->jsonget();
		}

		$sumRow    = count($list);
		$totalPage = ceil($sumRow / self::$pageSize);

		$page   = min($totalPage, $page);
		$offset = ($page - 1) * self::$pageSize;

		$data['page']    = $page;
		$data['sumPage'] = $totalPage;
		$data['sumRow']  = $sumRow;
		$data['list']    = array_slice($list, $offset, self::$pageSize);

		return $data;
	}

	/**
	 * 根据用户ID获取用户威望排名
	 * @author Hejunyun
	 * @param int $id
	 * @return array/false
	 */
	static public function getRenownRankingsByCityId($id) {
		$rc   = new B_Cache_RC(T_Key::RANKINGS_RENOWN);
		$data = $rc->jsonget();
		if (isset($data[$id])) {
			return $data[$id]['RANK'];
		} else {
			return false;
		}
	}

	/**
	 * 获取军功排行榜
	 * @author HeJunyun
	 * @return array $data
	 */
	static public function getMilMedal($page = 1) {
		$mcTimeKey = self::RANKINGS_RENOWN;
		$now       = time();
		$syncTime  = M_Ranking::getSyncTime($mcTimeKey);
		$needSync  = false;
		$rc        = new B_Cache_RC(T_Key::RANKINGS_MILMEDAL);
		if (!$rc->exists()) {
			$needSync = true;
		} else if (empty($syncTime)) {
			$needSync = true;
		} else if (!empty($syncTime) && $now > $syncTime) {
			$needSync = true;
		}

		if ($needSync) {
			$list = M_Cron::syncRankingsByMilMedal();
			M_Ranking::setSyncTime($mcTimeKey, $now + T_App::ONE_MINUTE * 5);
		} else {
			$list = $rc->jsonget();
		}

		$sumRow    = count($list);
		$totalPage = ceil($sumRow / self::$pageSize);

		$page   = min($totalPage, $page);
		$offset = ($page - 1) * self::$pageSize;

		$data['page']    = $page;
		$data['sumPage'] = $totalPage;
		$data['sumRow']  = $sumRow;
		$data['list']    = array_slice($list, $offset, self::$pageSize);

		return $data;
	}

	/**
	 * 根据用户ID获取用户军功排名
	 * @author Hejunyun
	 * @param int $id
	 * @return array/false
	 */
	static public function getMilmedalRankingsByCityId($id) {
		$rc   = new B_Cache_RC(T_Key::RANKINGS_MILMEDAL);
		$data = $rc->jsonget();
		if (isset($data[$id])) {
			return $data[$id]['RANK'];
		} else {
			return false;
		}
	}

	/**
	 * 获取联盟排行榜
	 * @author HeJunyun
	 * @return array $data
	 */
	static public function getUnion($cityId, $page = 1) {
		$applyList = M_Union::getUserAppList($cityId);
		$applyList = $applyList ? $applyList : array();
		$ret       = M_Union::getList($page);
		foreach ($ret['list'] as $id) {
			$info           = M_Union::getInfo($id);
			$data['list'][] = array(
				'ID'             => $info['id'],
				'FaceId'         => $info['face_id'],
				'Name'           => $info['name'],
				'Level'          => $info['level'],
				'Rank'           => $info['rank'],
				'Boss'           => $info['boss'],
				'CreateNickName' => $info['create_nick_name'],
				'TotalPerson'    => $info['total_person'],
				'TotalRenown'    => $info['total_renown'],
				'Notice'         => $info['notice'],
				'CreateAt'       => $info['create_at'],
				'IsApply'        => in_array($info['id'], $applyList) ? 1 : 0,
			);
		}
		$data['sumRow']  = $ret['total'];
		$data['page']    = $ret['page'];
		$data['sumPage'] = $ret['sumPage'];
		return $data;
	}

	/**
	 * 根据联盟ID获取联盟排名
	 * @author Hejunyun
	 * @param int $id 联盟ID
	 * @return int(排名)/bool
	 */
	static public function getUnionRankingsByUnionId($id) {
		$info = M_Union::getInfo($id);
		return $info['rank'];
	}

	/**
	 * 获取军官等级排行榜
	 * @author HeJunyun
	 * @return array $data
	 */
	static public function getHeroLevel($page = 1) {
		$now      = time();
		$syncTime = M_Ranking::getSyncTime(self::RANKINGS_HERO_LEVEL);
		$needSync = false;

		$rc = new B_Cache_RC(T_Key::RANKINGS_HERO_LEVEL);
		if (!$rc->exists()) {
			$needSync = true;
		} else if (empty($syncTime)) {
			$needSync = true;
		} else if (!empty($syncTime) && $now - $syncTime > T_App::ONE_HOUR) {
			$needSync = true;
		}

		if ($needSync) {
			$list = M_Cron::syncRankingsByHero();
			M_Ranking::setSyncTime(self::RANKINGS_HERO_LEVEL, $now);
		} else {
			$list = $rc->jsonget();
		}

		$sumRow    = count($list);
		$totalPage = ceil($sumRow / self::$pageSize);

		$page   = min($totalPage, $page);
		$offset = ($page - 1) * self::$pageSize;

		$data['sumRow']  = $sumRow;
		$data['page']    = $page;
		$data['sumPage'] = $totalPage;
		$data['list']    = array_slice($list, $offset, self::$pageSize);

		return $data;
	}

	/**
	 * 获取军官统帅排行榜
	 * @author HeJunyun
	 * @return array $data
	 */
	static public function getHeroLead($page = 1) {
		$now      = time();
		$syncTime = M_Ranking::getSyncTime(self::RANKINGS_HERO_LEAD);
		$needSync = false;

		$rc = new B_Cache_RC(T_Key::RANKINGS_HERO_LEAD);
		if (!$rc->exists()) {
			$needSync = true;
		} else if (empty($syncTime)) {
			$needSync = true;
		} else if (!empty($syncTime) && $now - $syncTime > T_App::ONE_HOUR) {
			$needSync = true;
		}

		if ($needSync) {
			$list = M_Cron::syncRankingsByHeroLead();
			M_Ranking::setSyncTime(self::RANKINGS_HERO_LEAD, $now);
		} else {
			$list = $rc->jsonget();
		}

		$sumRow    = count($list);
		$totalPage = ceil($sumRow / self::$pageSize);

		$page   = min($totalPage, $page);
		$offset = ($page - 1) * self::$pageSize;

		$data['page']    = $page;
		$data['sumRow']  = $sumRow;
		$data['sumPage'] = $totalPage;
		$data['list']    = array_slice($list, $offset, self::$pageSize);

		return $data;
	}

	/**
	 * 获取军官指挥排行榜
	 * @author HeJunyun
	 * @return array $data
	 */
	static public function getHeroCommand($page = 1) {
		$now      = time();
		$syncTime = M_Ranking::getSyncTime(self::RANKINGS_HERO_COMMAND);
		$needSync = false;
		$rc       = new B_Cache_RC(T_Key::RANKINGS_HERO_COMMAND);
		if (!$rc->exists()) {
			$needSync = true;
		} else if (empty($syncTime)) {
			$needSync = true;
		} else if (!empty($syncTime) && $now - $syncTime > T_App::ONE_HOUR) {
			$needSync = true;
		}

		if ($needSync) {
			$list = M_Cron::syncRankingsByHeroCommand();
			M_Ranking::setSyncTime(self::RANKINGS_HERO_COMMAND, $now);
		} else {
			$list = $rc->jsonget();
		}

		$sumRow    = count($list);
		$totalPage = ceil($sumRow / self::$pageSize);

		$page   = min($totalPage, $page);
		$offset = ($page - 1) * self::$pageSize;

		$data['page']    = $page;
		$data['sumRow']  = $sumRow;
		$data['sumPage'] = $totalPage;
		$data['list']    = array_slice($list, $offset, self::$pageSize);

		return $data;
	}

	/**
	 * 获取军官军事排行榜
	 * @author HeJunyun
	 * @return array $data
	 */
	static public function getHeroMilitary($page = 1) {
		$now      = time();
		$syncTime = M_Ranking::getSyncTime(self::RANKINGS_HERO_MILITARY);
		$needSync = false;
		$rc       = new B_Cache_RC(T_Key::RANKINGS_HERO_MILITARY);
		if (!$rc->exists()) {
			$needSync = true;
		} else if (empty($syncTime)) {
			$needSync = true;
		} else if (!empty($syncTime) && $now - $syncTime > T_App::ONE_HOUR) {
			$needSync = true;
		}

		if ($needSync) {
			$list = M_Cron::syncRankingsByHeroMilitary();
			M_Ranking::setSyncTime(self::RANKINGS_HERO_MILITARY, $now);
		} else {
			$list = $rc->jsonget();
		}

		$sumRow    = count($list);
		$totalPage = ceil($sumRow / self::$pageSize);

		$page   = min($totalPage, $page);
		$offset = ($page - 1) * self::$pageSize;

		$data['page']    = $page;
		$data['sumRow']  = $sumRow;
		$data['sumPage'] = $totalPage;
		$data['list']    = array_slice($list, $offset, self::$pageSize);

		return $data;
	}

	/**
	 * 获取军官胜利排行榜
	 * @author HeJunyun
	 * @return array $data
	 */
	static public function getHeroWin($page = 1) {
		$rc   = new B_Cache_RC(T_Key::RANKINGS_HERO_WIN);
		$list = $rc->jsonget();

		$now      = time();
		$syncTime = M_Ranking::getSyncTime(self::RANKINGS_HERO_WIN);
		$needSync = false;


		if (!$rc->exists()) {
			$needSync = true;
		} else if (empty($syncTime)) {
			$needSync = true;
		} else if (!empty($syncTime) && $now - $syncTime > T_App::ONE_HOUR) {
			$needSync = true;
		}

		if ($needSync) {
			$list = M_Cron::syncRankingsByHeroWin();
			M_Ranking::setSyncTime(self::RANKINGS_HERO_WIN, $now);
		} else {
			$list = $rc->jsonget();
		}


		$sumRow    = count($list);
		$totalPage = ceil($sumRow / self::$pageSize);

		$page   = min($totalPage, $page);
		$offset = ($page - 1) * self::$pageSize;

		$data['page']    = $page;
		$data['sumRow']  = $sumRow;
		$data['sumPage'] = $totalPage;
		$data['list']    = array_slice($list, $offset, self::$pageSize);

		return $data;
	}

	/**
	 *   从内存中读取战役排行
	 * @param int $fbNo
	 * @return Ambigous <boolean, mixed, multitype:, multitype:unknown string >
	 */
	static public function getFBPass($fbNo) {
		$rc = new B_Cache_RC(T_Key::FB_PASS_RANK, $fbNo);

		$list = $rc->jsonget();
		if (empty($list)) {
			$list = B_DB::instance('FbPass')->getRow($fbNo); //先从数据库中得到值

			$list['recently_passed'] = !empty($list['recently_passed']) ? json_decode($list['recently_passed'], true) : array();
			$list['first_passed']    = !empty($list['first_passed']) ? json_decode($list['first_passed'], true) : array();
			$list['loss_least']      = !empty($list['recently_passed']) ? json_decode($list['loss_least'], true) : array();
			$list['level_lowest']    = !empty($list['level_lowest']) ? json_decode($list['level_lowest'], true) : array();
			$rc->jsonset($list);

		}
		if (!empty($list['loss_least'])) {
			foreach ($list['loss_least'] as $key => $loss) {
				$list['loss_least'][$key]['loss'] = !empty($list['loss_least'][$key]['loss']) ? round($list['loss_least'][$key]['loss'], 4) : '';
			}

		}
		if (!empty($list['level_lowest'])) {
			foreach ($list['level_lowest'] as $key => $loss) {
				$list['level_lowest'][$key]['level'] = !empty($list['level_lowest'][$key]['level']) ? round($list['level_lowest'][$key]['level'], 2) : '';
			}
		}
		return $list;
	}

	/**
	 * 设置战役排行
	 * @param int $fbNo
	 * @param array $data
	 * @return Ambigous <boolean, mixed>
	 */

	static public function setFBPass($fbNo, $data) {
		$ret = false;
		$rc  = new B_Cache_RC(T_Key::FB_PASS_RANK, $fbNo);
		$ret = $rc->jsonset($data);
		return $ret;
	}

	/**
	 * 根据用户ID获取用户战绩值排名
	 * @author Hejunyun
	 * @param int $id
	 * @return array/false
	 */
	static public function getRecordRankingsByCityId($id) {
		$rc   = new B_Cache_RC(T_Key::RANKINGS_RECORD);
		$data = $rc->zrevrank($id);
		if (!empty($data) || strlen($data) != 0) {
			return $data + 1;
		} else {
			return false;
		}
	}

	/**
	 * 获取战绩值排行榜
	 * @author duhuihui
	 * @return array $data
	 */
	static public function getRecord($page = 1) //1分钟更新一下
	{
		$rc        = new B_Cache_RC(T_Key::RANKINGS_RECORD);
		$list      = $rc->zcard();
		$sumRow    = min(self::CITY_TOTAL_LIMIT, $list);
		$totalPage = ceil($sumRow / self::$pageSize);

		$page   = min($totalPage, $page);
		$offset = ($page - 1) * self::$pageSize;

		$info  = $rc->zrevrange($offset, $offset + self::$pageSize - 1, true);
		$data1 = array();
		if (!empty($info)) {
			foreach ($info as $key => $value) {
				$objPlayerR = new O_Player($key);
				$cityInfo   = $objPlayerR->getCityBase();
				if ($cityInfo['last_visit_time'] > (time() - T_App::ONE_DAY * M_Ranking::DAY_NO_LOGIN)) {
					$data1[$key] = array(
						'ID'        => $key,
						'NickName'  => $cityInfo['nickname'],
						'Renown'    => $cityInfo['renown'],
						'MilMedal'  => $cityInfo['mil_medal'],
						'UnionId'   => $cityInfo['union_id'],
						'Signature' => $cityInfo['signature'],
						'FaceId'    => $cityInfo['face_id'],
						'Gender'    => $cityInfo['gender'],
						'MilRank'   => $cityInfo['mil_rank'],
						'Record'    => $value
					);
				}
			}
		}
		$i       = 1;
		$resData = array();
		if (!empty($data1)) {
			foreach ($data1 as $key => $val) {
				if ($val['UnionId'] > 0) {
					$union = M_Union::getInfo($val['UnionId']);
					unset($val['UnionId']);
					$val['Union'] = $union['name'];
				} else {
					unset($val['UnionId']);
					$val['Union'] = '';
				}
				$resData[$i - 1]         = $val;
				$resData[$i - 1]['RANK'] = $i;
				$i++;
			}
		}
		$recordActive    = M_Config::getVal('record_active');
		$startDay        = $recordActive['start']; //获取战绩值起始时间
		$endDay          = $recordActive['end']; //获取战绩值截止时间
		$data['page']    = $page;
		$data['sumPage'] = $totalPage;
		$data['sumRow']  = $sumRow;
		$data['list']    = $resData;
		$data['open']    = array(
			'start_time' => strtotime($startDay),
			'end_time'   => strtotime($endDay)
		);

		return $data;
	}

	/**
	 * 每周更新战役排行
	 * @param int $fbNo
	 * @param array $data
	 * @return Ambigous <boolean, mixed>
	 */

	static public function runFBPass() {
		$ret     = false;
		$ret1    = false;
		$list    = array();
		$ListAll = B_DB::instance('FbPass')->all();
		foreach ($ListAll as $fbNo => $value) {
			$ret                     = self::setFBPass($fbNo, array());
			$list['recently_passed'] = '';
			$list['first_passed']    = '';
			$list['loss_least']      = '';
			$list['level_lowest']    = '';
			$ret1                    = B_DB::instance('FbPass')->update($list, $fbNo);

		}
		return $ret && $ret1;
	}

	static public function syncPoint($max = 50, $length) {
		$t    = ceil(time() / 900);
		$rc   = new B_Cache_RC(T_Key::RANKINGS_RECORD, "{$t}_{$length}_{$max}");
		$data = $rc->jsonget();
		if (empty($data)) {
			$arr     = $noArr = array();
			$no      = 1;
			$cityIds = B_DB::instance('CityBreakout')->getRankByPoint($max);
			foreach ($cityIds as $val) {
				$cityInfo  = M_City::getInfo($val['city_id']);
				$unionName = '';
				if ($cityInfo['union_id'] > 0) {
					$unionInfo = M_Union::getInfo($cityInfo['union_id']);
					$unionName = $unionInfo['name'];
				}

				$arr[]                  = array(
					'ID'        => $val['city_id'],
					'FaceId'    => $cityInfo['face_id'],
					'Gender'    => $cityInfo['gender'],
					'NickName'  => $cityInfo['nickname'],
					'Renown'    => $cityInfo['renown'],
					'MilRank'   => $cityInfo['mil_rank'],
					'MilMedal'  => $cityInfo['mil_medal'],
					'UnionId'   => $cityInfo['union_id'],
					'Point'     => $val['point'],
					'UnionName' => $unionName,
					'Rank'      => $no,
				);
				$noArr[$val['city_id']] = $no;
				$no++;
			}

			$tmp       = array();
			$totalPage = ceil($max / $length);
			for ($page = 1; $page <= $totalPage; $page++) {
				$offset     = ($page - 1) * $length;
				$tmp[$page] = array_slice($arr, $offset, $length);
			}


			$data['page'] = $tmp;
			$data['no']   = $noArr;
			$rc->jsonset($data, T_App::ONE_DAY);
		}

		return $data;
	}
}

?>