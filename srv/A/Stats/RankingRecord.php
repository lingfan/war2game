<?php

/**
 * 周活跃和月活跃用户统计
 */
class A_Stats_RankingRecord {
	static public function RankingRecord($params = array()) {
		if (!isset($params['nickname'])) {
			return (array)false;
		}
		$nickname = explode("\n", $params['nickname']);
		if (!is_array($nickname)) {
			return (array)false;
		}
		if (count($nickname) < 1) {
			return (array)false;
		}
		$consumerIds = isset($params['consumer_ids']) ? $params['consumer_ids'] : array();
		//根据玩家昵称获取城市ID
		$newName = array();
		foreach ($nickname as $key => $val) {
			$cityId = M_City::getCityIdByNickName(trim($val));
			if ($cityId > 0) {
				$cityInfo = M_City::getInfo($cityId);
				if (isset($consumerIds[$cityInfo['consumer_id']])) {
					$newName[$key] = array(
						'id' => $cityId,
						'nickname' => trim($val)
					);
				}
			}
		}

		if (empty($newName)) {
			return (array)false;
		}

		$nickname = $newName;
		foreach ($nickname as $val) {
			$rc = new B_Cache_RC(T_Key::RANKINGS_RECORD);
			$rc->zAdd($params['ranking_value'], $val['id']);
		}

		return 1;
	}
}

?>