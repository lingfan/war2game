<?php

/**
 * 周流失和月流失用户统计
 */
class A_Stats_OutflowUser {
	static public function OutflowUser($params = array()) {
		$row = array();
		$type = !empty($params['type']) ? $params['type'] : 1; //是周流失用户还是月流失用户
		if ($type == 1) {
			$params['num'] = 7;
		} else {
			$params['num'] = 30;
		}
		$cityInfo = B_DBStats::getOutflowUserInfo($params);
		$row['total'] = 0;
		$row['total'] = $cityInfo[0]['num'];
		return $row;
	}
}

?>