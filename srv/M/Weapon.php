<?php

/**
 * 武器模型层
 */
class M_Weapon {
	static private $_List = array();
	/** 默认特殊武器槽开启数量 */
	const DEF_SPEC_NUM = 2;

	/** 常规武器 */
	const COMMON = 0;
	/** 特殊武器 */
	const SPECIAL = 1;

	/** 非NPC武器 */
	const NOTNPC = 0;
	/** 是NPC武器 */
	const NPC = 1;

	/** 步行类 */
	const MOVE_FOOT = 1;
	/** 车辆类 */
	const MOVE_CAR = 2;
	/** 飞行类 */
	const MOVE_FLY = 3;
	/** 航海类 */
	const MOVE_SEA = 4;
	/** 武器(兵种)移动类型 */
	static $moveType = array(
		self::MOVE_FOOT => '步行类',
		self::MOVE_CAR  => '车辆类',
		self::MOVE_FLY  => '飞行类',
		self::MOVE_SEA  => '航海类',
	);

	/** 战场展示 单个 */
	const WAR_SHOW_ONE = 1;
	/** 战场展示 群体 */
	const WAR_SHOW_MULTI = 2;
	/** 战场展示类型 */
	static $showType = array(
		self::WAR_SHOW_ONE   => '单个展示',
		self::WAR_SHOW_MULTI => '群体展示',
	);

	/**
	 * 根据武器ID获取武器基础信息
	 * @author chenhui    on 20110413
	 * @param int weaponid 武器ID
	 * @return array 武器基础信息(一维数组)
	 */
	static public function baseInfo($weaponid) {
		$apcKey = T_Key::BASE_WEAPON . '_' . $weaponid;
		$info   = B_Cache_APC::get($apcKey);
		if (empty($info)) {
			$info = B_DB::instance('BaseWeapon')->get($weaponid);
			APC::set($apcKey, $info);
		}
		return $info;
	}


	/**
	 * 同步最新的某城市某常规武器至前端接口
	 * @author chenhui on 20110813
	 * @param int $cityId 城市ID
	 * @param int $propsId 道具ID
	 */
	static public function syncNormalWeapon2Front($cityId, $weaponId) {
		if (!empty($cityId) && !empty($weaponId)) {
			$msRow = array(
				$weaponId => M_Sync::ADD
			);
			M_Sync::addQueue($cityId, M_Sync::KEY_WEAPON_NORMAL, $msRow);
		}
	}

	/**
	 * 获取特殊武器ID=>名字的数组
	 * @author chenhui on 20110820
	 * @return array ID=>name
	 */
	static public function getSpecialIdName() {
		$arrBaseInfo = M_Base::weaponAll();
		$arrIdName   = array();
		if (!empty($arrBaseInfo) && is_array($arrBaseInfo)) {
			foreach ($arrBaseInfo as $weaponId => $baseInfo) {
				if (self::SPECIAL == $baseInfo['is_special']) {
					$arrIdName[$weaponId] = $baseInfo['name'];
				}
			}
		}
		return $arrIdName;
	}

}

?>