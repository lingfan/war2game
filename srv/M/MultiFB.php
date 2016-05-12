<?php

class M_MultiFB {
	const PAGE_SIZE = 10;

	/** 加攻击 **/
	const ADDITION_ATK = 1;
	/** 加防御 **/
	const ADDITION_DEF = 2;
	/** 加生命 **/
	const ADDITION_HP = 3;
	/** 恢复兵 **/
	const ADDITION_CURE = 4;

	static $addition = array(self::ADDITION_ATK, self::ADDITION_DEF, self::ADDITION_HP, self::ADDITION_CURE);

	/** 行军时间 **/
	const MARCH_TIME = 1;

	/**
	 * 获取基础多人副本数据
	 * @author huwei
	 * @return array
	 */
	static public function getBaseList() {
		static $list = array();
		if (empty($list)) {
			$apcKey  = T_Key::BASE_MULTI_FB;
			$tmpList = B_Cache_APC::get($apcKey);
			if (empty($tmpList)) {
				$tmpList = B_DB::instance('BaseMultiFB')->all();
				foreach ($tmpList as $id => $val) {
					$defline = array();
					$tmp     = explode('|', $val['def_line']);
					foreach ($tmp as $tmpVal) {
						list($lineNo, $npcId, $mapNo, $point) = explode('_', $tmpVal);
						$defline[$lineNo] = array($npcId, $mapNo, $point);
					}
					$tmpList[$id]['def_line'] = $defline;
				}
				Logger::base(__METHOD__);
				APC::set($apcKey, $tmpList);
			}

			$list = $tmpList;
		}
		return $list;
	}

	static public function clean() {
		$apcKey = T_Key::BASE_MULTI_FB;
		return B_Cache_APC::del($apcKey);
	}

	/**
	 * 更新队伍信息
	 * @author huwei
	 * @param array $fieldArr
	 * @param bool $upDB
	 */
	static public function setInfo($fieldArr, $upDB = false) {
		$ret = false;
		if (!empty($fieldArr['id'])) {
			$id   = $fieldArr['id'];
			$info = array();
			foreach ($fieldArr as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$teamMultiFields)) {
					$info[$key] = $val;
				}
			}

			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::TEAM_MULTI_FB_INFO, $id);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$upDB && B_DB::instance('TeamMultiFb')->update($info, $id);
				} else {
					Logger::error(array(__METHOD__, 'Update RC Team Info Fail', func_get_args()));
				}
			}
		}

		return $ret ? $info : false;
	}

	static public function addInfo($info) {
		$id = 0;
		if ($info['multi_fb_id']) {
			$id = B_DB::instance('TeamMultiFb')->insert($info);
			if ($id) {
				$rc  = new B_Cache_RC(T_Key::TEAM_MULTI_FB_LIST);
				$ret = $rc->sadd($id);
				if (!$ret) {
					Logger::error(array(__METHOD__, 'Add RC Team List Fail', func_get_args()));
				}

				$info['id'] = $id;
				M_MultiFB::setInfo($info);
			}
		}
		return $id;
	}

	/**
	 * 删除队伍信息
	 * @author huwei
	 * @param int $id
	 */
	static public function delInfo($id) {
		$ret = B_DB::instance('TeamMultiFb')->delete($id);
		if (!$ret) {
			Logger::error(array(__METHOD__, 'Del DB Team Info Fail', func_get_args()));
		}
		$rc  = new B_Cache_RC(T_Key::TEAM_MULTI_FB_INFO, $id);
		$ret = $rc->delete();
		if ($ret) {
			$rc  = new B_Cache_RC(T_Key::TEAM_MULTI_FB_LIST);
			$ret = $rc->srem($id);
			if (!$ret) {
				Logger::error(array(__METHOD__, 'Del RC Team List Fail', func_get_args()));
			}
		} else {
			Logger::error(array(__METHOD__, 'Del RC Team Info Fail', func_get_args()));
		}


		return $ret;
	}

	/**
	 * 获取队伍信息
	 * @author huwei
	 * @param int $id
	 * @return array
	 */
	static public function getInfo($id) {
		$ret = false;
		if ($id > 0) {
			$rc  = new B_Cache_RC(T_Key::TEAM_MULTI_FB_INFO, $id);
			$ret = $rc->hmget(T_DBField::$teamMultiFields);
			if (empty($ret['id'])) {
				$teamInfo = B_DB::instance('TeamMultiFb')->get($id);
				if (!empty($teamInfo)) {
					$ret = M_MultiFB::setInfo($teamInfo);
				}
			}
		}


		return $ret;
	}

	static public function addTeamList($fbId, $teamId) {
		$rc  = new B_Cache_RC(T_Key::TEAM_MULTI_FB_LIST_TMP, $fbId);
		$ret = $rc->sadd($teamId);
		return $ret;
	}

	static public function delTeamList($fbId, $teamId) {
		$rc  = new B_Cache_RC(T_Key::TEAM_MULTI_FB_LIST_TMP, $fbId);
		$ret = $rc->srem($teamId);
		return $ret;
	}

	static public function getTeamList($fbId) {
		$fbId = intval($fbId);
		$ret  = array();
		if ($fbId > 0) {
			$rc = new B_Cache_RC(T_Key::TEAM_MULTI_FB_LIST_TMP, $fbId);
			if ($rc->exists()) {
				$ret = $rc->smembers();
			} else {
				$list = B_DB::instance('TeamMultiFb')->getsBy(array('multi_fb_id' => $fbId));
				foreach ($list as $val) {
					$rc->sadd($val['id']);
					$ret[] = $val['id'];
				}
			}
		}


		return $ret;
	}

	/**
	 * ID列表分页数据
	 * @author huwei
	 * @param int $curPage
	 * @param array $listIds
	 * @return array
	 */
	static public function parsePage($listIds, $curPage) {
		$list      = array();
		$totalPage = 0;
		if (!empty($listIds)) {
			$total     = count($listIds);
			$totalPage = ceil($total / self::PAGE_SIZE);
			$curPage   = min($totalPage, $curPage);
			$offset    = ($curPage - 1) * self::PAGE_SIZE;

			sort($listIds);
			$list = array_slice($listIds, $offset, 10);
		}

		$ret['listIds']   = $list;
		$ret['totalPage'] = $totalPage;
		$ret['curPage']   = $curPage;
		return $ret;
	}

	static public function getListByType($type) {
		$apcKey = T_Key::TEAM_MULTI_FB_LIST_TMP . '_' . $type;
		$list   = B_Cache_APC::get($apcKey);
		$list   = false;
		if (empty($list)) {
			$ids  = M_MultiFB::getList($type);
			$list = array();
			if (!empty($ids)) {
				foreach ($ids as $id) {
					$info = M_MultiFB::getInfo($id);

					if ($info['id']) {
						$num = 0;
						for ($i = 1; $i < 6; $i++) {
							$pos = 'pos_' . $i;
							if (!empty($info[$pos])) {
								$num++;
							}
						}
						$header = json_decode($info['pos_1'], true);
						$list[] = array(
							'TeamId'    => $id,
							'Nickname'  => $header['nickname'],
							'FBId'      => $info['multi_fb_id'],
							'Type'      => $info['type'],
							'Num'       => $num,
							'UnionId'   => $info['union_id'],
							'CreateAt'  => $info['create_at'],
							'StartTime' => $info['start_time'],
						);
					}
				}
			}
			APC::set($apcKey, $list, T_App::ONE_MINUTE * 0.5);
		}
		return $list;
	}

	static public function setCityInfo($fieldArr, $upDB = false) {
		$ret = false;
		if (!empty($fieldArr['city_id'])) {
			$id   = $fieldArr['city_id'];
			$info = array();
			foreach ($fieldArr as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$cityMultiFields)) {
					$info[$key] = $val;
				}
			}
			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_MULTI_FB_INFO, $id);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$upDB && M_CacheToDB::addQueue(T_Key::CITY_MULTI_FB_INFO . ':' . $id);
				} else {
					Logger::error(array(__METHOD__, 'Update RC CityMultiFB Info Fail', func_get_args()));
				}
			}
		}

		return $ret ? $info : false;
	}

	static public function getCityInfo($cityId) {
		$ret = false;
		if ($cityId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_MULTI_FB_INFO, $cityId);
			$ret = $rc->hmget(T_DBField::$cityMultiFields);
			if (empty($ret['city_id'])) {
				$cityMultiFBInfo = B_DB::instance('CityMultiFB')->getRow($cityId);

				if (!empty($cityMultiFBInfo)) {
					$ret = M_MultiFB::setCityInfo($cityMultiFBInfo);
				}
			}

			if (date('Ymd') != $ret['daily_date']) {
				$buyCost                 = M_Config::getVal('multi_fb_buy_cost');
				$ret['daily_free_times'] = $buyCost[0];
				$ret['daily_date']       = date('Ymd');
				$ret['daily_buy_times']  = 0;

				$upArr = array(
					'city_id'          => $cityId,
					'daily_date'       => $ret['daily_date'],
					'daily_free_times' => $ret['daily_free_times'],
					'daily_buy_times'  => $ret['daily_buy_times'],
				);

				self::setCityInfo($upArr);
			}
		}

		return $ret;
	}


}

?>