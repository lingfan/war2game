<?php

/**
 *日活跃用户统计
 */
class A_Stats_DayActiveUser {
	static public function DayActiveUser($params = array()) {
		$row = array();
		$ym = isset($params['day']) ? $params['day'] : date('Ym'); //年月

		$days = isset($params['dayNum']) ? $params['dayNum'] : ''; //当月的天数

		$start = $ym . '01'; //1日0时0分0秒
		$end = $ym . $days; //月末23时59分59秒
		$row = B_DBStats::totalOnline('stats_active_num', $start, $end);
		$rc = new B_Cache_RC(T_Key::CITY_ACTIVE_NUM, date('Ymd'));
		$data = $rc->hgetall();
		$row[date('Ymd')] = $data;
		return $row;
	}

	static public function DayActiveUserList($params = array()) {
		$row = array();
		$date = isset($params['date']) ? $params['date'] : ''; //当月的天数
		$start = $date; //1日0时0分0秒
		$end = $date; //月末23时59分59秒
		if ($date == date('Ymd')) {
			$rc = new B_Cache_RC(T_Key::CITY_ACTIVE_NUM, date('Ymd'));
			$data = $rc->hgetall();
			$cityIdArr = array_keys($data);
		} else {
			$row = B_DBStats::totalOnline('stats_active_num', $start, $end);
			$cityIdArr = array_keys(json_decode(gzuncompress(base64_decode($row[0]['city_list'], true)), true));
		}

		foreach ($cityIdArr as $cityId) {
			$list[] = M_City::getInfo($cityId);

		}

		return $list;
	}
}

?>