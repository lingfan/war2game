<?php

class Q_CityLottery extends B_DB_Dao {
	protected $_name = 'city_lottery';
	protected $_connType = 'game';
	protected $_primary = 'city_id';

	/**
	 * 获取抽奖信息
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @return array/bool
	 */
	public function getRow($cityId) {
		$row = $this->get($cityId);

		if (empty($row['city_id'])) {
			$row = array(
				'city_id' => $cityId,
				'refresh_date' => date('Ymd'),
				'refresh_num' => 0,
				'award_content' => '[]',
				'award_no' => 0,
			);
			$bSucc = $this->insert($row);
			if (!$bSucc) {
				return false;
			}
		}
		return $row;
	}

}

?>