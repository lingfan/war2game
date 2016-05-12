<?php

/** 单个城市越野DB层 */
class Q_CityHorse extends B_DB_Dao {
	protected $_name = 'city_horse';
	protected $_connType = 'game';
	protected $_primary = 'city_id';

	/**
	 * 根据城市ID获取城市越野信息
	 * @author chenhui on 20121206
	 * @param int $cityId 城市ID
	 * @return array/bool 城市越野信息(1D)
	 */
	public function getRow($cityId) {
		$row = $this->get($cityId);
		if (!empty($row)) {
			return $row;
		} else {
			$horseInfo = array(
				'city_id' => $cityId,
				'horse_date' => date('Ymd'),
				'cycle_no' => 1,
				'encour_times' => 0,
				'horse1' => 0,
				'horse2' => 0,
				'horse3' => 0,
				'horse4' => 0,
				'horse5' => 0,
				'horse6' => 0,
				'horse7' => 0,
				'horse_all' => 0,
				'milpay_total' => 0, //暂时保留
				'create_at' => time(),
			);
			$initRet = $this->insert($horseInfo);
			if ($initRet) {
				return $horseInfo;
			} else {
				return false;
			}
		}
	}

	/**
	 * 获取全部已投注城市ID
	 * @author chenhui on 20121229
	 * @param string $newHorseNo 胜利马编号
	 * @param int $cycleNo 本日第几场
	 * @return array/bool 投注数据
	 */
	public function getAllCityAward($newHorseNo, $nowDate, $cycleNo) {
		$params = array(
			'horse_date' => $nowDate,
			'cycle_no' => $cycleNo,
			$newHorseNo => array('>', 0),
		);
		$rows = $this->getsBy($params);

		return $rows;

	}

	/** 获取某次比赛已投注玩家数据 */
	public function getRows($nowDate, $cycleNo) {
		$params = array(
			'horse_date' => $nowDate,
			'cycle_no' => $cycleNo,
			'horse_all' => array('>', 0),
		);
		$rows = $this->getsBy($params);
		return $rows;

	}

}

?>