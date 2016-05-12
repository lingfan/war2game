<?php

class M_Extra {
	static public function getInfo($cityId) {
		$info   = false;
		$cityId = intval($cityId);
		if ($cityId > 0) {
			$rc   = new B_Cache_RC(T_Key::CITY_EXTRA_INFO, $cityId);
			$info = $rc->hmget(T_DBField::$cityExtraFields);
			if (empty($info['city_id'])) {
				$info = B_DB::instance('CityExtra')->get($cityId);
				if (empty($info['city_id'])) {
					$info['city_id'] = $cityId;
					B_DB::instance('CityExtra')->insert($info);
				}
				if (!empty($info)) {
					M_Extra::setInfo($cityId, $info, false);
				}
			}
		}
		return $info;
	}

	/**
	 * 更新扩展信息
	 * @param int $cityId
	 * @param array $updInfo
	 * @param bool $upDB
	 * @return bool
	 */
	static public function setInfo($cityId, $updInfo, $upDB = true) {
		$ret    = false;
		$cityId = intval($cityId);
		if ($cityId > 0) {
			$arr = array();
			foreach ($updInfo as $k => $v) {
				in_array($k, T_DBField::$cityExtraFields) && $arr[$k] = $v;
			}

			$rc = new B_Cache_RC(T_Key::CITY_EXTRA_INFO, $cityId);
			if (!empty($arr)) {
				$ret = $rc->hmset($arr, T_App::ONE_DAY);
			}

			if ($ret) {
				$extraData = $rc->hgetall();
				if (!empty($extraData['city_id'])) {
					$extraData['sys_sync_time'] = time();
					$ret                        = B_DB::instance('CityExtra')->update($extraData, $extraData['city_id']);
					//Logger::debug(array(__METHOD__, $extraData));
				}

				$upDB && M_CacheToDB::addQueue(T_Key::CITY_EXTRA_INFO . ':' . $cityId);
			} else {
				Logger::error(array(__METHOD__, 'Err Update', func_get_args()));
			}
		}

		return $ret;
	}
}

?>