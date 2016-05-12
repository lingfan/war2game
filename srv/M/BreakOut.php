<?php

/** 突围模型层 */
class M_BreakOut {
	/** 突围活动 关闭 */
	const BREAKOUT_CLOSE = 0;
	/** 突围活动 开启 */
	const BREAKOUT_OPEN = 1;

	/** 某突围状态 关闭 */
	const STATUS_END = 0;
	/** 某突围状态 开始 */
	const STATUS_START = 1;

	/** 某突围某关卡宝箱未领奖 */
	const OUTPOST_NOT_AWARD = 0;
	/** 某突围某关卡宝箱已领奖 */
	const OUTPOST_HAD_AWARD = 1;

	/** 某突围未开启 */
	const BREAKOUT_NOT_OPEN = -1;


	/**
	 * 获取某特定突围基础信息
	 * @author chenhui on 20121021
	 * @param int $boutId 突围ID
	 * @return array 1D
	 */
	static public function baseInfo($boutId) {
		$apcKey = T_Key::BASE_BREAKOUT . '_' . $boutId;
		$info   = B_Cache_APC::get($apcKey);
		if (empty($info)) {
			$info = B_DB::instance('BaseBreakout')->get($boutId);
			APC::set($apcKey, $info);
		}
		return $info;
	}

	/**
	 * 获取某特定突围包含宝箱奖励的关ID
	 * @author chenhui on 20121106
	 * @param int $boutId 突围ID
	 * @return array 1D
	 */
	static public function getHasAwardIds(array $baseBoutArr, array $filterHadAwardArr = array()) {
		$ret = array();
		if (!empty($baseBoutArr)) {
			foreach ($baseBoutArr as $key => $strOutpost) {
				$arrOutpost = explode(',', $strOutpost);
				if (intval($arrOutpost[2]) > 0) {
					$id       = $key + 1; //关卡
					$ret[$id] = in_array($id, $filterHadAwardArr) ? M_BreakOut::OUTPOST_HAD_AWARD : M_BreakOut::OUTPOST_NOT_AWARD;
				}
			}
		}
		return $ret;
	}

	/**
	 * 获取玩家突围信息
	 * @author chenhui on 20121019
	 * @param int $cityId
	 * @return array 1D
	 */
	static public function getCityBreakOut($cityId) {
		$cityId = intval($cityId);
		$ret    = false;
		if ($cityId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_BREAKOUT, $cityId);
			$ret = $rc->hmget(T_DBField::$cityBreakOutFields);
			if (empty($ret['city_id'])) {
				$ret = B_DB::instance('CityBreakout')->getRow($cityId);
				if (!empty($ret)) {
					$rc->hmset($ret, T_App::ONE_DAY);
				}
			}

			$now_date = date('Ymd');
			if ($now_date != $ret['breakout_date']) {
				$bout_times_cost = M_Config::getVal('bout_times_cost');
				$upInfo          = array(
					'breakout_date'   => $now_date,
					'free_times_left' => $bout_times_cost[0],
					'buy_times'       => 0,
					'breakout_data'   => '[]'
				);
				M_BreakOut::updateCityBreakOut($cityId, $upInfo, true); //清空
				$ret['breakout_date']   = $upInfo['breakout_date'];
				$ret['free_times_left'] = $upInfo['free_times_left'];
				$ret['buy_times']       = $upInfo['buy_times'];
				$ret['breakout_data']   = $upInfo['breakout_data'];
			}
			$ret['point']         = !empty($ret['point']) ? intval($ret['point']) : 0;
			$ret['breakout_data'] = json_decode($ret['breakout_data'], true);
		}
		return $ret;
	}

	/**
	 * 根据城市ID更新城市突击信息
	 * @author chenhui on 20121019
	 * @param int $cityId 城市ID
	 * @param array $upInfo 要更新的键值对数组
	 * @param bool $upDB 是否更新到DB
	 * @return array/false
	 */
	static public function updateCityBreakOut($cityId, $upInfo, $upDB = true) {
		$ret = false;
		if (!empty($cityId) && is_array($upInfo) && !empty($upInfo)) {
			$info = array();
			foreach ($upInfo as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$cityBreakOutFields)) {
					$info[$key] = $val;
				}
			}
			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_BREAKOUT, $cityId);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$upDB && M_CacheToDB::addQueue(T_Key::CITY_BREAKOUT . ':' . $cityId);
				} else {
					$msg = array(__METHOD__, 'Update BreakOut Info Fail', func_get_args());
					Logger::error($msg);
				}
			}
		}

		return $ret ? $info : false;
	}

}

?>