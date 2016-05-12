<?php

/**
 * VIP用户模块,所有VIP相关功能、判断等都在此处理
 */
class M_Vip {
	/** VIP商城 资源包(基础道具) */
	const SHOP_RES = 1;
	/** VIP商城 武器图纸(基础道具) */
	const SHOP_DRAW = 2;
	/** VIP商城 装备(基础装备库) */
	const SHOP_EQUI = 3;
	/** 军令 */
	const SHOP_MILORDER = 4;
	/** 活力 */
	const SHOP_ENERGY = 5;
	/** VIP商城类型 */
	static $shop_type = array(
		self::SHOP_RES      => '资源包',
		self::SHOP_DRAW     => '武器图纸',
		self::SHOP_EQUI     => '装备',
		self::SHOP_MILORDER => '军令',
		self::SHOP_ENERGY   => '活力',
	);

	/** 资源编号=>VIP效果编号 */
	static $resCodeVipEffect = array(
		T_App::RES_FOOD_NAME => 'FOOD_INCR_YIELD',
		T_App::RES_OIL_NAME  => 'OIL_INCR_YIELD',
		T_App::RES_GOLD_NAME => 'GOLD_INCR_YIELD',
	);

	/** 是否可移动城内建筑 */
	static $canMoveBuild = array(
		0 => '不可移',
		1 => '可移动',
	);

	/** 军令ID[兼容普通商品] */
	const MILORDER_ID = 1;
	/** VIP玩家每次购买军令数量 */
	const BUY_MILORDER_NUM = 10;
	/* VIP各等级每天可购买军令次数
	 static $conf_buy_milorder = array(
			 0	=> 0,
			 1	=> 0,
			 2	=> 2,
			 3	=> 4,
			 4	=> 6,
			 5	=> 8,
			 6	=> 10,
			 7	=> 12,
			 8	=> 14,
			 9	=> 16,
	 );
	*/
	/** 活力ID[兼容普通商品] */
	const ENERGY_ID = 1;
	/** VIP玩家每次购买活力数量 */
	const BUY_ENERGY_NUM = 10;
	/* VIP各等级每天可购买活力次数
	 static $conf_buy_energy = array(
			 0	=> 0,
			 1	=> 0,
			 2	=> 2,
			 3	=> 4,
			 4	=> 6,
			 5	=> 8,
			 6	=> 10,
			 7	=> 12,
			 8	=> 14,
			 9	=> 16,
	 );
	*/
	/** VIP玩家每次购买VIP资源包数量 */
	const BUY_SHOP_RES_NUM = 1;
	/* 配置各VIP等级能否在VIP功能栏里购买VIP资源包
	 static $conf_vip_shop_res = array(
			 0	=> '',
			 1	=> 'SHOP_RES',
			 2	=> 'SHOP_RES',
			 3	=> 'SHOP_RES',
			 4	=> 'SHOP_RES',
			 5	=> 'SHOP_RES',
			 6	=> 'SHOP_RES',
			 7	=> 'SHOP_RES',
			 8	=> 'SHOP_RES',
			 9	=> 'SHOP_RES',
	 );
	*/
	/* 配置各VIP等级对应可购买VIP功能数据
	 static $conf_vip_function = array(
			 0	=> array(),
			 1	=> array(),
			 2	=> array(),
			 3	=> array(
					 'FOOD_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
			 ),
			 4	=> array(
					 'FOOD_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'OIL_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
			 ),
			 5	=> array(
					 'GOLD_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'FOOD_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'OIL_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
			 ),
			 6	=> array(
					 'GOLD_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'FOOD_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'OIL_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'ARMY_INCR_ATT'		=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
			 ),
			 7	=> array(
					 'GOLD_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'FOOD_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'OIL_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'ARMY_INCR_ATT'		=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'ARMY_INCR_DEF'		=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
			 ),
			 8	=> array(
					 'GOLD_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'FOOD_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'OIL_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'ARMY_INCR_ATT'		=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'ARMY_INCR_DEF'		=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'HERO_INCR_ARMY'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
			 ),
			 9	=> array(
					 'GOLD_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'FOOD_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'OIL_INCR_YIELD'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'ARMY_INCR_ATT'		=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'ARMY_INCR_DEF'		=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'HERO_INCR_ARMY'	=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
					 'ARMY_RELIFE'		=> array('eff_day'=>3, 'max_times'=>5, 'per_add'=>5),
			 ),
	 );
	*/
	/** VIP标签功能点名字 */
	static $arr_vip_function = array(
		'GOLD_INCR_YIELD',
		'FOOD_INCR_YIELD',
		'OIL_INCR_YIELD',
		'ARMY_INCR_ATT',
		'ARMY_INCR_DEF',
		'HERO_INCR_ARMY',
		'ARMY_RELIFE',
	);

	/*
	 * 获取VIP所有配置数据
	* @author chenhui on 20111118
	* @return array VIP配置数组

	static public function getVIPEffectConfig()
	{
	return M_Config::getVal('vip_effect');
	}
	*/

	/**
	 * 获取VIP标签栏VIP功能点配置
	 * @author chenhui on 20120828
	 * @param int $vipLevel VIP等级
	 * @return array
	 */
	static public function getFunctionConf($vipLevel = '') {
		$vipLevel = empty($vipLevel) ? 0 : intval($vipLevel);
		$vipConf  = M_Vip::getVipConfig();
		$arrNeed  = array();
		foreach (M_Vip::$arr_vip_function as $fun_name) {
			if (isset($vipConf[$fun_name][$vipLevel]) && !empty($vipConf[$fun_name][$vipLevel])) {
				$strLev             = $vipConf[$fun_name][$vipLevel];
				$arrLev             = explode(',', $strLev);
				$arrNeed[$fun_name] = $arrLev;
			}
		}
		$ret = $arrNeed;
		return $ret;
	}

	/**
	 * 判断某VIP等级是否有权限开启某序号建筑CD队列
	 * @author chenhui on 20111103
	 * @param int $vipLevel VIP等级
	 * @param int $listId 建筑队列序号(从1开始)
	 * @return bool
	 */
	static public function isCDListIdOpenPower($vipLevel, $listId) {
		$ret      = false;
		$vipLevel = intval($vipLevel);
		$listId   = intval($listId);

		$vipConf = self::getVipConfig(); //VIP配置
		if (isset($vipConf['BUILD_CD_LISTID'][$vipLevel]) && $vipConf['BUILD_CD_LISTID'][$vipLevel] >= $listId) {
			$ret = true;
		}

		return $ret;
	}

	/**
	 * 判断某VIP等级是否有权限开启某序号科技CD队列
	 * @author chenhui on 20111103
	 * @param int $vipLevel VIP等级
	 * @param int $listId 科技队列序号(从1开始)
	 * @return bool
	 */
	static public function isCDListIdOpenPowerTech($vipLevel, $listId) {
		$ret      = false;
		$vipLevel = intval($vipLevel);
		$listId   = intval($listId);

		$vipConf = self::getVipConfig(); //VIP配置
		if (isset($vipConf['TECH_CD_LISTID'][$vipLevel]) && $vipConf['TECH_CD_LISTID'][$vipLevel] >= $listId) {
			$ret = true;
		}

		return $ret;
	}

	/**
	 * 判断某VIP等级是否有权限开启某序号特殊武器槽
	 * @author chenhui on 20111103
	 * @param int $vipLevel VIP等级
	 * @param int $slotId 特殊武器槽序号(从1开始)
	 * @return bool
	 */
	static public function isSlotIdOpenPower($vipLevel, $slotId) {
		$ret      = false;
		$vipLevel = intval($vipLevel);
		$slotId   = intval($slotId);

		$vipConf = self::getVipConfig(); //VIP配置
		if (isset($vipConf['SPECIAL_SLOTID'][$vipLevel])) {
			$str_slotid = $vipConf['SPECIAL_SLOTID'][$vipLevel];
			$arrSlotId  = explode(',', $str_slotid);
			in_array($slotId, $arrSlotId) && $ret = true;
		}

		return $ret;
	}

	/**
	 * 判断某VIP等级是否有权限移动城内建筑
	 * @author chenhui on 20111103
	 * @param int $vipLevel VIP等级
	 * @return bool
	 */
	static public function isMoveCityinBuild($vipLevel) {
		$ret      = false;
		$vipLevel = intval($vipLevel);

		$vipConf = self::getVipConfig(); //VIP配置
		if (isset($vipConf['MOVE_BUILD'][$vipLevel]) && $vipConf['MOVE_BUILD'][$vipLevel] > 0) {
			$ret = true;
		}

		return $ret;
	}

	/**
	 * 判断某VIP等级是否有权限购买资源
	 * @author chenhui on 20120825
	 * @param int $vipLevel VIP等级
	 * @return bool
	 */
	static public function isShopRes($vipLevel) {
		$ret      = true;
		$vipLevel = intval($vipLevel);

		$vipConf = self::getVipConfig(); //VIP配置
		if (isset($vipConf['SHOP_RES'][$vipLevel]) && $vipConf['MOVE_BUILD'][$vipLevel] < 1) {
			$ret = false;
		}

		return $ret;
	}

	/**
	 * 判断某VIP等级是否有权限领取VIP宝箱
	 * @author chenhui on 20120825
	 * @param int $vipLevel VIP等级
	 * @return int 0/VIP宝箱编号
	 */
	static public function isVipPackPower($vipLevel) {
		$ret      = '';
		$vipLevel = intval($vipLevel);

		$vipConf = self::getVipConfig(); //VIP配置
		if (isset($vipConf['VIP_PACKAGE'][$vipLevel])) {
			$ret = $vipConf['VIP_PACKAGE'][$vipLevel];
		}

		return $ret;
	}

	/**
	 * 判断某VIP等级是否有权限购买此 减少出征时间 功能
	 * @author chenhui on 20111118
	 * @param int $vipLevel VIP等级
	 * @param int $percent 要减少的百分比值
	 * @return bool
	 */
	static public function isDecrMarchTime($vipLevel, $percent) {
		$ret      = false;
		$vipLevel = intval($vipLevel);
		$percent  = intval($percent);

		$vipConf = self::getVipConfig();
		$strTime = isset($vipConf['DECR_MARCH_TIME'][$vipLevel]) ? $vipConf['DECR_MARCH_TIME'][$vipLevel] : '';
		$arrTime = !empty($strTime) ? explode(',', $strTime) : array();
		if (0 == $percent || in_array($percent, $arrTime)) {
			$ret = true;
		}

		return $ret;
	}

	/**
	 * 获取购买 减少出征时间 所需军饷
	 * @author chenhui on 20111118
	 * @param int $vipLevel VIP等级
	 * @param int $percent 要减少的百分比值
	 * @return int 军饷数
	 */
	static public function getDecrMarchTimeCost($vipLevel, $percent) {
		$vipLevel = intval($vipLevel);
		$percent  = intval($percent);
		$ret      = 0;
		if ($percent > 0) {
			$vipConf = self::getVipConfig();
			if (isset($vipConf['DECR_MARCH_TIME'][$vipLevel])) {
				$arrTime = explode(',', $vipConf['DECR_MARCH_TIME'][$vipLevel]);
				if (in_array($percent, $arrTime)) {
					$ret = $percent * 2;
				}
			}
		}
		return $ret;
	}

	/**
	 * 获取VIP配置中某物品的信息
	 * @author chenhui on 20120827
	 * @param int $vipLevel VIP等级
	 * @param int $type 物品类型
	 * @param int $itemId 物品ID
	 * @return array        array(每天玩家限购, 每天系统限购, 价格)/array 3D
	 */
	static public function getShopItemInfo($vipLevel, $type = '', $itemId = '') {
		$ret     = $arrNeed = array();
		$vipConf = M_Vip::getVipConfig(); //VIP配置

		$strItemInfo = isset($vipConf['VIP_SHOP'][$vipLevel]) ? $vipConf['VIP_SHOP'][$vipLevel] : '';
		if (!empty($strItemInfo)) {
			$arrItemInfo = explode('|', $strItemInfo);
			if (!empty($arrItemInfo) && is_array($arrItemInfo)) {
				foreach ($arrItemInfo as $strItem) {
					$arrItem                           = explode(',', $strItem);
					$arrNeed[$arrItem[0]][$arrItem[1]] = array($arrItem[2], $arrItem[3], $arrItem[4]);
				}
			}

			if (empty($type) && empty($itemId)) {
				$ret = $arrNeed;
			} else {
				$ret = $arrNeed[$type][$itemId];
			}
		}

		return $ret;
	}

	/**
	 * 获取今天某玩家购买的某VIP物品数量
	 * @author chenhui on 20111117
	 * @param int $cityId 城市ID
	 * @param int $type 类型
	 * @param int $itemId 物品ID
	 * @return int 数量
	 */
	static public function getTodayShopItemBuyNum($cityId, $type, $itemId) {
		$ret           = 0;
		$cityId        = intval($cityId);
		$type          = intval($type);
		$itemId        = intval($itemId);
		$cityExtraInfo = M_Extra::getInfo($cityId);
		if (!empty($cityExtraInfo['vipshop_buylist'])) {
			$str_buylist = empty($cityExtraInfo['vipshop_buylist']) ? '[]' : $cityExtraInfo['vipshop_buylist'];
			$arrBuyList  = json_decode($str_buylist, true);
			$todayStamp  = mktime(0, 0, 0);
			if (!empty($arrBuyList[$todayStamp][$type][$itemId])) {
				$ret = $arrBuyList[$todayStamp][$type][$itemId];
			}
		}
		return intval($ret);
	}

	/**
	 * 判断某玩家是否可以购买特定数量的VIP商城玩家限量物品
	 * @author chenhui on 20111117
	 * @param int $cityId 城市ID
	 * @param int $vipLevel VIP等级
	 * @param int $type 类型
	 * @param int $itemId 相应ID
	 * @param int $buyNum 购买数量
	 * @return bool
	 */
	static public function isShopItemNumUserOK($cityId, $vipLevel, $type, $itemId, $buyNum) {
		$ret      = false;
		$cityId   = intval($cityId);
		$vipLevel = intval($vipLevel);
		$type     = intval($type);
		$itemId   = intval($itemId);
		$buyNum   = intval($buyNum);

		$itemInfo = M_Vip::getShopItemInfo($vipLevel, $type, $itemId);
		if (!empty($itemInfo)) {
			$userLimit  = intval($itemInfo[0]);
			$userBuyNum = self::getTodayShopItemBuyNum($cityId, $type, $itemId);
			if ($userBuyNum + $buyNum <= $userLimit) {
				$ret = true;
			}
		}

		return $ret;
	}

	/**
	 * 获取玩家限购的某等级某类型某商品今天剩余数量
	 * @author chenhui on 20120220
	 * @param int $cityId 城市ID
	 * @param int $vipLevel VIP等级
	 * @param int $type 类型
	 * @param int $itemId 相应ID
	 * @return int 数量
	 */
	static public function getTodayShopItemUserLeftNum($cityId, $vipLevel, $type, $itemId) {
		$ret      = 0;
		$itemInfo = M_Vip::getShopItemInfo($vipLevel, $type, $itemId);
		if (!empty($itemInfo) && $cityId > 0 && $vipLevel > 0) {
			$buyNum    = self::getTodayShopItemBuyNum($cityId, $type, $itemId);
			$userLimit = intval($itemInfo[0]);
			$ret       = max(0, $userLimit - $buyNum);
		}
		return $ret;
	}

	/**
	 * 获取系统限购的某等级某类型某商品今天剩余数量
	 * @author chenhui on 20111213
	 * @param int $vipLevel VIP等级
	 * @param int $type 商品类型
	 * @param int $itemId 商品对应ID
	 * @return int            剩余数量
	 */
	static public function getTodayShopItemSysLeftNum($vipLevel, $type, $itemId) {
		$ret      = 0;
		$itemInfo = M_Vip::getShopItemInfo($vipLevel, $type, $itemId);
		$hKey     = strval($vipLevel . $type . $itemId);
		$rc       = new B_Cache_RC(T_Key::VIP_SHOP_SYS_LEFT, date('Ymd'));
		if ($rc->exists()) {
			$arrKeyAll = $rc->hgetall();
			if (isset($arrKeyAll[$hKey])) {
				$ret = $rc->hget($hKey);
			} else {
				$ret = $itemInfo[1];
				$up  = array($hKey => $ret);
				$rc->hmset($up, T_App::ONE_DAY);
			}
		} else {
			$ret = $itemInfo[1];
			$up  = array($hKey => $ret);
			$rc->hmset($up, T_App::ONE_DAY);
		}

		return intval($ret);
	}

	/**
	 * 减少系统限购商品的今天剩余数量，余量不足则提示错误
	 * @author chenhui on 20111213
	 * @param int $vipLevel VIP等级
	 * @param int $type 商品类型
	 * @param int $itemId 商品对应ID
	 * @param int $buyNum 购买数量
	 * @return string        错误编号或空
	 */
	static public function isShopItemSysOK($vipLevel, $type, $itemId, $buyNum) {
		$err     = '';
		$buyNum  = intval($buyNum);
		$leftNum = self::getTodayShopItemSysLeftNum($vipLevel, $type, $itemId);
		if ($leftNum >= $buyNum) {
			$hKey = strval($vipLevel . $type . $itemId);
			$rc   = new B_Cache_RC(T_Key::VIP_SHOP_SYS_LEFT, date('Ymd'));
			$rc->hincrby($hKey, -$buyNum); //减少剩余数量
		} else {
			$err = T_ErrNo::SHOP_SYS_OVER_LIMIT;
		}
		return $err;
	}

	/**
	 * 更新VIP商城玩家限购物品的已被购买数量
	 * @author chenhui on 20111117
	 * @param int $cityId 城市ID
	 * @param int $vipLevel VIP等级
	 * @param int $type 类型
	 * @param int $itemId 相应ID
	 * @param int $buyNum 购买数量
	 * @return bool
	 */
	static public function upShopItemNumUserBuy($cityId, $vipLevel, $type, $itemId, $buyNum) {
		$ret       = false;
		$cityId    = intval($cityId);
		$vipLevel  = intval($vipLevel);
		$type      = intval($type);
		$itemId    = intval($itemId);
		$buyNum    = intval($buyNum);
		$extraInfo = M_Extra::getInfo($cityId);
		if (isset($extraInfo['vipshop_buylist'])) {
			$str_buylist = empty($extraInfo['vipshop_buylist']) ? '[]' : $extraInfo['vipshop_buylist'];
			$arrBuyList  = json_decode($str_buylist, true);
			$todayStamp  = mktime(0, 0, 0);
			if (isset($arrBuyList[$todayStamp])) {
				if (!empty($arrBuyList[$todayStamp][$type][$itemId])) {
					$arrBuyList[$todayStamp][$type][$itemId] += $buyNum;
				} else {
					$arrBuyList[$todayStamp][$type][$itemId] = $buyNum;
				}
			} else {
				$arrBuyList                              = array();
				$arrBuyList[$todayStamp][$type][$itemId] = $buyNum;
			}

			$ret = M_Extra::setInfo($cityId, array('vipshop_buylist' => json_encode($arrBuyList)));
		}

		return $ret;
	}




	/*****VIP改版**开始*****************************/
	/*
	 //非繁体版VIP配置
	static $vip_config_cn = array(
			'MAX_VIP_LEVEL'			=> 9,
			'MIL_PAY_CONF'			=> array(0,100,500,1000,2000,5000,10000,30000,50000,10000),
			'INCR_ENERGY_LIMIT'		=> array(0,0,10,20,30,40,50,60,80,100),
			'BUY_ENERGY'			=> array(0,0,2,4,6,8,10,12,14,16),
			'INCR_MILORDER_LIMIT'	=> array(0,0,5,15,25,35,45,60,80,100),
			'BUY_MILORDER'			=> array(0,0,2,4,6,8,10,12,14,16),
			'BUILD_CD_LISTID'		=> array(3,3,4,5,6,7,7,7,7,7),	//从1开始
			'TECH_CD_LISTID'		=> array(1,1,1,1,1,1,1,1,1,1),	//从1开始
			'SPECIAL_SLOTID'		=> array(1,2,3,4,5,6,7,8,10,12),	//从1开始
			'MOVE_BUILD'			=> array(0,1,1,1,1,1,1,1,1,1),
			'INCR_AWARD_RATE'		=> array(0,0,0,0,0,10,20,30,50,100),
			'DECR_MARCH_TIME'		=> array('','','','','','10','10,20','10,20,30','10,20,30,40','10,20,30,40,50'),
			'HERO_AWARD'			=> array('','','','','','','7','8','8','8'),
			'EQUI_AWARD'			=> array('','','','44,104,164,224,284,344,174,354','45,105,165,225,285,345,175,355','46,106,166,226,286,346,176,356','47,107,167,227,287,347,177,357','48,108,168,228,288,348,178,358','49,109,169,229,289,349,179,359','50,110,170,230,290,350,180,360'),
			'FOOD_INCR_YIELD'		=> array('','','','3,5,5','3,5,5','3,5,5','3,5,5','3,5,5','3,5,5','3,5,5'),
			'OIL_INCR_YIELD'		=> array('','','','','3,5,5','3,5,5','3,5,5','3,5,5','3,5,5','3,5,5'),
			'GOLD_INCR_YIELD'		=> array('','','','','','3,5,5','3,5,5','3,5,5','3,5,5','3,5,5'),
			'ARMY_INCR_ATT'			=> array('','','','','','','3,5,5','3,5,5','3,5,5','3,5,5'),
			'ARMY_INCR_DEF'			=> array('','','','','','','','3,5,5','3,5,5','3,5,5'),
			'HERO_INCR_ARMY'		=> array('','','','','','','','','3,5,5','3,5,5'),
			'ARMY_RELIFE'			=> array('','','','','','','','','','3,5,5'),
			'COLONY_OPEN'			=> array('0','0','0','0','0','0,500','0,500','0,500','0,500,1000'),
			'VIP_SHOP'				=> array('','','','2,102,99999,99999,999|2,116,99999,99999,1199','2,102,99999,99999,999|2,116,99999,99999,1199|2,103,99999,99999,1099|2,104,99999,99999,1199|2,111,99999,99999,1199|2,122,99999,99999,1299','2,102,99999,99999,999|2,116,99999,99999,1199|2,103,99999,99999,1099|2,104,99999,99999,1199|2,111,99999,99999,1199|2,122,99999,99999,1299|2,105,99999,99999,1299|2,114,99999,99999,1399|2,120,99999,99999,1499|2,124,99999,99999,1399','2,102,99999,99999,999|2,116,99999,99999,1199|2,103,99999,99999,1099|2,104,99999,99999,1199|2,111,99999,99999,1199|2,122,99999,99999,1299|2,105,99999,99999,1299|2,114,99999,99999,1399|2,120,99999,99999,1499|2,124,99999,99999,1399|2,106,99999,99999,1299|2,113,99999,99999,1399|2,118,99999,99999,1499|2,125,99999,99999,1499','3,94,99999,99999,50|3,34,99999,99999,50|3,154,99999,99999,50|3,214,99999,99999,50|3,274,99999,99999,50|3,334,99999,99999,50|2,102,99999,99999,999|2,116,99999,99999,1199|2,103,99999,99999,1099|2,104,99999,99999,1199|2,111,99999,99999,1199|2,122,99999,99999,1299|2,105,99999,99999,1299|2,114,99999,99999,1399|2,120,99999,99999,1499|2,124,99999,99999,1399|2,106,99999,99999,1299|2,113,99999,99999,1399|2,118,99999,99999,1499|2,125,99999,99999,1499|2,107,99999,99999,1399|2,110,99999,99999,1699|2,117,99999,99999,1699|2,127,99999,99999,1899','3,34,99999,99999,50|3,35,99999,99999,60|3,94,99999,99999,50|3,95,99999,99999,60|3,154,99999,99999,50|3,155,99999,99999,60|3,214,99999,99999,50|3,215,99999,99999,60|3,274,99999,99999,50|3,275,99999,99999,60|3,334,99999,99999,50|3,335,99999,99999,60|2,102,99999,99999,999|2,116,99999,99999,1199|2,103,99999,99999,1099|2,104,99999,99999,1199|2,111,99999,99999,1199|2,122,99999,99999,1299|2,105,99999,99999,1299|2,114,99999,99999,1399|2,120,99999,99999,1499|2,124,99999,99999,1399|2,106,99999,99999,1299|2,113,99999,99999,1399|2,118,99999,99999,1499|2,125,99999,99999,1499|2,107,99999,99999,1399|2,110,99999,99999,1699|2,117,99999,99999,1699|2,127,99999,99999,1899|2,108,99999,99999,1699|2,115,99999,99999,1899|2,123,99999,99999,1999|2,126,99999,99999,2199','3,34,99999,99999,50|3,35,99999,99999,60|3,36,99999,99999,70|3,37,99999,99999,80|3,94,99999,99999,50|3,95,99999,99999,60|3,96,99999,99999,70|3,97,99999,99999,80|3,154,99999,99999,50|3,155,99999,99999,60|3,156,99999,99999,70|3,157,99999,99999,80|3,214,99999,99999,50|3,215,99999,99999,60|3,216,99999,99999,70|3,217,99999,99999,80|3,274,99999,99999,50|3,275,99999,99999,60|3,276,99999,99999,70|3,277,99999,99999,80|3,334,99999,99999,50|3,335,99999,99999,60|3,336,99999,99999,70|3,337,99999,99999,80|2,102,99999,99999,999|2,116,99999,99999,1199|2,103,99999,99999,1099|2,104,99999,99999,1199|2,111,99999,99999,1199|2,122,99999,99999,1299|2,105,99999,99999,1299|2,114,99999,99999,1399|2,120,99999,99999,1499|2,124,99999,99999,1399|2,106,99999,99999,1299|2,113,99999,99999,1399|2,118,99999,99999,1499|2,125,99999,99999,1499|2,107,99999,99999,1399|2,110,99999,99999,1699|2,117,99999,99999,1699|2,127,99999,99999,1899|2,108,99999,99999,1699|2,115,99999,99999,1899|2,123,99999,99999,1999|2,126,99999,99999,2199|2,109,99999,99999,1899|2,112,99999,99999,2199|2,121,99999,99999,2199|2,119,99999,99999,2199|2,140,99999,99999,2299'),
			'VIP_PACKAGE'			=> array("","256_1","256_1","256_1","256_1","256_1","256_1","256_1","256_1","256_1"),
			'SHOP_RES'				=> array(0,1,1,1,1,1,1,1,1,1),
			'PACK_EQUI'				=> array("48","72","72","96","96","120","120","144","144","168"),	//装备背包容量
			'PACK_DRAW'				=> array("48","72","72","96","96","120","120","144","144","168"),	//图纸背包容量
			'PACK_PROPS'			=> array("48","72","72","96","96","120","120","144","144","168"),	//道具背包容量
	);

	// 繁体版VIP配置
	static $vip_config_tw = array(
			'MAX_VIP_LEVEL'			=> 5,
			'MIL_PAY_CONF'			=> array(0,0,350,1000,5000,20000),
			'INCR_ENERGY_LIMIT'		=> array(0,0,20,40,60,100),
			'BUY_ENERGY'			=> array(0,0,4,8,12,16),
			'INCR_MILORDER_LIMIT'	=> array(0,0,15,35,60,100),
			'BUY_MILORDER'			=> array(0,0,4,8,12,16),
			'BUILD_CD_LISTID'		=> array(3,3,4,5,6,7),	//从1开始
			'TECH_CD_LISTID'		=> array(1,1,1,1,2,2),	//从1开始
			'SPECIAL_SLOTID'		=> array(1,2,3,4,5,6),	//从1开始
			'MOVE_BUILD'			=> array(0,1,1,1,1,1),
			'INCR_AWARD_RATE'		=> array(0,0,0,0,10,20),
			'DECR_MARCH_TIME'		=> array('','','','10','30','50'),
			'HERO_AWARD'			=> array('','','','7','8','8'),
			'EQUI_AWARD'			=> array('','','','44,104,164,224,284,344,174,354','45,105,165,225,285,345,175,355','46,106,166,226,286,346,176,356'),
			'FOOD_INCR_YIELD'		=> array('','','','','',''),
			'OIL_INCR_YIELD'		=> array('','','','','',''),
			'GOLD_INCR_YIELD'		=> array('','','','','',''),
			'ARMY_INCR_ATT'			=> array('','','','','',''),
			'ARMY_INCR_DEF'			=> array('','','','','',''),
			'HERO_INCR_ARMY'		=> array('','','','','',''),
			'ARMY_RELIFE'			=> array('','','','','','3,5,5'),
			'COLONY_OPEN'			=> array('0,500,800','0,500,800','0,500,800','0,500,800','0,500,800','0,500,800'),
			'VIP_SHOP'				=> array('','','','','',''),
			'VIP_PACKAGE'			=> array("","","256_1","256_2","256_3","256_4"),
			'SHOP_RES'				=> array(0,1,1,1,1,1),
			'PACK_EQUI'				=> array("48","72","96","120","144","168"),	//装备背包容量
			'PACK_DRAW'				=> array("48","72","96","120","144","168"),	//图纸背包容量
			'PACK_PROPS'			=> array("48","72","96","120","144","168"),	//道具背包容量
	);
	*/

	/**
	 * 获取VIP配置
	 * @author chenhui on 20120822
	 * @return array
	 */
	static public function getVipConfig() {
		return M_Config::getVal('vip_config');
	}

	/*****VIP改版**结束*****************************/


	//以下为后台编辑VIP配置需要
	/** 资源包类道具 ID=>名字 */
	static public function getVipShopPackageIdName() {
		$ret      = array();
		$arrProps = M_Base::propsAll();
		if (!empty($arrProps) && is_array($arrProps)) {
			foreach ($arrProps as $id => $propsInfo) {
				if ('NEWBIE_PACKS' == $propsInfo['effect_txt'] && 1 == intval($propsInfo['is_vip_use'])) {
					$ret[$id] = $propsInfo['name'];
				}
			}
		}
		return $ret;
	}

	/** 图纸类道具 ID=>名字 */
	static public function getVipShopDrawingIdName() {
		$ret      = array();
		$arrProps = M_Base::propsAll();
		if (!empty($arrProps) && is_array($arrProps)) {
			foreach ($arrProps as $id => $propsInfo) {
				if ('WEAPON_CREATE' == $propsInfo['effect_txt'] && 1 == intval($propsInfo['is_vip_use'])) {
					$ret[$id] = $propsInfo['name'];
				}
			}
		}
		return $ret;
	}

}

?>