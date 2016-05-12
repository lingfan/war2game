<?php

/**
 * 野地驻守模块
 */
class M_March_Hold {
	/** 循环时间 */
	const LOOP_DELAY_TIME = 5;

	/**
	 * 添加占领的野地坐标
	 */
	static public function set($posNo) {
		$ret   = false;
		$posNo = intval($posNo);
		if ($posNo > 0) {
			$rc  = new B_Cache_RC(T_Key::MARCH_HOLD_LIST_KEY);
			$ret = $rc->sadd($posNo);
			if (!$ret) {
				$msg = array(__METHOD__, 'Set March Hold Queue Fail', func_get_args());
				Logger::error($msg);
			}
		}
		return $ret;
	}

	/**
	 * 是否被占领
	 */
	static public function exist($posNo) {
		$rc = new B_Cache_RC(T_Key::MARCH_HOLD_LIST_KEY);
		return $rc->sismember($posNo);
	}

	/**
	 * 获取已占领的野地坐标
	 */
	static public function get() {
		$rc = new B_Cache_RC(T_Key::MARCH_HOLD_LIST_KEY);
		return $rc->smembers();
	}

	/**
	 * 占领野地数量
	 */
	static public function size() {
		$rc = new B_Cache_RC(T_Key::MARCH_HOLD_LIST_KEY);
		return $rc->scard();
	}

	/**
	 * 删除野地占领坐标
	 */
	static public function del($posNo) {
		$ret   = false;
		$posNo = intval($posNo);
		if ($posNo > 0) {
			if (self::exist($posNo)) {
				$rc  = new B_Cache_RC(T_Key::MARCH_HOLD_LIST_KEY);
				$ret = $rc->srem($posNo);
				if (!$ret) {
					$msg = array(__METHOD__, 'Del March Hold Queue Fail', self::get(), func_get_args());
					Logger::error($msg);
				}
			} else {
				$ret = true;
			}

		}
		return $ret;
	}

	/**
	 * NPC野地驻守 守护进程
	 */
	static public function run() {
		$date = date('Ymd');
		$now  = time();
		$list = self::get();

		foreach ($list as $posNo) {
			$needDel = true;
			$mapInfo = M_MapWild::getWildMapInfo($posNo);
			if (!empty($mapInfo['pos_no'])) {
				if ($mapInfo['type'] == T_Map::WILD_MAP_CELL_CITY) //城市属地
				{ //城市属地
					$holdTimeInterval = M_Config::getVal('hold_city_time_interval'); //4
					if ($mapInfo['city_id'] > 0) {
						$cityColonyInfo = M_ColonyCity::getInfo($mapInfo['city_id']);
						if (!empty($cityColonyInfo['atk_city_id'])) {
							if ($cityColonyInfo['hold_time'] > T_App::ONE_HOUR * $holdTimeInterval) { //超过过期时间
								if ($cityColonyInfo['atk_march_id'] > 0) {
									M_March::setMarchBack($cityColonyInfo['atk_march_id']);
								}

								$ret = M_ColonyCity::del($cityColonyInfo['atk_city_id'], $posNo);
								if (!$ret) {
									Logger::error(array(__METHOD__, 'Error Set Wild Map', array($mapInfo['city_id'], $posNo)));
								}
							} else {
								$needDel = false;
								$updInfo = array('hold_time' => $cityColonyInfo['hold_time'] + M_Client::VISIT_LOOP_DELAY_TIME);
								M_ColonyCity::setInfo($mapInfo['city_id'], $updInfo);
							}
						}
					}
				} else if ($mapInfo['type'] == T_Map::WILD_MAP_CELL_NPC) { //野外NPC属地
					$objPlayer = new O_Player($mapInfo['city_id']);
					if ($mapInfo['hold_expire_time'] < $now) { //超过过期时间
						if ($mapInfo['march_id'] > 0) {
							M_March::setMarchBack($mapInfo['march_id']);
						}
						//删除属地信息
						$ret = $objPlayer->ColonyNpc()->del($posNo);
						if (!$ret) {
							$msg = array(__METHOD__, 'Error Set Wild Map', array($mapInfo['city_id'], $posNo));
							Logger::error($msg);
						}
					} else { //补兵操作
						$needDel = false;
						$arr     = explode(',', $mapInfo['last_fill_army_time']);
						if ($arr[0] < $now) {
							if (in_array($arr[1], array(M_NPC::CITY_NPC_FOOT, M_NPC::CITY_NPC_GUN, M_NPC::CITY_NPC_ARMOR, M_NPC::CITY_NPC_AIR))) { //补兵操作

								$arr[0] = $now + T_App::ONE_MINUTE * 10; //时间差 10分钟
								$objPlayer->Army()->makeCityArmy($arr[1], $arr[2]);
								$objPlayer->save();
								$fieldArr['last_fill_army_time'] = implode(',', $arr);
								M_MapWild::setWildMapInfo($posNo, $fieldArr);
							}
						}
					}

				}
			}
			$needDel && self::del($posNo);
		}
	}
}

?>