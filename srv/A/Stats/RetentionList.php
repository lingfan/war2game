<?php

/**
 *用户留存率
 */
class A_Stats_RetentionList {

	static public function RetentionList($parms = array()) {
		$dayNum = !empty($parms['day_num']) ? $parms['day_num'] : 7;
		$formatStart = !empty($parms['date_id']) ? $parms['date_id'] : 0;
		$list = array();
		$ret = array();
		if ($formatStart != 0) {

			$consumer_id = isset($parms['consumer_id']) ? $parms['consumer_id'] : 0;
			$startDay = strtotime($formatStart);
			$endDay = $startDay + 3600 * 24;
			$newCityIdList = array();
			if ($startDay < time()) {
				$userInfo = B_DB::instance('City')->countCityId(intval($startDay), intval($endDay), $consumer_id);
				$cityNum = count($userInfo); //新注册用户的人数
				foreach ($userInfo as $value) {
					if ($value['city_id']) {
						$newCityIdList[] = $value['city_id'];
					}
				}
			} else {
				$ret[$formatStart][0] = '-';
			}
			$cityIdArr = array();
			if ($startDay + 3600 * 24 * $dayNum < time()) {
				if (date('Ymd', $startDay + 3600 * 24 * $dayNum) == date('Ymd')) {
					$rc = new B_Cache_RC(T_Key::CITY_ACTIVE_NUM, date('Ymd'));
					$data = $rc->hgetall();
					$cityIdArr = !empty($data) ? array_keys($data) : array();
				} else {
					$onlineInfo = B_DBStats::getOnlineCityId(date('Ymd', $startDay + 3600 * 24 * $dayNum));
					$cityIdArr = !empty($onlineInfo) ? array_keys(json_decode(gzuncompress(base64_decode($onlineInfo[0]['city_list'], true)), true)) : array(); //第二天登陆cityId列表
				}
				if (!empty($newCityIdList)) {
					$ret[$formatStart][$dayNum] = array_values(array_intersect($newCityIdList, $cityIdArr));
				} else {
					$ret[$formatStart][$dayNum] = 0;
				}

			} else {
				$ret[$formatStart][$dayNum] = '-';
			}

		}
		if (!empty($ret)) {
			foreach ($ret[$formatStart][$dayNum] as $value) {
				$list[] = M_City::getInfo($value);

			}
		}
		return $list;
	}


}

?>