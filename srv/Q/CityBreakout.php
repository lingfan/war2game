<?php

/** 城市突围DB层 */
class Q_CityBreakout extends B_DB_Dao {
	protected $_name = 'city_breakout';
	protected $_connType = 'game';
	protected $_primary = 'city_id';

	/**
	 * 根据城市ID获取城市突围信息
	 * @author chenhui on 20121019
	 * @param int $cityId 城市ID
	 * @return array/bool 城市突围信息(1D)
	 */
	public function getRow($cityId) {

		$row = $this->get($cityId);

		if (!empty($row)) {
			return $row;
		} else {
			$nowtime = time();
			$bout_times_cost = M_Config::getVal('bout_times_cost');
			$breakoutInfo = array(
				'city_id' => $cityId,
				'breakout_date' => date('Ymd'),
				'free_times_left' => $bout_times_cost[0],
				'buy_times_left' => 0,
				'buy_times' => 0,
				'breakout_pass' => '1',
				'breakout_data' => '[]',
				'breakout_cd' => '0_1',
				'battle_id' => 0,
				'point' => 0,
				'create_at' => $nowtime,
				'sys_sync_time' => $nowtime,
			);
			$initRet = $this->insert($breakoutInfo);
			if ($initRet) {
				return $breakoutInfo;
			} else {
				return false;
			}
		}
	}


	public function getRankByPoint($limit = 10) {
		$sql = "SELECT city_id, point FROM city_breakout order by point DESC limit 0, {$limit} ";
		$rows = $this->fetchAll($sql);
		return $rows;

	}

}

?>