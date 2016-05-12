<?php

/**
 * 周活跃和月活跃用户统计
 */
class A_Stats_ActiveUser {
	static public function ActiveUser($params = array()) {
		$row = array();
		$type = isset($params['type']) ? $params['type'] : 1; //是周流失用户还是月流失用户
		if ($type == 1) {
			$params['num'] = 7;
		} else {
			$params['num'] = 30;
		}
		$cityInfo = B_DBStats::getActiveUserInfo($params);
		$row = $cityInfo;
		return $row;
	}
}

?>