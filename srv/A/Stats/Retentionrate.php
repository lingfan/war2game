<?php

/**
 *用户留存率
 */
class A_Stats_Retentionrate {

	static public function Retentionrate($parms = array()) {
		$curPage = !empty($formVals['page']) ? $formVals['page'] : 1;
		$offset = !empty($formVals['rows']) ? $formVals['rows'] : 20;
		$formVals['filter'] = !empty($formVals['filter']) ? $formVals['filter'] : array();
		$startDate = !empty($parms['filter']['create_start']) ? $parms['filter']['create_start'] : date('Y-m-01');
		$endDate = !empty($parms['filter']['create_end']) ? $parms['filter']['create_end'] : date('Y-m-d');
		$consumer_id = isset($parms['filter']['consumer_id']) ? $parms['filter']['consumer_id'] : 0;
		$day = (strtotime($endDate) - strtotime($startDate)) / 3600 / 24;
		for ($i = 0; $i <= $day; $i++) {
			$startDay = strtotime($startDate) + $i * 3600 * 24;
			$formatStart = date('Y-m-d', $startDay);
			$endDay = strtotime($startDate) + ($i + 1) * 3600 * 24;


			$newCityIdList = array();
			if ($startDay < time()) {
				$userInfo = B_DB::instance('City')->countCityId(intval($startDay), intval($endDay), $consumer_id);
				$cityNum = count($userInfo); //新注册用户的人数
				foreach ($userInfo as $value) {
					if ($value['city_id']) {
						$newCityIdList[] = $value['city_id'];
					}
				}


				$ret[$formatStart][0] = $cityNum;
			} else {
				$ret[$formatStart][0] = '-';
			}

			for ($j = 1; $j <= 7; $j++) {
				$cityIdArr = array();
				if ($startDay + 3600 * 24 * $j < time()) {

					if (date('Ymd', $startDay + 3600 * 24 * $j) == date('Ymd')) {
						$rc = new B_Cache_RC(T_Key::CITY_ACTIVE_NUM, date('Ymd'));
						$data = $rc->hgetall();
						$cityIdArr = !empty($data) ? array_keys($data) : array();
					} else {
						$onlineInfo = B_DBStats::getOnlineCityId(date('Ymd', $startDay + 3600 * 24 * $j));

						$cityIdArr = !empty($onlineInfo) ? array_keys(json_decode(gzuncompress(base64_decode($onlineInfo[0]['city_list'], true)), true)) : array(); //第二天登陆cityId列表
					}
					if (!empty($newCityIdList)) {
						$ret[$formatStart][$j] = count(array_intersect($newCityIdList, $cityIdArr));
					} else {
						$ret[$formatStart][$j] = 0;
					}
					if ($ret[$formatStart][0] != 0 && $ret[$formatStart][$j] != 0) {
						$ret[$formatStart][$j] = $ret[$formatStart][$j] . '(' . (round($ret[$formatStart][$j] / $ret[$formatStart][0], 4) * 100) . '%' . ')';
					} else {
						$ret[$formatStart][$j] = 0;
					}
				} else {
					$ret[$formatStart][$j] = '-';
				}


			}


			if ($startDay + 3600 * 24 * 14 < time()) {
				$cityIdArr = array();
				if (date('Ymd', $startDay + 3600 * 24 * 14) == date('Ymd')) {
					$rc = new B_Cache_RC(T_Key::CITY_ACTIVE_NUM, date('Ymd'));
					$data = $rc->hgetall();
					$cityIdArr = !empty($data) ? array_keys($data) : array();
				} else {
					$onlineInfo = B_DBStats::getOnlineCityId(date('Ymd', $startDay + 3600 * 24 * 14));

					$cityIdArr = !empty($onlineInfo) ? array_keys(json_decode(gzuncompress(base64_decode($onlineInfo[0]['city_list'], true)), true)) : array(); //第二天登陆cityId列表
				}
				if (!empty($newCityIdList)) {
					$ret[$formatStart][14] = count(array_intersect($newCityIdList, $cityIdArr));
				} else {
					$ret[$formatStart][14] = 0;
				}
				if ($ret[$formatStart][0] != 0 && $ret[$formatStart][14] != 0) {
					$ret[$formatStart][14] = $ret[$formatStart][14] . '(' . (round($ret[$formatStart][14] / $ret[$formatStart][0], 4) * 100) . '%' . ')';
				} else {
					$ret[$formatStart][14] = 0;
				}
			} else {
				$ret[$formatStart][14] = '-';
			}
			if ($startDay + 3600 * 24 * 30 < time()) {
				$cityIdArr = array();
				if (date('Ymd', $startDay + 3600 * 24 * 30) == date('Ymd')) {
					$rc = new B_Cache_RC(T_Key::CITY_ACTIVE_NUM, date('Ymd'));
					$data = $rc->hgetall();
					$cityIdArr = !empty($data) ? array_keys($data) : array();
				} else {
					$onlineInfo = B_DBStats::getOnlineCityId(date('Ymd', $startDay + 3600 * 24 * 30));

					$cityIdArr = !empty($onlineInfo) ? array_keys(json_decode(gzuncompress(base64_decode($onlineInfo[0]['city_list'], true)), true)) : array(); //第二天登陆cityId列表
				}
				if (!empty($newCityIdList)) {
					$ret[$formatStart][30] = count(array_intersect($newCityIdList, $cityIdArr));
				} else {
					$ret[$formatStart][30] = 0;
				}
				if ($ret[$formatStart][0] != 0 && $ret[$formatStart][30] != 0) {
					$ret[$formatStart][30] = $ret[$formatStart][30] . '(' . (round($ret[$formatStart][30] / $ret[$formatStart][0], 4) * 100) . '%' . ')';
				} else {
					$ret[$formatStart][30] = 0;
				}
			} else {
				$ret[$formatStart][30] = '-';
			}
		}

		return $ret;
	}


}

?>