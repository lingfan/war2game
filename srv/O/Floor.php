<?php

class O_Floor implements O_I {
	const TYPE_NUM = 4;

	private $_times = 0;
	private $_date = '';
	private $_list = array();
	private $_bId = 0;
	private $_change = false;
	private $_sync = array();
	public $baseData = array();
	public $baseCost = array();
	public $nowTime = 0;
	public $nowDate = 0;

	public function __construct(O_Player $objPlayer) {
		$extraInfo = $objPlayer->getCityExtra();
		$floorData = array();
		if (!empty($extraInfo['floor_list'])) {
			$floorData = json_decode($extraInfo['floor_list'], true);
		}

		$this->nowTime = time();
		$this->nowDate = date('Ymd');

		$this->_times = isset($floorData[0]) ? $floorData[0] : 0;
		$this->_date = isset($floorData[1]) ? $floorData[1] : $this->nowDate;

		//级别 => array(是否开放, 当前所在楼层,是否领取奖励)
		$list = isset($floorData[2]) ? $floorData[2] : array();
		$this->_list = $this->_initList($list);
		$this->_bId = isset($floorData[3]) ? $floorData[3] : 0;

		if ($this->_date != $this->nowDate) {
			$this->_times = 0;
			$this->_date = $this->nowDate;
			$this->_change = true;
		}

		$this->baseData = M_Config::getVal('floor_data');
		$this->baseCost = M_Config::getVal('floor_cost');
		//消耗系数
		$this->baseRate = M_Config::getVal('floor_rate');
	}

	private function _initList($data = array()) {
		$tmp = array();
		for ($i = 1; $i <= self::TYPE_NUM; $i++) {
			//array(是否开启, 当前关卡编号,是否已领取奖励))
			$flag = isset($data[$i][0]) ? $data[$i][0] : 0;
			if ($i == 1) {
				$flag = 1;
			}
			$tmp[$i] = array($flag, 1, 0);
		}

		return $tmp;
	}

	public function getData($type) {
		$ret = isset($this->_list[$type]) ? $this->_list[$type] : array();
		return $ret;
	}

	/**
	 * 最大楼层数
	 *
	 * @param int $type
	 */
	public function getMaxNum($type) {
		return count($this->baseData[$type]);
	}

	public function setData($type, $data) {
		$this->_list[$type] = $data;
		$this->_change = true;
	}

	public function getTimes() {
		return intval($this->_times);
	}

	public function incrTimes($num = 1) {
		$this->_times += $num;
		$this->_change = true;
		return $this->_times;
	}

	public function leftFreeTimes() {
		return max($this->baseCost[0] - $this->_times, 0);
	}

	public function calcCost($type, $num) {
		list($isOpen, $curNo, $hadAward) = $this->getData($type);
		$ret = M_Formula::calcStepCost($this->baseCost, $num) * $curNo * $this->baseRate;
		return $ret;
	}

	public function getBId() {
		return intval($this->_bId);
	}

	public function setBId($bId = 0) {
		$this->_bId = $bId;
		$this->_change = true;
	}

	public function get() {
		return array($this->_times, $this->_date, $this->_list, $this->_bId);
	}

	public function isChange() {
		return $this->_change;
	}

	public function getSync() {
		$ret = $this->_sync;
		$this->_sync = array();
		return $ret;
	}
}