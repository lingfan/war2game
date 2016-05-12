<?php

class Q_CityQqShare extends B_DB_Dao {
	protected $_name = 'city_qq_share';
	protected $_connType = 'game';
	protected $_primary = 'city_id';

	/**
	 * 通过分享ID获取信息
	 * @author duhuihui
	 * @param int $cityId 城市ID
	 * @return array/bool
	 */
	public function getRow($cityId) {
		$row = $this->get($cityId);
		if (empty($row['city_id'])) {
			$row = array(
				'city_id' => $cityId,
				'complete_txt' => '', //array(完成状态,完成条件,完成数)
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