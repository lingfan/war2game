<?php

class O_Liveness implements O_I {
	private $_num = array();
	private $_date = array();
	private $_list = array();
	private $_change = false;
	private $_sync = array();
	public $base = array();
	public $nowTime = 0;
	public $nowDate = 0;

	public function __construct(O_Player $objPlayer) {
		$extraInfo = $objPlayer->getCityExtra();
		$livenessData = array();
		if (!empty($extraInfo['liveness_list'])) {
			$livenessData = json_decode($extraInfo['liveness_list'], true);
		}

		$this->nowTime = time();
		$this->nowDate = date('Ymd');
		$this->_num = isset($livenessData[0]) ? $livenessData[0] : 0;
		$this->_date = isset($livenessData[1]) ? $livenessData[1] : $this->nowDate;
		$this->_list = isset($livenessData[2]) ? $livenessData[2] : array();
		if ($this->_date != $this->nowDate) {
			$this->_list = array();
			$this->_date = $this->nowDate;
			$this->_change = true;
		}

		$this->base = M_Config::getVal('activeness_list');
	}

	public function incr($num = 0) {
		$this->_num -= $num;
		$this->_num = max(0, $this->_num);
		$this->_sync['num'] = $this->_num;
		$this->_change = true;
		return $this->_num;
	}


	public function check($type, $lv = 0) {
		$ret = 0;
		if (isset($this->base[$type]) &&
			$this->base['start'] < $this->nowTime &&
			$this->nowTime < $this->base['end']
		) {
			$addPoint = $maxPoint = 0;

			if ($lv) { //array(分数,最大)
				if (isset($this->base[$type][0][$lv])) {
					$addPoint = $this->base[$type][0][$lv];
					$maxPoint = $this->base[$type][1];
				}
			} else { //消灭法西斯 和 攻击玩家 array(分数,最大)
				list($addPoint, $maxPoint) = $this->base[$type];
			}

			$pass = true;
			if (!empty($this->base[$type][2])) {
				$odds = intval($this->base[$type][2]);
				$pass = B_Utils::odds($odds);
			}


			if ($pass) {
				$curPoint = isset($this->_list[$type]) ? intval($this->_list[$type]) : 0;
				$newPoint = $curPoint + $addPoint;

				if ($newPoint <= $maxPoint) {
					$this->_list[$type] = $newPoint;
					$this->_num += $addPoint;
					$ret = $this->_num;

					$this->_sync['num'] = $this->_num;
					$this->_change = true;
				}
			}
		}
		return $ret;
	}

	public function get() {
		return array($this->_num, $this->_date, $this->_list);
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