<?php

class O_Build implements O_I {
	private $_data = array();
	private $_change = false;
	private $_sync = array();
	private $_objPlayer = null;

	public function __construct(O_Player $objPlayer) {
		$this->_objPlayer = $objPlayer;
		$extraInfo = $objPlayer->getCityExtra();
		$buildList = array();
		if (!empty($extraInfo['build_list'])) {
			$buildList = json_decode($extraInfo['build_list'], true);
		}

		$this->_init($buildList);
	}

	private function _init($buildList) {
		$this->_data = $buildList;
	}

	public function set($list) {
		$this->_data = $list;
		$this->_change = true;
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
			T_App::RES_GOLD => M_Build::ID_GOLD_BASE,
			T_App::RES_FOOD => M_Build::ID_FOOD_BASE,
			T_App::RES_OIL => M_Build::ID_OIL_BASE,
		);

		foreach ($filed as $k => $bid) {
			$num = 0;
			if (isset($this->_data[$bid])) {
				foreach ($this->_data[$bid] as $pos => $lv) {
					$num = $this->_calcResAdd($k, $lv);
				}
			}
			$ret[$k] = $num;
		}

		return $ret;
	}

	/**
	 *
	 * @return array [[建筑ID,建筑位置,建筑等级],...]
	 */
	public function toFront($zone) {
		$ret = array();

		$tmp = array();
		foreach ($this->_data as $id => $posList) {
			foreach ($posList as $pos => $lv) {
				$tmp[$pos] = array($id, $pos, $lv, 1); //建筑ID,建筑位置,建筑等级,显示
			}
		}

		$baseConf = M_Config::getVal();
		foreach ($baseConf['build_open'][$zone] as $val) {
			list($tPos, $tBid, $tLv) = $val;
			$ret[] = isset($tmp[$tPos]) ? $tmp[$tPos] : array($tBid, $tPos, 1, 0);
		}

		return $ret;
	}

	public function limitCond($arr) {
		$ok = 0;
		foreach ($arr as $checkId => $checkLv) {
			if (isset($this->_data[$checkId])) {
				foreach ($this->_data[$checkId] as $pos => $lv) {
					if ($lv >= $checkLv) {
						$ok++;
					}
				}
			}
		}

		$ret = false;
		if ($ok >= count($arr)) {
			$ret = true;
		}

		return $ret;
	}

	private function _calcResAdd($type, $buildLv) {
		$rateArr = array(
			T_App::RES_FOOD => 100,
			T_App::RES_OIL => 100,
			T_App::RES_GOLD => 150,
		);

		$rate = isset($rateArr[$type]) ? $rateArr[$type] : 0;

		if ($buildLv <= 1 || $buildLv >= 100) {
			$ret = $rate;
		} else {
			$ret = $this->_calcResAdd($type, $buildLv - 1) + $rate * $buildLv;
		}
		return $ret;
	}

	/**
	 * 获取某城市某建筑的等级
	 * @author chenhui on 20110811
	 * @param int $cityId 城市ID
	 * @param int $buildId 建筑ID
	 * @return int 等级
	 */
	public function getLevel($buildId) {
		$level = 0;
		if (isset($this->_data[$buildId])) {
			$level = current($this->_data[$buildId]);
		}
		return $level;
	}

	/**
	 * 新建、升级、降级 建筑后相关效果的更新
	 * @author chenhui on 20110718
	 * @param int $cityId 城市ID
	 * @param int $buildId 建筑ID
	 * @param int $type 类型 1新 2升 3降
	 */
	public function updateEffect($buildId, $type) {
		$ret = false;
		$arrId = array(M_Build::ID_STORAGE, M_Build::ID_MARKET, M_Build::ID_HOUSE);
		if (in_array($buildId, $arrId)) {
			switch ($buildId) {
				case M_Build::ID_STORAGE:
					$this->_storage($buildId); //仓库效果更新
					break;
				case M_Build::ID_MARKET:
					$this->_market($buildId); //市场效果更新
					break;
				case M_Build::ID_HOUSE: //住宅效果更新
					$this->_house($buildId);
					break;
				default:
					break;
			}
		}
		return $ret;
	}

	/**
	 * 仓库效果更新
	 * @param O_City
	 * */
	private function _storage($buildId) {
		$capacity = M_Build::DEFAULT_STORE; //城市无仓库 默认容量
		$buildList = $this->_data;
		if (isset($buildList[$buildId])) {
			$capacity = 0;
			$arrBuildInfo = $buildList[$buildId];
			foreach ($arrBuildInfo as $pos => $level) {
				$capacity += M_Formula::calcStorageCapaCity(M_Build::STORAGE_ARGS, $level);
			}
		}
		$this->_objPlayer->Res()->upStore($capacity);
	}

	/** 市场效果更新 */
	private function _market($buildId) {
		$this->_objPlayer->City()->market_amount = json_encode(array());
		$msRow = array('market_amount' => $this->_objPlayer->City()->getTradeQuota());
		M_Sync::addQueue($this->_objPlayer->City()->id, M_Sync::KEY_CITY_INFO, $msRow);
	}

	/** 民房容量更新 */
	private function _house($buildId) {
		$capacity = $this->_objPlayer->City()->correctMaxPeople();
		$this->_objPlayer->City()->max_people = $capacity;

		$syncData = array('max_people' => $capacity);
		M_Sync::addQueue($this->_objPlayer->City()->id, M_Sync::KEY_CITY_INFO, $syncData);
	}
}