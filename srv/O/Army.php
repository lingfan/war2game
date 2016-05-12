<?php

class O_Army implements O_I {
	const NUM = 5;

	private $_data = array();
	private $_sync = array();
	private $_change = false;
	public $base = array();
	private $_objPlayer = null;

	public function __construct(O_Player $objPlayer) {
		$this->_objPlayer = $objPlayer;
		$extraInfo = $objPlayer->getCityExtra();
		$armyList = array();
		if (!empty($extraInfo['team_list'])) {
			$armyList = json_decode($extraInfo['army_list'], true);
		}

		$this->_init($armyList);

		foreach (M_Army::$type as $armyId => $v) {
			$this->base[$armyId] = M_Army::baseInfo($armyId);
		}
	}

	private function _init($armyList) {
		$ret = array();
		foreach (M_Army::$type as $armyId => $v) {
			//array(数量,等级,经验)
			$tmp = array(0, 0, 0);
			if (!empty($armyList[$armyId]) && count($armyList[$armyId]) == 3) {
				$tmp = $armyList[$armyId];
			}
			$ret[$armyId] = $tmp;
		}
		$this->_data = $ret;
		$this->_change = true;
	}


	public function calcRecruitCost($base, $lv) {
		return ceil($base * pow(1.1, $lv));
	}

	/**
	 * 根据兵种等级获取所需的熟练度
	 *
	 * @param int $lv 兵种等级
	 * @return int 所需熟练度值
	 */
	public function calcExp($lv) {
		return ceil($lv * $lv * ($lv + 1) * (2 * $lv * $lv + 1) / 6 * 100);
	}


	public function addNum($armyId, $num) {
		$this->_data[$armyId][0] += $num;
		$this->_change = true;
		return $this->_data[$armyId][0];
	}

	public function setNum($armyId, $num) {
		$this->_data[$armyId][0] = $num;
		$this->_change = true;
		return $num;
	}

	public function addLv($armyId, $num) {
		$this->_data[$armyId][1] += $num;
		$this->_change = true;
		return $this->_data[$armyId][1];
	}

	public function addExp($armyId, $exp) {
		$this->_data[$armyId][2] += $exp;
		$this->_change = true;
		return $this->_data[$armyId][2];
	}

	/**
	 * @return array [数量,等级,经验]
	 */
	public function getById($armyId) {
		return $this->_data[$armyId];
	}

	public function get() {
		return $this->_data;
	}

	public function isChange() {
		return $this->_change;
	}

	public function getSync() {
		$ret = $this->_sync;
		$this->_sync = array();
		return $ret;
	}

	public function toFront() {
		$ret = array();
		foreach ($this->_data as $id => $val) {
			array_unshift($val, $id);
			$ret[] = $val;
		}
		return $ret;
	}

	public function toData() {
		$ret = array();
		if (is_array($this->_data)) {
			foreach ($this->_data as $armyId => $val) {
				$ret[$armyId] = array(
					'army_id' => $armyId,
					'number' => $val[0],
					'level' => $val[1],
					'exp' => $val[2],
				);
			}
		}
		return $ret;
	}

	/**
	 * 增加城市兵种数量减少空闲人口
	 * @author chenhui on 20120312
	 * @param int $cityId 城市ID
	 * @param int $armyId 兵种ID
	 * @param int $needNum 需要兵种数量
	 * @return bool
	 */
	public function makeCityArmy($armyId, $needNum) {
		$cityId = $this->_objPlayer->City()->id;
		$armyId = intval($armyId);
		$needNum = intval($needNum);
		if ($cityId > 0 && isset(M_Army::$type[$armyId]) && $needNum > 0) {
			$freePeople = $this->_objPlayer->City()->max_people - $this->_objPlayer->City()->cur_people; //空闲人口
			if ($freePeople >= 0) {
				//数量,等级,经验
				list($num, $lv, $exp) = $this->getById($armyId);
				//Logger::debug(array(__METHOD__,'getCityArmy',func_get_args()));
				$baseArmyInfo = M_Army::baseInfo($armyId);
				$needPeople = $needNum * $baseArmyInfo['cost_people'];

				if ($needPeople <= $freePeople) { //需要人口数 大于 空闲人口数
					$newArmyNum = $this->addNum($armyId, $needNum); //兵种新数量
					//新已占用人口
					$this->_objPlayer->City()->cur_people += $needPeople;
				}
			}
		}
	}
}