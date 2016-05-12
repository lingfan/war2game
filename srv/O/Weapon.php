<?php

class O_Weapon implements O_I {
	private $_now = 0;
	private $_data = array();
	private $_sync = array();
	private $_baseSpecialSlot = array();
	private $_change = false;
	private $_cityId = 0;

	public function __construct(O_Player $objPlayer) {
		$cityInfo = $objPlayer->getCityBase();
		$extraInfo = $objPlayer->getCityExtra();
		$vipLv = $cityInfo['vip_level'];

		$weaponList = array();
		if (!empty($extraInfo['weapon_list'])) {
			$weaponList = json_decode($extraInfo['weapon_list'], true);
		}

		$this->_cityId = $cityInfo['id'];

		$this->_now = time();
		if (empty($weaponList)) {
			$weaponList = $this->_init();
			$this->_change = true;
		}
		$this->_data = $weaponList;

		$this->_baseSpecialSlot = $this->_getBaseSlot($vipLv);
		$this->_tempExpire();
	}

	private function _init() {
		$special = array();
		$baseSpecial = $this->_getBaseSlot(0);
		foreach ($baseSpecial as $slotId) { //(武器ID,过期时间)
			$special[$slotId] = array(0, 0);
		}
		//array(基础武器数据[武器ID,...], 特殊武器数据[槽ID:武器ID,...], 临时武器数据[武器ID:过期时间])
		return array('base' => array_values(T_Hero::$army2weapon), 'special' => $special, 'temp' => array());
	}

	/**
	 * vip对应的特殊武器槽ID
	 *
	 * @param int $vipLv
	 * @return array
	 */
	private function _getBaseSlot($vipLv = 0) {
		$vipConf = M_Vip::getVipConfig();
		return explode(',', $vipConf['SPECIAL_SLOTID'][$vipLv]);
	}


	public function getOpenSlotIds($endSlotId) {
		$ret = array();

		$baseConf = M_Config::getVal(); //配置数据
		$maxId = intval($baseConf['weapon_max_special']); //特殊武器槽总数

		$initId = count($this->_data['special']) + 1;
		if ($endSlotId <= $maxId && $initId < $endSlotId) {
			$ret = range($initId, $endSlotId);
		}

		return $ret;
	}

	/**
	 * 可打开的特殊武器槽
	 *
	 * @param int $slotId
	 * @return bool
	 */
	public function canOpenSlot($slotId) {
		$ret = false;
		if (in_array($slotId, $this->_baseSpecialSlot)) {
			$ret = true;
		}
		return $ret;
	}

	public function getBaseSpcialSlot() {
		return $this->_baseSpecialSlot;
	}

	/**
	 * 计算槽花费
	 *
	 * @param array $slotIds
	 * @return int
	 */
	public function getOpenSlotCost($slotIds) {
		$cost = 0;
		foreach ($slotIds as $id) {
			$cost += M_Formula::calcOpenSlotNeed($id);
		}
		return $cost;
	}

	/**
	 * 检测临时武器过期
	 *
	 */
	private function _tempExpire() {
		if ($this->_data['temp']) {
			foreach ($this->_data['temp'] as $wId => $eTime) {
				if ($eTime < $this->_now) {
					unset($this->_data['temp'][$wId]);
					$this->_sendExpireMsg($wId);
					$this->_change = true;
				}
			}
		}
	}

	/**
	 * 发送武器过期邮件
	 * @param $wId
	 *
	 */
	private function _sendExpireMsg($wId) {
		$baseInfo = M_Weapon::baseInfo($wId);
		$content = json_encode(array(T_Lang::WEAPON_EXPRITE, $baseInfo['name']));
		$title = json_encode(array(T_Lang::T_SYS_TIP));
		M_Message::sendSysMessage($this->_cityId, $title, $content);
	}


	/**
	 * 存在特殊武器ID
	 *
	 * @param int $wId
	 * @return bool
	 */
	public function existSpecial($wId) {
		$ret = false;

		foreach ($this->_data['special'] as $slotId => $v) {
			if (!empty($v[0]) && $wId == $v[0] && $this->_now < $v[1]) {
				$ret = true;
				break;
			}
		}
		return $ret;
	}

	/**
	 * 是否已开启武器槽
	 *
	 * @param int $slotId
	 * @return bool
	 */
	public function hasSlot($slotId) {
		return isset($this->_data['special'][$slotId]) ? true : false;
	}

	public function getSpecialWidBySlot($slotId) {
		$wid = 0;
		if ($this->_data['special'][$slotId][0]) {
			$wid = $this->_data['special'][$slotId][0];
		}
		return $wid;
	}

	/**
	 * 判断某武器ID是否存在于某城市
	 * @author huwei
	 * @param int $wId
	 * @return bool
	 */
	public function hasWeapon($wId) {
		$ret = false;
		if ($this->inBaseWeapon($wId) ||
			$this->inSpecialWeapon($wId) ||
			$this->inTempWeapon($wId)
		) {
			$ret = true;
		}
		return $ret;
	}

	public function inBaseWeapon($wId) {
		return in_array($wId, $this->_data['base']);
	}

	public function inSpecialWeapon($wId) {
		return $this->existSpecial($wId);
	}

	public function inTempWeapon($wId) {
		return isset($this->_data['temp'][$wId]);
	}

	/**
	 * 添加常规武器
	 *
	 * @param $wId
	 */
	public function addBase($wId) {
		if ($wId) {
			$this->_data['base'][] = $wId;
			$this->_change = true;
		}
	}

	/**
	 * 添加特殊武器
	 *
	 * @param int $slotId
	 * @param int $wId
	 */
	public function addSpecial($slotId, $wId) {
		if ($slotId) {
			$t = time();
			if (isset($this->_data['special'][$slotId][0]) && $wId == isset($this->_data['special'][$slotId][0])) {
				$t = max($this->_data['special'][$slotId][1], $t);
			}
			$this->_data['special'][$slotId] = array($wId, $t + T_App::ONE_WEEK);
			$this->_change = true;
		}
	}

	/**
	 * 添加临时武器
	 *
	 * @param $wId
	 * @param $baseTime
	 */
	public function addTemp($wId, $baseTime = 0) {
		$t = isset($this->_data['temp'][$wId]) ? $this->_data['temp'][$wId] : $this->_now;
		$this->_data['temp'][$wId] = ceil($t + $baseTime * T_App::ONE_HOUR);
		$this->_change = true;
	}

	public function get() {
		return $this->_data;
	}

	public function getSpecial() {
		$special = array();
		foreach ($this->_data['special'] as $slotId => $v) {
			if (!empty($v[0]) && time() < $v[1]) {
				$special[] = array($slotId, $v[0], $v[1]);
			}
		}
		return $special;
	}


	/**
	 * 武器列表
	 * @return array [
	 * array(基础武器ID,ID...),
	 * array(array(槽ID,特殊武器ID)...),
	 * 最大已开启槽ID
	 * array(array(武器ID,过期时间搓),array(武器ID,过期时间搓),...),
	 * ]
	 *
	 */
	public function toFront() {
		$base = $this->_data['base'];
		$special = $this->getSpecial();
		$num = count($this->_data['special']);
		$temp = array();
		foreach ($this->_data['temp'] as $wId => $eTime) {
			$temp[] = array($wId, $eTime);
		}

		return array($base, $special, $num, $temp);
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