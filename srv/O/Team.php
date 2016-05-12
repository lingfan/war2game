<?php

class O_Team implements O_I {
	const NUM = 5;
	public $objPlayer = null;

	private $_cityId = 0;
	private $_data = array();
	private $_hasIds = array();
	private $_change = false;
	private $_sync = array();


	public function __construct(O_Player $objPlayer) {
		$this->objPlayer = $objPlayer;

		$cityInfo = $this->objPlayer->getCityBase();
		$extraInfo = $this->objPlayer->getCityExtra();
		$cityLv = $cityInfo['level'];

		$teamList = array();
		if (!empty($extraInfo['team_list'])) {
			$teamList = json_decode($extraInfo['team_list'], true);
		}

		$this->_init($teamList, $cityLv);
	}


	public function hasIds() {
		$ret = array();
		foreach ($this->_data as $no => $val) {

			if (!empty($val[1])) {
				foreach ($val[1] as $id) {
					$ret[$id] = $no;
				}
			}

		}
		return $ret;
	}

	public function get() {
		return $this->_data;
	}

	public function getIdsByNo($no) {
		return isset($this->_data[$no][1]) ? $this->_data[$no][1] : array();
	}

	private function _init($teamList, $lv = 1) {
		for ($i = 1; $i <= self::NUM; $i++) {
			//$open = ($lv >= $i) ? 1 : 0;
			$open = 1;
			$ids = isset($teamList[$i][1]) ? $teamList[$i][1] : array();
			$this->_data[$i] = array($open, $ids); //array(编队,是否开放[1是|0否],军官ID)
		}
	}


	public function set($no, $heroIdArr) {
		$ret = false;
		Logger::debug(array(__METHOD__, $this->_data, $no, $heroIdArr));
		if (isset($this->_data[$no])) {
			if ($this->_data[$no][0] == 1) {
				$this->_data[$no][1] = $heroIdArr;
				//更新存在的军官ID列表
				$ret = true;

				$this->_change = true;
			}

		}
		return $ret;
	}

	public function isExist($heroIds) {
		$ret = false;
		$hasIds = $this->hasIds();
		foreach ($heroIds as $heroId) {
			if (isset($hasIds[$heroId])) {
				$ret = true;
				break;
			}
		}
		return $ret;
	}

	/**
	 * 排除军官在当前编队中
	 *
	 * @param int $no
	 * @param int $heroIds
	 */
	public function exclExist($no, $heroIds) {
		$ret = false;
		$hasIds = $this->hasIds();
		foreach ($heroIds as $heroId) {
			if (isset($hasIds[$heroId]) && $hasIds[$heroId] != $no) {
				$ret = true;
				break;
			}
		}
		return $ret;
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

?>