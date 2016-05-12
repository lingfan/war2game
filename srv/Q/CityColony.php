<?php

class Q_CityColony extends B_DB_Dao {
	protected $_name = 'city_colony';
	protected $_connType = 'game';
	protected $_primary = 'city_id';

	/**
	 * 根据城市ID获取城市额外信息
	 * @author chenhui on 20110425
	 * @param int $cityId 城市ID
	 * @return array 城市额外信息(一维数组)
	 */
	public function getRow($cityId) {
		$cityId = intval($cityId); //6.26更改

		if ($cityId > 0) {
			$row = $this->get($cityId);

			if (!empty($row)) {
				return $row;
			} else {
				$row = array(
					'city_id' => $cityId,
					'rescue_date' => 0,
					'atk_city_id' => 0,
					'atk_march_id' => 0,
					'hold_time' => 0,
					'colony_city' => '',
					'rescue_num' => 0,
				);
				$bSucc = $this->insert($row);
				if (!$bSucc) {
					return false;
				}
			}

		}
		return $row;
	}


}

?>