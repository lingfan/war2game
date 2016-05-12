<?php

class O_Tech implements O_I {
	private $_data = array();
	private $_change = false;
	private $_sync = array();

	public function __construct(O_Player $objPlayer) {
		$extraInfo = $objPlayer->getCityExtra();
		$techList = array();
		if (!empty($extraInfo['tech_list'])) {
			$techList = json_decode($extraInfo['tech_list'], true);
		}
		$this->_init($techList);
	}

	private function _init($techList) {
		if (empty($techList)) {
			$techArr = M_Base::techAll();
			$arr = array();
			foreach ($techArr as $key => $val) {
				$arr[$key] = 0; //初始化科技等级为0
			}
			$techList = $arr;
		}
		$this->_data = $techList;
	}

	public function __set($id, $val) {
		if (isset($this->_data[$id])) {
			$this->_data[$id] = $val;
			$this->_sync[$id] = $val;
			$this->_change = true;
		}

	}

	public function __get($id) {
		return isset($this->_data[$id]) ? $this->_data[$id] : 0;
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

	public function getResAdd() {
		$ret = array();

		$filed = array(
			T_App::RES_GOLD => M_Tech::ID_GOLD,
			T_App::RES_FOOD => M_Tech::ID_FOOD,
			T_App::RES_OIL => M_Tech::ID_OIL,
		);

		foreach ($filed as $k => $id) {
			$num = 0;
			if (isset($this->_data[$id])) {
				$num = $this->_calcResAdd($this->_data[$id]);
			}
			$ret[$k] = $num;
		}
		return $ret;
	}

	/**
	 *
	 * @return array [[科技ID,科技等级],...]
	 */
	public function toFront() {
		$ret = array();

		foreach ($this->_data as $id => $lv) {
			$ret[] = array($id, $lv); //科技ID,科技等级
		}
		return $ret;
	}

	public function limitCond($arr) {
		$ok = 0;
		foreach ($arr as $checkId => $checkLv) {
			if (isset($this->_data[$checkId])) {
				if ($this->_data[$checkId] >= $checkLv) {
					$ok++;
				}
			}
		}

		$ret = false;
		if ($ok >= count($arr)) {
			$ret = true;
		}

		return $ret;
	}

	private function _calcResAdd($lv) {
		return $lv * 1;
	}

	/**
	 * 计算兵种对应科技的速度加成
	 * @author huwei
	 * @param int $cityId 城市id
	 * @param int $armyId 军队ID
	 * @return int
	 */
	public function calcArmyTechSpeed($armyId) {
		$type = array(
			M_Army::ID_FOOT => array(M_Tech::ID_FOOT_S, 'FOOT_INCR_SP'),
			M_Army::ID_GUN => array(M_Tech::ID_GUN_S, 'GUN_INCR_SP'),
			M_Army::ID_ARMOR => array(M_Tech::ID_ARMOR_S, 'ARMOR_INCR_SP'),
			M_Army::ID_AIR => array(M_Tech::ID_AIR_S, 'AIR_INCR_SP'),
		);

		$techInfo = $this->get();
		$armyTechId = $type[$armyId][0]; //获取当前对应兵种的速度科技ID
		$armyTechEffect = $type[$armyId][1]; //获取当前对应兵种的速度科技效果定义
		$techLv = $techInfo[$armyTechId]; //获取当前兵种的科技等级

		$techData = M_Tech::baseUpgInfo($armyTechId);
		$techAdd = 0;
		if (isset($techData[$techLv]['effect'])) {
			$techEffData = json_decode($techData[$techLv]['effect'], true);
			$techAdd = isset($techEffData[$armyTechEffect]) ? $techEffData[$armyTechEffect] : 0;
		}
		return intval($techAdd);
	}
}

?>