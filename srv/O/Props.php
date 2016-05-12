<?php

class O_Props implements O_I {
	private $_data = array();
	private $_change = false;
	private $_sync = array();
	private $_now = 0;

	/**
	 * @var O_Player
	 */
	private $_objPlayer = null;

	public function __construct(O_Player $objPlayer) {
		$this->_now       = time();
		$this->_objPlayer = $objPlayer;
		$extraInfo        = $objPlayer->getCityExtra();
		$useProps         = array();
		if (!empty($extraInfo['props_use'])) {
			$useProps = json_decode($extraInfo['props_use'], true);
		}

		$this->_data = !empty($useProps) ? $useProps : array();
		$this->_checkUseExpire();
	}

	public function get() {
		return $this->_data;
	}

	public function getVal($key) {
		$ret = false;
		if (!empty($this->_data[$key])) {
			$ret = $this->_data[$key];
		}
		return $ret;
	}

	public function getIdByEffect() {

	}

	/**
	 * 根据城市ID和道具效果获取效果值(未使用相关效果道具则值为0)(针对使用后效果维持一段时间的道具)
	 *
	 * @param string $key
	 */
	public function getEffectVal($key) {

		$ret = false;
		if (!empty($this->_data[$key])) {
			$ret = $this->_data[$key]['effect_val'];
		}
		return $ret;
	}

	public function isChange() {
		return $this->_change;
	}

	public function getSync() {
		$ret         = $this->_sync;
		$this->_sync = array();
		return $ret;
	}

	/**
	 * 检查道具效果过期
	 *
	 */
	private function _checkUseExpire() {
		$expireNum = 0;
		$ret       = array();
		foreach ($this->_data as $k => $tmp) {
			if (!empty($tmp)) {
				if ($tmp['end_time'] > $this->_now) {
					$ret[$k] = $tmp;
				} else {
					$expireNum++;
				}
			}
		}
		if ($expireNum) {
			$this->_data   = $ret;
			$this->_change = true;
		}
	}

	public function getResAdd() {
		$ret = array(T_App::RES_GOLD => 0, T_App::RES_FOOD => 0, T_App::RES_OIL => 0);

		$resFilterArr = array(
			'GOLD_INCR_YIELD' => T_App::RES_GOLD,
			'FOOD_INCR_YIELD' => T_App::RES_FOOD,
			'OIL_INCR_YIELD'  => T_App::RES_OIL
		);

		foreach ($this->_data as $ukey => $uval) {
			if (isset($resFilterArr[$uval['effect_txt']])) {
				$v       = $resFilterArr[$uval['effect_txt']];
				$ret[$v] = $uval['effect_val'];
			}
		}
		return $ret;
	}


	public function toFront() {
		$ret = array();
		foreach ($this->_data as $ukey => $uval) {
			$ret[] = array($uval['effect_txt'], $uval['effect_val'], M_Formula::calcCDTime($uval['end_time'])); //效果编号,效果值,到期剩余秒数
		}
		return $ret;
	}


	/**
	 * 判断某玩家此时能否使用军官加经验道具
	 * @return bool
	 */
	public function canUseHeroExpProps() {
		$ret = false;
		$num = $this->getVal('HERO_WAR_EXP_INCR');
		if (empty($num)) {
			$ret = true;
		}

		return $ret;
	}

	/**
	 * 同步最新的某城市某道具使用效果及时间至前端接口
	 * @author chenhui on 20110815
	 * @param int $cityId 城市ID
	 * @param int $propsId 道具ID
	 */
	public function syncPropsEffect2Front($propsInfo) {
		if (!empty($propsInfo)) {
			$effect_txt = $propsInfo['effect_txt'];
			if (isset(self::$EffectUse[$effect_txt])) {
				$useInfo = $this->getVal($effect_txt);
				if (!empty($useInfo) && isset($useInfo['effect_val'])) {
					$this->_sync = array(
						$effect_txt => array($useInfo['effect_val'], M_Formula::calcCDTime($useInfo['end_time']))
					);
				}
			}
		}
	}

	/**
	 * 判断某城市是否处于免战状态
	 * @author chenhui on 20111104
	 * @param int $cityId 城市ID
	 * @return bool false正常/true免战中
	 */
	public function isAvoidWar() {
		$ret      = false;
		$propsUse = $this->getVal('AVOID_WAR');
		if (!empty($propsUse['end_time']) && $propsUse['end_time'] > time()) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 判断某城市是否处于免占领状态
	 * @author huwei on 20121126
	 * @param int $cityId 城市ID
	 * @return bool false正常/true免战中
	 */
	public function isAvoidHold() {
		$ret      = false;
		$propsUse = $this->getVal('AVOID_HOLD');
		if (!empty($propsUse['end_time']) && $propsUse['end_time'] > time()) {
			$ret = true;
		}
		return $ret;
	}


	public function call($propsInfo) {
		$ret       = false;
		$effectVal = $propsInfo['effect_txt'];
		if (isset(M_Props::$EffectUse[$effectVal])) {
			$funcName = M_Props::$EffectUse[$effectVal];
			$ret      = $this->$funcName($propsInfo);

			$this->_change = true;
			if (in_array($effectVal, array('GOLD_INCR_YIELD', 'FOOD_INCR_YIELD', 'OIL_INCR_YIELD'))) {
				$this->_objPlayer->Res()->upGrow('props');
			}
		}

		return $ret;
	}

	/* --根据道具效果代码调用函数-----开始----------------------- */
	/** 持续一段时间的道具共同处理 return bool */
	private function _addPeriod($base) {
		$ret     = false;
		$nowTime = time();
		if (isset(M_Props::$EffectUse[$base['effect_txt']])) {
			$effectTxt = $base['effect_txt'];

			if (!empty($this->_data[$effectTxt])) {
				$this->_data[$effectTxt]['end_time'] = $this->_data[$effectTxt]['end_time'] + $base['effect_time'];
			} else {
				if ($base['effect_txt'] == 'AVOID_HOLD') {
					$this->_data['AVOID_WAR'] = array(
						'effect_txt' => 'AVOID_WAR',
						'effect_val' => $base['effect_val'],
						'end_time'   => $nowTime + $base['effect_time'],
						'create_at'  => $nowTime,
					);
					$this->_sync['AVOID_WAR'] = array($this->_data['AVOID_WAR']['effect_val'], M_Formula::calcCDTime($this->_data['AVOID_WAR']['end_time']));
				}

				$this->_data[$effectTxt] = array(
					'effect_txt' => $base['effect_txt'],
					'effect_val' => $base['effect_val'],
					'end_time'   => $nowTime + $base['effect_time'],
					'create_at'  => $nowTime,
				);
			}
			$this->_sync[$effectTxt] = array($this->_data[$effectTxt]['effect_val'], M_Formula::calcCDTime($this->_data[$effectTxt]['end_time']));

			$ret = true;
		}
		return $ret;
	}

	private function _effectIncrGoldYield($propsInfo) {
		$ret = $this->_addPeriod($propsInfo);
		return $ret;
	}

	private function _effectIncrFoodYield($propsInfo) {
		$ret = $this->_addPeriod($propsInfo);
		return $ret;
	}

	private function _effectIncrOilYield($propsInfo) {
		$ret = $this->_addPeriod($propsInfo);
		return $ret;
	}

	private function _effectIncrHeroExpAdd($propsInfo) {
		$ret = $this->_addPeriod($propsInfo);
		return $ret;
	}

	private function _effectIncrArmyAtk($propsInfo) {
		$ret = $this->_addPeriod($propsInfo);
		return $ret;
	}

	private function _effectIncrArmyDef($propsInfo) {
		$ret = $this->_addPeriod($propsInfo);
		return $ret;
	}

	private function _effectIncrArmyHP($propsInfo) {
		$ret = $this->_addPeriod($propsInfo);
		return $ret;
	}

	private function _effectIncrArmySpeed($propsInfo) {
		$ret = $this->_addPeriod($propsInfo);
		return $ret;
	}

	private function _effectIncrArmyExpAdd($propsInfo) {
		$ret = $this->_addPeriod($propsInfo);
		return $ret;
	}

	private function _effectRelifeArmy($propsInfo) {
		$ret = $this->_addPeriod($propsInfo);
		return $ret;
	}

	private function _effectAvoidWar($propsInfo) //免战道具
	{
		$ret                                         = $this->_addPeriod($propsInfo);
		$new_cd_time                                 = time() + $propsInfo['effect_time'] * 2;
		$this->_objPlayer->City()->avoid_war_cd_time = $new_cd_time;
		return $ret;
	}

	private function _effectAvoidHold($propsInfo) {
		$ret                                         = $this->_addPeriod($propsInfo);
		$new_cd_time                                 = time() + $propsInfo['effect_time'] * 2;
		$this->_objPlayer->City()->avoid_war_cd_time = $new_cd_time;
		return $ret;
	}

	/** 消除免战 */
	private function _effectCleanAvoidWar($propsInfo) {
		$now = time();
		foreach (array('AVOID_WAR', 'AVOID_HOLD') as $val) {
			if (isset($this->_data[$val])) {
				unset($this->_data[$val]);
			}
		}

		return true;
	}


	//VIP道具卡
	private function _effectVipFunc($propsInfo) {
		$this->_objPlayer->City()->vip_level = $propsInfo['effect_val'];
		return true;
	}

	/**
	 * 打开礼包
	 * @author chenhui
	 * @param int $cityId
	 * @param array $propsInfo
	 */
	private function _effectUnpack($propsInfo) {
		$ret      = false;
		$awardArr = M_Award::rateResult($propsInfo['effect_val']);
		if (!empty($awardArr)) {
			$this->_objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Prop);
			$ret = M_Award::toText($awardArr);
		}

		return $ret;
	}

	/**
	 * 使用军官卡
	 * @author huwei
	 * @param int $cityId
	 * @param array $propsInfo
	 */
	private function _effectHeroCard($propsInfo) {
		$awardArr = M_Award::rateResult($propsInfo['effect_val']);
		$this->_objPlayer->City()->toAward($awardArr, Logger::H_ACT_CARD);
		$ret = M_Award::toText($awardArr);
		return $ret;
	}
	/**--根据道具效果代码调用函数-----结束-----------------------*/

}