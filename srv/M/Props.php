<?php

/**
 * 道具模型层    Created on 2011-4-9
 */
class M_Props {
	/** 内政 */
	const TYPE_INNER = 1;
	/** 军官 */
	const TYPE_HERO = 2;
	/** 宝物 */
	const TYPE_TREA = 3;
	/** 战斗 */
	const TYPE_WAR = 4;
	/** 图纸 */
	const TYPE_DRAW = 5;
	/** 材料 */
	const TYPE_STUFF = 6;
	/** 道具类型 */
	static $type = array(
		self::TYPE_INNER => '内政',
		self::TYPE_HERO  => '军官',
		self::TYPE_TREA  => '宝物',
		self::TYPE_WAR   => '战斗',
		self::TYPE_DRAW  => '图纸',
		self::TYPE_STUFF => '材料',
	);

	/** 资源编号=>道具效果编号 */
	static $resCodePropsEffect = array(
		T_App::RES_FOOD_NAME => 'FOOD_INCR_YIELD',
		T_App::RES_OIL_NAME  => 'OIL_INCR_YIELD',
		T_App::RES_GOLD_NAME => 'GOLD_INCR_YIELD',
	);

	/** 城市正在使用道具(有持续时间)信息(效果) 字段 */
	static $cityPropsUseFields = array(
		'GOLD_INCR_YIELD',
		'FOOD_INCR_YIELD',
		'OIL_INCR_YIELD',
		'HERO_WAR_EXP_INCR',
		'ARMY_INCR_ATT',
		'ARMY_INCR_DEF',
		'ARMY_INCR_SPEED',
		'ARMY_WAR_EXP_INCR',
		'ARMY_RELIFE',
		'AVOID_WAR',
		'AVOID_HOLD'
	);

	/** 未绑定状态 */
	const UNBINDING = 0;
	/** 绑定状态 */
	const BINDING = 1;
	static $bindingType = array(
		self::UNBINDING => '未绑定',
		self::BINDING   => '已绑定',
	);

	/** 免战道具 功能点 可攻击他人 */
	const WAR_ATK_SB = 1;
	/** 免战道具 功能点 可被他人攻击 */
	const WAR_ATK_BY = 2;
	/** 免战道具 功能点 可占领他人 */
	const WAR_HOLD_SB = 4;
	/** 免战道具 功能点 可被他人占领 */
	const WAR_HOLD_BY = 8;

	/** 可直接使用道具效果对应函数 */
	static $EffectUse = array(
		'GOLD_INCR_YIELD'   => '_effectIncrGoldYield', //增加金钱产量
		'FOOD_INCR_YIELD'   => '_effectIncrFoodYield', //增加食物产量
		'OIL_INCR_YIELD'    => '_effectIncrOilYield', //增加石油产量
		'HERO_WAR_EXP_INCR' => '_effectIncrHeroExpAdd', //增加军官战斗获得经验值
		'ARMY_INCR_ATT'     => '_effectIncrArmyAtk', //增加所有部队攻击
		'ARMY_INCR_DEF'     => '_effectIncrArmyDef', //增加所有部队防御
		'ARMY_INCR_HP'      => '_effectIncrArmyHP', //增加所有部队生命
		'ARMY_INCR_SPEED'   => '_effectIncrArmySpeed', //增加所有部队速度
		'ARMY_WAR_EXP_INCR' => '_effectIncrArmyExpAdd', //增加士兵战斗获得熟练度
		'ARMY_RELIFE'       => '_effectRelifeArmy', //复活士兵
		'AVOID_WAR'         => '_effectAvoidWar', //免进攻道具
		'AVOID_HOLD'        => '_effectAvoidHold', //免占领道具
		'REMOVE_AVOID_WAR'  => '_effectCleanAvoidWar', //消除免战道具
		'VIP_FUNCTION'      => '_effectVipFunc', //VIP道具卡
		'NEWBIE_PACKS'      => '_effectUnpack', //礼包
		'HERO_CARD'         => '_effectHeroCard', //军官卡
	);

	/** 初级迁城令 */
	const MOVE_JUNIOR = 1;
	/** 中级迁城令 */
	const MOVE_MIDDLE = 2;
	/** 高级迁城令 */
	const MOVE_HIGH = 3;


	// 元首改名道具ID
	const MODIFY_CITY_NAME_PROPS_ID = 2001;
	// 军团改名道具ID
	const MODIFY_UNION_NAME_PROPS_ID = 874;
	// 转生奖章道具ID
	const HERO_RECYCLE_PROPS_ID = 856;


	/** 获取军团改名道具ID chenhui on 20130320 */
	static public function getModifyUnionNameId() {
		$propsId = 2002; //中文版军团改名道具ID
		if ('tw' == ETC_NO) {
			$propsId = 2002; //繁体版军团改名道具ID
		} else if ('vn' == ETC_NO) {
			$propsId = 2002; //越南版军团改名道具ID
		}
		return $propsId;
	}

	/** 获取转生奖章道具ID chenhui on 20130320 */
	static public function getRecycleId() {
		$propsId = 2000; //中文版转生奖章道具ID
		if ('tw' == ETC_NO) {
			$propsId = 2000; //繁体版转生奖章道具ID
		} else if ('vn' == ETC_NO) {
			$propsId = 2000; //越南版转生奖章道具ID
		}
		return $propsId;
	}

	/**
	 * 根据道具ID获取道具基础信息
	 * @author chenhui    on 20110414
	 * @param int props_id 道具ID
	 * @return array 道具基础信息(一维数组)
	 */
	static public function baseInfo($propsId) {
		$apcKey = T_Key::BASE_PROPS . '_' . $propsId;
		$info   = B_Cache_APC::get($apcKey);
		if (empty($info)) {
			$info = B_DB::instance('BaseProps')->get($propsId);
			APC::set($apcKey, $info);
		}
		return $info;
	}

	static public function cleanBaseInfo($propsId) {
		$apcKey = T_Key::BASE_PROPS . '_' . $propsId;
		APC::del($apcKey);
	}

	/**
	 * 根据效果标签获取基础道具ID数组
	 * @author chenhui on 20120723
	 * @param string $effect_txt 效果标签
	 * @return array 道具ID数组
	 */
	static public function getBaseIdByEffect($effect_txt) {
		$ret      = array();
		$baseList = M_Base::propsAll();
		if (!empty($baseList) && is_array($baseList)) {
			foreach ($baseList as $baseInfo) {
				($effect_txt == $baseInfo['effect_txt']) && $ret[] = $baseInfo['id'];
			}
		}
		return $ret;
	}


	/**
	 * 修正道具中被删除的数据
	 * @author huwei
	 * @param array $ret
	 * @return void
	 */
	static private function _fixDelPropsData(&$ret) {
		$list         = B_DB::instance('BaseProps')->all();
		$tmp          = json_decode($ret['props'], true);
		$newPropsData = array();
		foreach ($tmp as $key => $val) {
			if (isset($list[$key]) && isset($val[M_Props::UNBINDING]) && $val[M_Props::UNBINDING] > 0) {
				$newPropsData[$key] = $val;
			}
		}
		$ret['props'] = json_encode($newPropsData);
	}


	/**
	 * 获取某城市资源道具加成值
	 * @author huwei on 20111202
	 * @param array $resPropsArr 资源道具
	 * @return array 资源建筑加成值
	 */
	static public function getAllResPropsAdd($cityPropsArr) {
		$resAdd = array('gold_grow' => 0, 'food_grow' => 0, 'oil_grow' => 0);

		foreach ($resAdd as $k => $v) {
			if (isset($cityPropsArr[$k])) {
				$resAdd[$k] = $cityPropsArr[$k];
			}
		}
		return $resAdd;
	}

	/**
	 * 根据城市ID更新城市道具信息
	 * @author chenhui on 20110523
	 * @param int $cityId 城市ID
	 * @param array updinfo 要更新的键值对数组
	 * @return bool true/false
	 */
	static public function updateCityProps($cityId, $upInfo, $upDB = true) {
		$ret    = false;
		$cityId = intval($cityId);

		$arr = array();
		foreach ($upInfo as $k => $v) {
			in_array($k, T_DBField::$cityPropsFields) && $arr[$k] = $v;
		}
		if (!empty($arr)) {
			$rc  = new B_Cache_RC(T_Key::CITY_PROPS, $cityId);
			$ret = $rc->hmset($arr, T_App::ONE_DAY);
		}

		if ($ret) {
			$upDB && M_CacheToDB::addQueue(T_Key::CITY_PROPS . ':' . $cityId);
		} else {
			Logger::error(array(__METHOD__, 'Err Update City Props', func_get_args()));
		}
		return $ret;
	}

	/**
	 * 判断某城市某状态道具数量是否大于某值
	 * @author chenhui on 20110607
	 * @param int $cityId 城市ID
	 * @param int $propsid 道具ID
	 * @param int $binding 绑定状态 0未 1绑
	 * @param int $num 某数量
	 * @return bool 是否大于
	 */
	static public function checkCityPropsNum($cityId, $propsid, $binding = self::UNBINDING, $num = 1) {
		$ret    = false;
		$hasNum = $objPlayer->Pack()->getNumByPropsId($propsId);
		if ($hasNum >= $num) {
			$ret = true;
		}

		return $ret;
	}


	/**
	 * 获取某强化石可强化的最大等级
	 * @author chenhui on 20110607
	 * @param int $propsId 道具ID
	 * @return false/array('min'=>1,'max'=>30)
	 */
	static public function getMaxStrongGrade($propsId) {
		$ret       = array('min' => 0, 'max' => 0);
		$propsInfo = M_Props::baseInfo($propsId);
		if (!empty($propsInfo) && 'EQUI_STRONG_GD' == $propsInfo['effect_txt']) {
			$stoneLev           = intval($propsInfo['effect_val']);
			$props_strong_level = M_Config::getVal('props_strong_level');
			$ret                = isset($props_strong_level[$stoneLev]) ? $props_strong_level[$stoneLev] : $ret;
		}
		return $ret;
	}

	/**
	 * 获取幸运符 增加装备强化成功率的值
	 * @author chenhui on 20110607
	 * @param int $propsId 道具ID
	 * @return false/int (false或百分比值)
	 */
	static public function getLuckyRate($propsId) {
		$ret       = false;
		$propsInfo = M_Props::baseInfo($propsId);
		if (!empty($propsInfo) && 'EQUI_INCR_STRONG' == $propsInfo['effect_txt']) {
			$tmpVal = explode(',', $propsInfo['effect_val']);
			$ret    = $tmpVal[0];
		}
		return $ret;
	}

	/**
	 * 获取幸运符 增加装备强化失败后增加的幸运池值
	 * @author chenhui on 20110901
	 * @param int $propsId 道具ID
	 * @return false/int (false或百分比值)
	 */
	static public function getLuckyPool($propsId) {
		$ret       = false;
		$propsInfo = M_Props::baseInfo($propsId);
		if (!empty($propsInfo) && 'EQUI_INCR_STRONG' == $propsInfo['effect_txt']) {
			$tmpVal = explode(',', $propsInfo['effect_val']);
			$ret    = $tmpVal[1];
		}
		return $ret;
	}

	/**
	 * 获取从技能书中学习到的军官技能ID
	 * @author chenhui on 20110613
	 * @param int $propsId 道具ID
	 * @return 军官技能ID
	 */
	static public function getSkillFromBook($propsId) {
		$ret       = 0;
		$flag      = false;
		$maxRate   = 0;
		$propsInfo = M_Props::baseInfo($propsId);
		if (!empty($propsInfo) && 'SKILL_RAND_LEARN' == $propsInfo['effect_txt']) {
			$arrEffectVal = json_decode($propsInfo['effect_val'], true);
			foreach ($arrEffectVal as $skillId => $rate) {
				if (B_Utils::odds($rate)) {
					$ret  = $skillId;
					$flag = true;
					break;
				}
				$maxRate = max($maxRate, $rate);
			}
			if (!$flag) {
				$ret = array_search($maxRate, $arrEffectVal);
			}
		}
		return $ret;
	}

	/**
	 * 根据道具ID和效果获取效果值(针对一次性使用有一定效果的道具)
	 * @author chenhui on 20110613
	 * @param int $propsId 道具ID
	 * @param string $effect_txt 道具效果编号
	 * @return int 效果值
	 */
	static public function getPropsItemVal($propsId, $effect_txt) {
		$ret = 0;
		if (!empty($propsId) && !empty($effect_txt)) {
			$propsInfo = M_Props::baseInfo($propsId);
			if (!empty($propsInfo) && $effect_txt == $propsInfo['effect_txt']) {
				$ret = $propsInfo['effect_val'];
			}
		}
		return $ret;
	}

	/**
	 * 判断某玩家此时能否使用免进攻道具
	 * @author chenhui on 20111104
	 * @param int $cityId 城市ID
	 * @return bool
	 */
	static public function canUseNoWarProps($cityInfo) {
		$errno = '';
		if (isset($cityInfo['avoid_war_cd_time']) && $cityInfo['avoid_war_cd_time'] > time()) {
			$errno = T_ErrNo::AVOID_WAR_CD_HOLD;
		}

		return $errno;
	}


	/**
	 * 判断某玩家此时能否使用免占领道具(免战道具CD时间)
	 * @author chenhui on 20111104
	 * @param int $cityId 城市ID
	 * @return bool
	 */
	static public function canUseNoHoldProps($objPlayer) {
		$errno = '';
		if ($objPlayer->City()->avoid_war_cd_time > time()) {
			$errno = T_ErrNo::AVOID_WAR_CD_HOLD;
		} else if (M_March_Hold::exist($objPlayer->City()->pos_no)) //如果被占领则不能使用免占领道具
		{
			$errno = T_ErrNo::AVOID_WAR_CITY_HOLD;
		} else if (M_ColonyCity::isHadCityColony($objPlayer->City()->id)) {
			$errno = T_ErrNo::AVOID_WAR_HOLD_CITY;
		}

		return $errno;
	}

	/**
	 * 判断某玩家此时能否使用消除免战道具
	 * @author chenhui on 20111104
	 * @param int $cityInfo 城市数据
	 * @return bool
	 */
	static public function canUseRemoveNoWar($cityInfo) {
		$ret = false;
		if (!empty($cityInfo['avoid_war_cd_time']) && $cityInfo['avoid_war_cd_time'] > time()) {
			$ret = true;
		}
		return $ret;
	}




	/**********道具模块管理后台所需接口*******************/
	/** 删除道具 基础 数据 缓存 */
	static public function delPropsCache() {
		return B_Cache_APC::del(T_Key::BASE_PROPS); //删除缓存，用完注释
	}

	/**
	 * 获取道具ID=>名字的数组
	 * @author chenhui on 20110820
	 * @return array ID=>name
	 */
	static public function getPropsIdName() {
		$arrBaseInfo = M_Base::propsAll();
		$arrIdName   = array();
		if (!empty($arrBaseInfo) && is_array($arrBaseInfo)) {
			foreach ($arrBaseInfo as $id => $baseInfo) {
				$arrIdName[$id] = $baseInfo['name'];
			}
		}
		return $arrIdName;
	}


}

?>