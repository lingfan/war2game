<?php

class Q_CityMultiFB extends B_DB_Dao {
	protected $_name = 'city_multi_fb';
	protected $_connType = 'game';
	protected $_primary = 'city_id';

	public function getRow($cityId) {
		$row = $this->get($cityId);
		if (empty($row)) {
			$fieldArr = array(
				'city_id' => $cityId,
				'daily_date' => '',
				'daily_free_times' => 0,
				'daily_buy_times' => 0,
				'left_buy_times' => 0,
				'team_id' => 0,
				'sys_sync_time' => 0,
			);

			$ret = $this->insert($fieldArr);
			$row = $ret ? $fieldArr : false;
		}

		return $row;
	}

}

?>