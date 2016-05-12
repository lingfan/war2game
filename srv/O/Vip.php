<?php

class O_Vip implements O_I {

	private $_data = array();
	private $_change = false;
	private $_sync = array();
	private $_now = 0;
	private $_cityId = 0;

	public function __construct(O_Player $objPlayer) {
		$extraInfo = $objPlayer->getCityExtra();
		$vipEffect = array();
		if (!empty($extraInfo['vip_effect'])) {
			$vipEffect = json_decode($extraInfo['vip_effect'], true);
		}

		$this->_cityId = $objPlayer->City()->id;
		$this->_now = time();
		$this->_init($vipEffect);
	}

	public function _init($vipEffect) {
		if (!empty($vipEffect)) {
			foreach ($vipEffect as $funCode => $arr) {
				if (!empty($arr) && $arr[1] < $this->_now) { //过滤已过期VIP功能
					unset($vipEffect[$funCode]);
				}
			}
		}
		$this->_data   = $vipEffect;
		$this->_change = true;
	}

	/**
	 * 获取未过期VIP功能值
	 *
	 *
	 * @param string $key 功能标签
	 * @return int VIP功能值
	 */
	public function getVal($key) {
		$ret = 0;
		if (isset($this->_data[$key])) {
			$ret = $this->_data[$key][0];
		}
		return $ret;
	}

	/**
	 * 获取未过期VIP功能值
	 *
	 *
	 * @param string $key 功能标签
	 * @return int VIP功能值
	 */
	public function getTime($key) {
		$ret = 0;
		if (isset($this->_data[$key])) {
			$ret = $this->_data[$key][1];
		}
		return $ret;
	}

	public function setKey($key, $num, $time) {
		$this->_data[$key] = array($num, $time);
		$this->_change     = true;
	}

	public function isExist($key) {
		return isset($this->_data[$key]) ? true : false;
	}

	/**
	 * 获取某城市资源VIP加成值
	 *
	 *
	 * @return array 资源VIP加成值
	 */

	public function getResAdd() {
		$ret = array(T_App::RES_GOLD => 0, T_App::RES_FOOD => 0, T_App::RES_OIL => 0);

		$resFilterArr = array(
			'GOLD_INCR_YIELD' => T_App::RES_GOLD,
			'FOOD_INCR_YIELD' => T_App::RES_FOOD,
			'OIL_INCR_YIELD'  => T_App::RES_OIL
		);

		foreach ($resFilterArr as $k => $v) {
			$ret[$v] = $this->getVal($k);;
		}

		return $ret;
	}

	/**
	 * 获取城市影响战斗的VIP加成
	 * @author chenhui on 20111209
	 * @param array $cityVipFunc
	 * @return array array('A'=>0,'D'=>0,'L'=>0)
	 */
	public function getBattleAdd() {
		$vipAdd = array('A' => 0, 'D' => 0, 'L' => 0, 'ArmyRelifeAdd' => 0);
		if (!empty($cityVipFunc)) {
			$vipAdd['A']             = $this->getVal('ARMY_INCR_ATT');
			$vipAdd['D']             = $this->getVal('ARMY_INCR_DEF');
			$vipAdd['ArmyRelifeAdd'] = $this->getVal('ARMY_RELIFE');
		}
		return $vipAdd;
	}

	public function get() {
		//Logger::debug(array(__METHOD__, $this->_cityId, $this->_data));
		return $this->_data;
	}

	public function isChange() {
		return $this->_change;
	}

	public function getSync() {
		$ret         = $this->_sync;
		$this->_sync = array();
		return $ret;
	}


}

?>