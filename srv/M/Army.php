<?php

/**
 * 兵种模型层    on 20110411
 */
class M_Army {
	/** 步兵 兵种ID */
	const ID_FOOT = 1;
	/** 炮兵  兵种ID */
	const ID_GUN = 2;
	/** 装甲  兵种ID */
	const ID_ARMOR = 3;
	/** 航空  兵种ID */
	const ID_AIR = 4;
	/** 兵种类型 */
	static $type = array(
		self::ID_FOOT  => '步兵',
		self::ID_ARMOR => '装甲兵',
		self::ID_AIR   => '航空兵',
		self::ID_GUN   => '炮兵',
	);
	/** 兵种升级所需军营等级 */
	static $upgbuild = array(
		self::ID_FOOT  => array(1, 2), //初始需求等级，间隔等级
		self::ID_ARMOR => array(1, 3),
		self::ID_AIR   => array(1, 4),
		self::ID_GUN   => array(1, 4),
	);


	static $rate = array(
		self::ID_FOOT  => 1,
		self::ID_ARMOR => 3,
		self::ID_AIR   => 4,
		self::ID_GUN   => 4,
	);

	/**
	 * 根据兵种ID获取兵种基础信息
	 * @author chenhui    on 20110414
	 * @param int build_id 兵种ID
	 * @return array 兵种基础信息(一维数组)
	 */
	static public function baseInfo($armyId) {
		$list = M_Base::armyAll();
		return isset($list[$armyId]) ? $list[$armyId] : array();
	}




	/**********兵种模块管理后台所需接口*******************/
	/** 删除兵种 基础 数据 缓存 */
	static public function delArmyCache() {
		APC::del(T_Key::BASE_ARMY); //删除缓存，用完注释
	}
}

?>