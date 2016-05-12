<?php

/**
 * 历史在线人数统计
 */
class A_Stats_HistoryHigh {
	static public function HistoryHigh($params = array()) {
		$row = array();
		$ym = isset($params['day']) ? $params['day'] : date('Ym'); //年月

		$days = isset($params['dayNum']) ? $params['dayNum'] : ''; //当月的天数

		$start = $ym . '01'; //1日0时0分0秒
		$end = $ym . $days; //月末23时59分59秒
		$row = B_DBStats::totalOnline('stats_online_people', $start, $end);
		return $row;
	}
}

?>