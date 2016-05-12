<?php

/**
 * 公式模块
 */
class M_Formula {
	/**
	 * 计算加成
	 * @author huwei on 20111027
	 * @param int $num 原始数值
	 * @param int $add 加成数  (25 表示 25%)
	 * @return int
	 */
	static public function calcAdd($num, $add = 0) {
		$ret = $num;
		if (!empty($num) && $add > 0) {
			$ret = $num * (($add / 100) + 1);
		}
		return $ret;
	}

	/**
	 * 计算某值的某百分比值
	 * @author chenhui on 20120408
	 * @param int $num 原始数值
	 * @param int $add 百分比值
	 * @return int/float
	 */
	static public function calcPerNum($num, $add) {
		$ret = 0;
		if ($num > 0 && $add > 0) {
			$ret = $num * ($add / 100);
		}
		return $ret;
	}

	/**
	 * 计算军官升级所需经验
	 * @author Hejunyun
	 * @param int $level 军官当前等级
	 * @return int $needExp 升级所需经验
	 */
	static public function getGrowExp($level) {
		//下一级需要的经验值 [n(n+1)(2n+1)/6*系数
		$needExp  = 0;
		$heroConf = M_Config::getVal();
		$level    = $level + 1;

		if (isset($heroConf['hero_exp'][$level])) {
			$needExp = $heroConf['hero_exp'][$level];
		} else {
			$needExp = self::_heroExp($level);
		}

		return round($needExp);
	}

	static private function _heroExp($level, $fac = 1) {
		$level = max(1, $level);
		if ($level > 70) {
			$fac = 30;
		} else if ($level > 50) {
			$fac = 15;
		} else if ($level > 20) {
			$fac = 6;
		} else if ($level > 1) {
			$fac = 3;
		} else {
			$fac = 1;
		}

		if ($level == 1) {
			return 1;
		}
		$tmpLv = $level - 1;
		return self::_heroExp($tmpLv, $fac) + ($level - 1) * $fac;
	}

	/**
	 * 根据军官成长值获取升级获得点数
	 * @author Hejunyun
	 * @param int $grow_rate 成长值
	 * @return float $growAttr 成长点数
	 */
	static public function getGrowAttr($grow_rate) {
		//升级获取点数=成长值 * 升级基础点数
		$growAttr = $grow_rate * 3;
		return $growAttr;
	}

	/**
	 * 根据三围计算英雄价值
	 * @author huwei
	 * @param int $num 三围总数
	 * @return int
	 */
	static public function heroValue($num) {
		$heroConf = M_Config::getVal();
		return $num * $heroConf['hero_base_value'];
	}

	/**
	 * 根据等级计算英雄情绪
	 * @author huwei
	 * @param int $level 英雄等级
	 * @return int
	 */
	static public function heroMood($level) {
		$heroConf = M_Config::getVal();
		if (isset($heroConf['hero_attr_mood'][$level])) {
			$ret = $heroConf['hero_attr_mood'][$level];
		} else {
			$ret = $heroConf['hero_attr_mood'][1];
		}
		return $ret;
	}

	/**
	 * 根据等级计算英雄精力
	 * @author huwei
	 * @param int $level 英雄等级
	 * @return int
	 */
	static public function heroEnergy($level) {
		$heroConf = M_Config::getVal();
		if (isset($heroConf['hero_attr_energy'][$level])) {
			$ret = $heroConf['hero_attr_energy'][$level];
		} else {
			$ret = $heroConf['hero_attr_energy'][1];
		}
		return $ret;
	}


	/**
	 * 根据城市等级计算所能拥有的英雄数
	 * @author huwei
	 * @param int $level 城市等级(总共50级)
	 * @return int
	 */
	static public function calcHeroNumLimit($level = 1) {
		//$grade = ceil($level / 10);
		return $level * 3;
	}


	/**
	 * 英雄每个兵种最大带兵数数组
	 * @author huwei
	 * @param int $cityId
	 * @param int $level 英雄等级
	 * @param int $skillAdd
	 * @return array
	 */
	static public function calcHeroMaxArmyNum($heroLv = 1, $skillEff = array(), $heroArmyNumAdd = array()) {
		$arrMaxArmyNum = array();
		if (!empty($heroLv)) {
			foreach (M_Army::$type as $armyId => $name) {
				$skillAdd               = isset($skillEff[$armyId]) ? $skillEff[$armyId] : 0;
				$vipAdd                 = isset($heroArmyNumAdd['vip_add']) ? $heroArmyNumAdd['vip_add'] : 0;
				$unionAdd               = isset($heroArmyNumAdd['union_add']) ? $heroArmyNumAdd['union_add'] : 0;
				$incrRate               = (100 + $vipAdd + $skillAdd + $unionAdd) / 100;
				$armyInfo               = M_Army::baseInfo($armyId);
				$arrMaxArmyNum[$armyId] = floor($heroLv * (60 * $incrRate / $armyInfo['cost_people'])); //计算各兵种最大带兵数
			}
		}
		return $arrMaxArmyNum;
	}

	/**
	 * 计算基础技能加成值
	 * @author huwei
	 * @param int $num
	 * @param int $add
	 * @return int
	 */
	static public function calcHeroBaseSkillAdd($num, $add) {
		$ret = 0;
		if ($num > 0 && $add > 0) {
			$ret = round($num * $add / 100);
		}
		return $ret;
	}

	/**
	 * 复活消耗金钱数量
	 * @author huwei
	 * @param int $level 城市等级(总共50级)
	 * @return int
	 */
	static public function relifeGlod($level) {
		$heroConf = M_Config::getVal();
		return $heroConf['hero_relife_gold'] * $level;
	}

	/**
	 * 计算英雄复活时间
	 * @author huwei
	 * @param int $heroInfo 英雄数据
	 * @return int
	 */
	static public function heroRelifeTime($heroInfo) {
		$ret = T_App::ONE_DAY;
		if (isset($heroInfo['id'])) {
			$arr       = array($heroInfo['attr_lead'], $heroInfo['attr_command'], $heroInfo['attr_military'], floor($heroInfo['stat_point']));
			$totalAttr = array_sum($arr);
			$ret       = $totalAttr * 50;
		}
		return $ret;
	}

	/**
	 * 计算英雄复活军饷消耗
	 * @author huwei
	 * @param int $time 复活时间差
	 * @return int
	 */
	static public function heroRelifeCost($time) {
		$ret = 0;
		if ($time > 0) {
			$ret = floor($time / (5 * 60));
		}
		return $ret;
	}

	/**
	 * 计算装备升级消耗的军饷
	 * @author Hejunyun
	 * @param int $lv 装备穿戴等级
	 * @param int $strengLv 装备强化等级
	 * @param int $quality 装备品质
	 * @param int $type 升级类型
	 */
	static public function calcUpgradeEquipCostMilpay($lv, $strengLv, $quality, $type) {
		$arr = array(
			0 => 0,
			1 => 0.5,
			2 => 1,
		);

		if ($strengLv > 40) {
			$x = 150;
		} else if ($strengLv > 30) {
			$x = 100;
		} else if ($strengLv > 20) {
			$x = 80;
		} else if ($strengLv > 10) {
			$x = 50;
		} else {
			$x = 20;
		}

		if ($quality <= 4) {
			$a = 0;
		} else if ($quality == 5) {
			$a = 40;
		} else if ($quality == 6) {
			$a = 200;
		}
		$cost = ($lv * 3 + $strengLv * $x + ($quality - 4) * $a) * $arr[$type];
		return $cost;
	}

	/**
	 * 更具城市等级计算能拥有的英雄数量
	 * @param int $level 城市等级(1-5)
	 * @return int
	 */
	static public function canHasHeroNum($level) {
		$sysMaxNum = max(intval(M_Config::getVal('hero_num_city_max')), 3);
		$level     = max(1, $level);
		return min($level * 3, $sysMaxNum);
	}

	/**
	 * 根据兵种等级获取所需的熟练度
	 * @author chenhui on 20110418
	 * @param int level 兵种等级
	 * @return int 所需熟练度值
	 */
	static public function armyProfic($level) {
		return ceil($level * $level * ($level + 1) * (2 * $level * $level + 1) / 6 * 100);
	}

	/**
	 * 根据兵种ID与等级获取所需军营等级
	 * @author chenhui on 20110712
	 * @param int $armyId 兵种ID
	 * @param int $level 兵种等级
	 * @return int 所需军营等级
	 */
	static public function armyUpgBuildLev($armyId, $level) {
		list($baseNeedLev, $diffLev) = M_Army::$upgbuild[$armyId];
		$needLev = floor($baseNeedLev + $diffLev * $level);
		return $needLev;
	}

	/**
	 * 计算资源基础增长
	 * @author huwei
	 * @param int $type 资源类型
	 * @param int $buildLv 建筑等级
	 */
	static public function calcResBuildBaseGrow($type, $buildLv) {
		$rateArr = array(
			T_App::RES_FOOD => 100,
			T_App::RES_OIL  => 100,
			T_App::RES_GOLD => 150,
		);

		$rate = isset($rateArr[$type]) ? $rateArr[$type] : 0;

		if ($buildLv <= 1 || $buildLv >= 100) {
			$ret = $rate;
		} else {
			$ret = self::calcResBuildBaseGrow($type, $buildLv - 1) + $rate * $buildLv;
		}
		return $ret;
	}


	/**
	 * 计算真实的资源加成
	 * @param int $maxStore 仓库大小
	 * @param int $resNum 现有资源数
	 * @param int $baseGrow 基础增长产量
	 * @param int $diffTime 时间差
	 */
	static public function calcIncrResAdd($maxStore, $resNum, $baseGrow, $diffTime) {
		$incrNum  = 0;
		$tmpAdd   = $baseGrow / T_App::ONE_HOUR * $diffTime;
		$totalNum = floatval($resNum) + floatval($tmpAdd);
		if ($maxStore > $resNum) {
			$incrNum = $totalNum > $maxStore ? max($maxStore - $resNum, 0) : $tmpAdd;
		}
		return round(floatval($incrNum), 8);
	}

	/**
	 * 计算真实的资源加成
	 * @param int $resNum 现有资源数
	 * @param int $baseGrow 基础增长产量
	 * @param int $diffTime 时间差
	 */
	static public function calcIncrResAddTemp($baseGrow, $diffTime) {
		$incrNum = 0;
		$incrNum = $baseGrow / T_App::ONE_HOUR * $diffTime;
		return round(floatval($incrNum), 8);
	}

	/**
	 * 计算CD时间剩余的秒数
	 * @author huwei
	 * @param int $time cd时间戳
	 * @return int
	 */
	static public function calcCDTime($time) {
		return max($time - time(), 0);
	}

	/**
	 * 计算Build CD时间的秒数
	 * @author huwei
	 * @param string $buildCD cd时间戳
	 * @param int $num 最大允许队列数量
	 * @return array(array(剩余秒数, 0/1是否可累加)...)
	 */
	static public function calcBuildCDTime($buildCD, $num) {
		$arr         = array();
		$buildCDTime = json_decode($buildCD, true);
		for ($i = 0; $i < $num; $i++) {
			if (isset($buildCDTime[$i])) {
				//$buildCDTime[$i] = is_numeric($buildCDTime[$i]) ? $buildCDTime[$i].'_'.'1' : $buildCDTime[$i];	// 容错处理
				$arrT  = explode('_', $buildCDTime[$i]);
				$left  = self::calcCDTime($arrT[0]);
				$flag  = ($left < 1) ? T_App::ADDUP_CAN : $arrT[1];
				$arr[] = array($left, intval($flag));
			} else {
				$arr[] = array(0, T_App::ADDUP_CAN);
			}
		}
		return $arr;
	}

	/**
	 * 计算Tech CD时间的秒数
	 * @author chenhui on 20120829
	 * @param string $techCD cd时间戳
	 * @param int $num 最大允许队列数量
	 * @return array(array(剩余秒数, 0/1是否可累加)...)
	 */
	static public function calcTechCDTime($techCD, $num) {
		$arr        = array();
		$num        = max(1, intval($num));
		$techCDTime = json_decode($techCD, true);

		for ($i = 0; $i < $num; $i++) {
			if (!empty($techCDTime) && is_array($techCDTime) && isset($techCDTime[$i])) {
				$arrT  = explode('_', $techCDTime[$i]);
				$left  = self::calcCDTime($arrT[0]);
				$flag  = ($left < 1) ? T_App::ADDUP_CAN : $arrT[1];
				$arr[] = array($left, intval($flag));
			} else {
				$arr[] = array(0, T_App::ADDUP_CAN);
			}
		}

		if (!in_array(ETC_NO, array('tw', 'en'))) {
			$arr = $arr[0];
		}

		return $arr;
	}

	/**
	 * 装备出售公式
	 * 销售金钱 =  四舍五入[品质系数*等级*(1+强化等级/5) ]（品质系数：白色=200、绿色=500、蓝色=1000、紫色=2000、红色=3000、金色=5000）
	 * @param int $quality 品质
	 * @param int $needLevel 需求等级
	 * @param int $strongLevel 强化等级
	 */
	static public function equipSellGold($quality, $needLevel, $strongLevel) {
		$tmp = isset(T_Equip::$sellParam[$quality]) ? T_Equip::$sellParam[$quality] : 0;
		$ret = round($tmp * $needLevel * (1 + $strongLevel / 5));
		return $ret;
	}

	/**
	 * 根据装备等级、品质计算装备属性
	 * [向上取整(等级/10) * 5 + 3 + (品质-1 ) * 10]；
	 * @author Hejunyun
	 * @param int $needLevel 装备等级
	 * @param int $quality 品质
	 * @return $attr 总属性
	 */
	static public function equipMakeAttrPoint($needLevel, $quality) {
		$attr      = 0;
		$needLevel = intval($needLevel);
		$quality   = intval($quality);
		if ($needLevel > 0 && isset(T_Word::$EQUIP_QUAL[$quality])) {
			$attr = round($needLevel / 10 * 5 + 3 + ($quality - 1) * 10);
		}
		return $attr;
	}

	/**
	 * 装备升级消耗的金钱
	 * 消耗金钱 = (装备等级 + 20 ) * 系数A + 强化等级 * 系数B；系数A = 5000，系数B = 10000 (系数可调整)
	 * @author Hejunyun
	 * @param int $needLv 装备穿戴等级
	 * @param int $strongLv 装备强化等级
	 */
	static public function equipUpgradeCostGold($needLv, $strongLv) {
		$cost = (intval($needLv) + 20) * 5000 + intval($strongLv) * 10000;
		return $cost;
	}


	/**
	 * 计算消单条建筑CD时间所需军饷数
	 * @author chenhui on 20110928
	 * @param int $seconds 剩余CD时间
	 * @return int 所需军饷数
	 */
	static public function calcCleanBuildCDNeed($seconds) {
		$ret = 0;
		if ($seconds > 2400 * 60) {
			$ret = ceil($seconds / (24 * 60));
		} else if ($seconds > 1800 * 60) {
			$ret = ceil($seconds / (23 * 60));
		} else if ($seconds > 1200 * 60) {
			$ret = ceil($seconds / (22 * 60));
		} else if ($seconds > 600 * 60) {
			$ret = ceil($seconds / (21 * 60));
		} else {
			$ret = ceil($seconds / (20 * 60));
		}
		return $ret;
	}

	/**
	 * 计算消科技CD时间所需军饷数
	 * @author chenhui on 20110928
	 * @param int $seconds 剩余CD时间
	 * @return int 所需军饷数
	 */
	static public function calcCleanTechCDNeed($seconds) {
		$ret = 0;
		if ($seconds > 2400 * 60) {
			$ret = ceil($seconds / (12 * 60));
		} else if ($seconds > 1800 * 60) {
			$ret = ceil($seconds / (11 * 60));
		} else if ($seconds > 1200 * 60) {
			$ret = ceil($seconds / (10 * 60));
		} else if ($seconds > 600 * 60) {
			$ret = ceil($seconds / (9 * 60));
		} else {
			$ret = ceil($seconds / (8 * 60));
		}
		return $ret;
	}

	/**
	 * 计算消武器CD时间所需军饷数
	 * @author chenhui on 20110929
	 * @param int $seconds 剩余CD时间
	 * @return int 所需军饷数
	 */
	static public function calcCleanWeaponCDNeed($seconds) {
		return ceil($seconds / (5 * 60));
	}

	/**
	 * 计算消解救CD时间所需军饷数
	 * @author duhuihui on 20121110
	 * @param int $num 解救次数
	 * @return int 所需军饷数
	 */
	static public function calcCleanRescueCDNeed($num = 0) {
		$arr       = explode(',', M_Config::getVal('rescue_cd_times')); //array(免费次数,军饷增加系数,最大军饷)
		$costPrice = max($num - $arr[0], 0) * $arr[1];
		$costPrice = min($costPrice, $arr[2]);
		return $costPrice;
	}

	/**
	 * 计算快速战斗CD时间所需军饷数
	 * @author huwei
	 * @param int $seconds 剩余CD时间
	 * @return int 所需军饷数
	 */
	static public function calcCleanQuickCD($seconds) {
		return ceil($seconds / 60 * 3);
	}

	/**
	 * 计算开启某ID武器槽所需军饷数
	 * @author chenhui on 20110929
	 * @param int $slotId 槽ID(从1开始)
	 * @return int 所需军饷数
	 */
	static public function calcOpenSlotNeed($slotId) {
		$ret    = 0;
		$slotId = intval($slotId);

		$costConf = M_Config::getVal('weapon_slot_cost');
		$ret      = isset($costConf[$slotId - 1]) ? $costConf[$slotId - 1] : 0;

		return $ret;
	}

	/**
	 * 计算增加某ID建筑CD队列所需军饷数
	 * @author chenhui on 20110929
	 * @param int $listId 建筑CD队列ID(第几列，从1开始)
	 * @return int 所需军饷数
	 */
	static public function calcIncrCDNumNeed($listId) {
		$ret    = 0;
		$listId = intval($listId);

		$costConf = M_Config::getVal('build_list_cost');
		$ret      = isset($costConf[$listId - 1]) ? $costConf[$listId - 1] : 0;

		return $ret;
	}

	/**
	 * 计算增加某ID科技CD队列所需军饷数
	 * @author chenhui on 20110929
	 * @param int $listId 科技CD队列ID(第几列，从1开始)
	 * @return int 所需军饷数
	 */
	static public function calcIncrCDNumNeedTech($listId) {
		$ret    = 0;
		$listId = intval($listId);

		$costConf = M_Config::getVal('tech_list_cost');
		$ret      = isset($costConf[$listId - 1]) ? $costConf[$listId - 1] : 0;

		return $ret;
	}

	/**
	 * 根据x,y 计算区域编号
	 * @author huwei
	 * @param int $x
	 * @param int $y
	 * @param int $maxW
	 * @return int
	 */
	static public function calcAreaNo($x, $y, $maxW) {
		return (int)$y * $maxW + $x;
	}

	/**
	 * 根据地图缓存块编号 计算区块X,区块Y
	 * @author huwei
	 * @param int $areaNo
	 * @param int $maxW
	 * @return int
	 */
	static public function calcAreaX($areaNo, $maxW) {
		return (int)$areaNo % $maxW;
	}

	/**
	 * 根据地图缓存块编号 计算区块Y
	 * @author huwei
	 * @param int $areaNo
	 * @param int $maxW
	 * @return int
	 */
	static public function calcAreaY($areaNo, $maxW) {
		return (int)floor($areaNo / $maxW);
	}

	/**
	 * 计算行军的距离
	 * 本州坐标差:    Z=开平方[（x1-x2）平方+（y1-y2）平方]
	 * 跨州坐标差:    Z=[开平方（ax1-bx2）平方+（ay1-by2）平方]
	 * 本州距离：    距离=坐标差*20
	 * 跨州距离：    距离=坐标差*20+1000
	 * @author huwei
	 * @param array $atkPosNo
	 * @param array $defPosNo
	 * @return int
	 */
	static public function calcMarchDistance($atkPosNo, $defPosNo) {
		list($attArea, $attX, $attY) = M_MapWild::calcWildMapPosXYByNo($atkPosNo);
		list($defArea, $defX, $defY) = M_MapWild::calcWildMapPosXYByNo($defPosNo);
		if ($attArea == $defArea) {
			$ret = sqrt(pow($attX - $defX, 2) + pow($attY - $defY, 2)) * 20;
		} else {
			$ret = sqrt(pow($attX - $defX, 2) + pow($attY - $defY, 2)) * 20 + 8000;
		}
		return $ret;
	}

	/**
	 * 计算行军的时间
	 * 四舍五入{[开平方（距离*100/速度）*63+1]*修正值}
	 * @author huwei
	 * @param int $speed
	 * @param int $distance
	 * @return int
	 */
	static public function calcMarchTime($speed, $distance, $percent = 0) {
		$factor  = 1;
		$percent = intval($percent);
		$percent = max(0, min(100, $percent));
		return round((sqrt($distance * 25 / $speed) * 63 + 1) * (100 - $percent) / 100 * $factor);
	}

	/**
	 * 行军资源消耗
	 * 消耗公式：四舍五入{（兵种消耗A+兵种消耗B+...）*出征时间/3600}
	 */
	static public function calcMarchCost($resTotal, $marchTime) {
		return round($resTotal * $marchTime * 2 / 3600);
	}


	/**
	 * 根据出征行军时间计算对应消耗的活力值
	 * @author chenhui on 20110816
	 * @param int $seconds 行军秒数
	 * @return int 活力值
	 */
	static public function needEnergy($seconds) {
		$energy = 2;
		if ($seconds >= 5400) {
			$energy = 15;
		} else if ($seconds >= 3600) {
			$energy = 12;
		} else if ($seconds >= 3000) {
			$energy = 10;
		} else if ($seconds >= 2400) {
			$energy = 8;
		} else if ($seconds >= 1800) {
			$energy = 6;
		} else if ($seconds >= 1200) {
			$energy = 5;
		} else if ($seconds >= 600) {
			$energy = 4;
		}
		return $energy;
	}


	/**
	 * 计算寻将时间
	 * @author huwei on 20110615
	 * @param int $baseVal 基础时间秒钟
	 * @param int $addVal 加成时间(百分比)
	 */
	static public function calcFindHeroTime($baseVal, $addVal) {
		$num = round($baseVal * (1 - $addVal / 100));
		return max(0, $num);
	}

	/**
	 * 计算寻将概率
	 * @author huwei on 20110615
	 * @param int $baseVal 基础
	 * @param int $addVal 加成
	 * @return int
	 */
	static public function calcFindHeroRate($baseVal, $addVal) {
		//四舍五入
		return min(round($baseVal + $addVal), 100);
	}

	/**
	 * 计算基础兵种基础属性的值 根据等级的加成(攻击,防御,生命)
	 * @author huwei
	 * @param int $baseVal 基础属性的值
	 * @param int $lv 等级
	 * @return int
	 */
	static public function getArmyBaseValByLv($baseVal, $lv) {
		return ceil($baseVal * (1 + $lv / 6.5));
	}


	/**
	 * 计算战斗攻击力 防御力
	 * 攻击力=[(兵种基础攻击+配备武器攻击力)*(1+武器相克加成%+科技加成%+军官、装备加成%+道具加成%+地形加成%+天气加成%+情绪加成%)]*(1+攻击修正值)*数量
	 * 防御力=[(兵种基础防御+配备武器防御力)*(1+武器相克加成%+科技加成%+军官、装备加成%+道具加成%+地形加成%+天气加成%+情绪加成%)]*(1+防御修正值)*数量
	 * 四舍五入
	 * @author huwei on 20110701
	 * @param int $baseForce 基础值
	 * @param int $addForce 加成值
	 * @param int $armyNum 数量
	 * @return int
	 */
	static public function calcBattleForce($baseForce, $addForce, $armyNum = 0) {
		$rnd   = rand(95, 105) / 100;
		$total = $baseForce * $addForce * $rnd;
		return $total;
	}

	/**
	 * 计算战斗伤害输出
	 * @author huwei on 20110701
	 * @param int $atkForce 攻击力
	 * @param int $defForce 防御力
	 * @return int
	 */
	static public function calcBattleDamage($atkForce, $defForce) {
		$total = 0;
		if (!empty($atkForce) && !empty($defForce)) {
			$rnd   = rand(95, 105) / 100;
			$total = (pow($atkForce, 2) / ($atkForce + $defForce)) * $rnd;
		} else {
			Logger::error(array(__METHOD__, 'err data', func_get_args()));
		}

		return $total;
	}

	/**
	 * 计算战斗后剩余兵数
	 * @author huwei
	 * @param int $totalDamage 总伤害
	 * @param int $armyNum 总数量
	 * @param int $hp 单个生命值
	 * @return array (死兵数, 剩余伤害)
	 */
	static public function calcBattleLeftArmyNum($totalDamage, $hp) {
		$killNum = $leftDmg = 0;
		if ($hp) {
			$killNum = floor($totalDamage / $hp);
			$leftDmg = floor($totalDamage % $hp);
		} else {
			Logger::error(array(__METHOD__, 'err data', func_get_args()));
		}

		return array($killNum, $leftDmg);
	}

	/**
	 * 计算原资源在空袭中应该损失的数量
	 * @author chenhui on 20110704
	 * @param array $arrRes 当前拥有的资源=>数量
	 * @return array 资源=>应损失的数量
	 */
	static public function calcBombDecrRes($arrRes) {
		$decrRate   = mt_rand(5, 8) / 100; //损失 5%~8%
		$arrDecrRes = array();
		if (count($arrRes) > 0) {
			foreach ($arrRes as $res => $num) {
				$arrDecrRes[$res] = round($num * $decrRate);
			}
		}
		return $arrDecrRes;
	}

	/**
	 * 计算侦察失败率(初步值)
	 * @author chenhui on 20110706
	 * @param int $army_num 侦察兵/机数量
	 * @param int $radarDiff 雷达站相差等级
	 * @param int $defRadarLv 防守方雷达等级
	 * @return int 失败率
	 */
	static public function calcTmpFailRate($army_num, $radarDiff, $defRadarLv) {
		return round(pow($army_num, 2) - 4 * pow($radarDiff / 2.5, 2) + ($defRadarLv / 2.5));
	}

	/**
	 * 计算侦察情报值(初步值)
	 * @author chenhui on 20110706
	 * @param int $army_num 侦察兵/机数量
	 * @param int $radarDiff 雷达站相差等级
	 * @return int 情报值
	 */
	static public function calcTmpInfoVal($army_num, $radarDiff) {
		return round($army_num + pow($radarDiff / 2.5, 2));
	}

	/**
	 * 计算兵种招募各等级消耗
	 * @author chenhui on 20110706
	 * @param int $base 基础消耗
	 * @param int $lev 兵种等级
	 * @return int 消耗值
	 */
	static public function calcArmyRecruitCost($base, $lev) {
		return ceil($base * pow(1.1, $lev));
	}

	/**
	 * 计算单个仓库容量
	 * @author chenhui on 20110718
	 * @param int $args 仓库容量参数
	 * @param int $level 此仓库等级
	 * @return int 容量
	 */
	static public function calcStorageCapaCity($args, $level) {
		if ($level == 0) {
			return M_Build::DEFAULT_STORE;
		}
		return self::calcStorageCapaCity($args, $level - 1) + $args * $level * $level;
	}

	/**
	 * 计算单个住宅容量
	 * @author chenhui on 20110721
	 * @param int $baseMaxPeople 初始最大人口数
	 * @param int $level 此住宅等级
	 * @return int 容量
	 */
	static public function calcHouseCapaCity($baseMaxPeople, $level) {
		if ($level < 1 || $level > 100) {
			return $baseMaxPeople;
		}
		return (self::calcHouseCapaCity($baseMaxPeople, $level - 1) + 10 * $level) * 1;
	}

	/**
	 * 计算坐标直接的距离
	 * @author huwei
	 * @param string $sPos
	 * @param string $ePos
	 * @return int
	 */
	static public function aiCalcDistance($sPos, $ePos) {
		$num = 0;
		if (!empty($sPos) && !empty($ePos)) {
			list($X1, $Y1) = explode('_', $sPos);
			list($X2, $Y2) = explode('_', $ePos);


			if ($Y1 == $Y2) {
				$num = ceil(abs($X1 - $X2) / 2);
			} elseif ($X2 == $X1) {
				$num = abs($Y2 - $Y1) * 2;
			} else {

				if ($X2 < $X1) {
					$tmpSX = $X1;
					$tmpSY = $Y1;
					$X1    = $X2;
					$Y1    = $Y2;
					$X2    = $tmpSX;
					$Y2    = $tmpSY;
				}

				if (($X1 % 2 == 0 && $X2 % 2 == 0) || ($X1 % 2 != 0 && $X2 % 2 != 0)) //都是奇数 或 偶数
				{
					$X0 = $X2 - abs($Y1 - $Y2) * 2;

					$Z1  = ($X0 - $X1) / 2;
					$Z1  = $Z1 < 0 ? 0 : ceil($Z1);
					$Z2  = $X2 - $X0;
					$num = $Z1 + $Z2;
				} else if ($X1 % 2 != 0 && $X2 % 2 == 0) //X1为奇数时  且  X1与X2奇偶不同
				{
					$X0 = $X2 - abs($Y1 - $Y2) * 2;

					$Z2 = $X2 - $X0;
					$Z1 = ($X0 - $X1) / 2;
					if ($Y1 < $Y2) {
						$Z1  = $Z1 < 0 ? 0 : ceil($Z1);
						$num = $Z1 + $Z2 - 1;
					} else {
						$Z1  = $Z1 < 0 ? 1 : ceil($Z1);
						$num = $Z1 + $Z2;
					}

				} else if ($X1 % 2 == 0 && $X2 % 2 != 0) //X1为偶数时 且  X1与X2奇偶不同
				{
					if ($Y1 < $Y2) {
						$X0  = $X2 - abs($Y1 - $Y2) * 2;
						$Z1  = ($X0 - $X1) / 2;
						$Z1  = $Z1 < 0 ? 1 : ceil($Z1);
						$Z2  = $X2 - $X0;
						$num = $Z1 + $Z2;
					} else {
						$X0  = ($X2 + 1) - abs($Y1 - $Y2) * 2;
						$Z1  = ($X0 - $X1) / 2;
						$Z1  = $Z1 < 0 ? 0 : ceil($Z1);
						$Z2  = $X2 - $X0;
						$num = $Z1 + $Z2;
					}
				}
			}
		}
		return $num;

	}

	/**
	 * 计算战场等待时间
	 * @author huwei
	 * @param int $now 当前时间
	 * @param int $waitTime 等待结束时间
	 * @param int $endTime 回合结束时间
	 */
	static public function calcBattleWaitTime($now, $waitTime, $endTime) {
		if ($now < $waitTime) {
			$leftTime = $waitTime - $now;
		} else {
			$leftTime = max($endTime - $now, 0);
		}
		return $leftTime;
	}

	/**
	 * 计算副本编号
	 * @author huwei
	 * @param int $chapterNo 章节编号
	 * @param int $campaignNo 战役编号
	 * @param int $pointNo 关卡编号
	 * @return int
	 */
	static public function calcFBNo($chapterNo, $campaignNo, $pointNo) {
		return $chapterNo * 10000 + $campaignNo * 100 + $pointNo;
	}

	/**
	 * 解析副本编号
	 * @author huwei
	 * @param int $fbNo 副本编号
	 * @return array
	 */
	static public function calcParseFBNo($fbNo) {
		$fbNo       = !empty($fbNo) ? $fbNo : '10100';
		$chapterNo  = floor($fbNo / 10000);
		$campaignNo = floor(($fbNo % 10000) / 100);
		$pointNo    = floor(($fbNo % 10000) % 100);
		return array($chapterNo, $campaignNo, $pointNo);
	}


	/**
	 * 计算当前等级市场贸易额初始上限值
	 * @author chenhui on 20110926
	 * @param int $baseQuota 市场交易额系数
	 * @param int $marketLev 市场等级
	 * @return int 贸易额初始上限值
	 */
	static public function calcMarketTradeMax($baseQuota, $marketLev) {
		return $baseQuota * pow($marketLev, 2);
	}

	/**
	 * 根据掠夺次数计算攻击次数效果(系数)
	 * @author chenhui on 20110930
	 * @param int $times 攻击次数
	 * @return float 效果系数值
	 */
	static public function calcPlunderTimesRate($times) {
		$times = intval($times);
		$ret   = 1;
		if ($times > 3) {
			$ret = 0;
		} else if ($times > 2) {
			$ret = 0.15;
		} else if ($times > 1) {
			$ret = 0.5;
		}
		return $ret;
	}


	/**
	 * 强化装备需要消耗的金币
	 * @author huwei on 20111014
	 * @param int $strongLevel
	 * @param int $equipQuality
	 */
	static public function calcStrongEquipCostGold($strongLevel, $equipQuality) {
		$strongLevel = intval($strongLevel);
		//读取装备配置
		$equipConfig = M_Config::getVal();
		//最大强化等级
		$maxLevel = $equipConfig['strong_equip_max_level'];
		//消耗公式：消耗数量 = （A*N²+B*N）*S
		$a = $equipConfig['strong_equip_rate_a'];
		$b = $equipConfig['strong_equip_rate_b'];
		$s = isset($equipConfig['strong_equip_rate_s'][$equipQuality]) ? $equipConfig['strong_equip_rate_s'][$equipQuality] : $equipConfig['strong_equip_rate_s'][1];

		//N=强化等级=当前等级+1
		$needGold = (($a * $strongLevel * $strongLevel) + ($b * $strongLevel)) * $s;
		return $needGold;
	}

	/**
	 * 根据累计充值军饷数计算当前VIP等级
	 * @author chenhui on 20111102
	 * @param int $total 累计充值军饷数
	 * @return int VIP等级[0-9]
	 */
	static public function calcVipLevelByTotalMilPay($total) {
		$level = 0;
		$total = intval($total);

		$vipConfig  = M_Vip::getVipConfig();
		$maxLevel   = intval($vipConfig['MAX_VIP_LEVEL']);
		$milpayConf = $vipConfig['MIL_PAY_CONF'];
		for ($lev = $maxLevel; $lev >= 0; $lev--) {
			if ($total >= intval($milpayConf[$lev])) {
				$level = $lev;
				break;
			}
		}

		return $level;
	}

	/**
	 * 计算VIP加成后的战斗掉宝概率
	 * @author chenhui on 20111110
	 * @param int $oldRate 初始掉宝概率(百分比值)
	 * @param int $vipLevel VIP等级
	 * @return int 新概率
	 */
	static public function calcVipAwardRate($oldRate, $vipLevel) {
		$vipConf = M_Vip::getVipConfig();
		return round($oldRate * (100 + $vipConf['INCR_AWARD_RATE'][$vipLevel]) / 100);
	}

	/**
	 * 计算联盟科技减成后的值
	 * @author chenhui on 20111222
	 * @param int $unionId 联盟ID
	 * @param int $unTechId 联盟科技ID
	 * @param int $oldVal 初始值
	 * @return int            减成后的值
	 */
	static public function calcUnionTechDecrEff($unionAdd, $oldVal) {
		return round($oldVal * (100 - $unionAdd) / 100);
	}

	/**
	 * 计算暴击加成
	 * @author huwei
	 * @param int $odds 暴击出现几率
	 * @param int $baseForce 基础攻击力
	 * @return int
	 */
	static public function calcAtkCritForce($baseForce, $isCrit = false) {
		$ret = $baseForce;
		if ($isCrit) {
			$ret = round($baseForce * T_Battle::CRIT_ADDNUM);
		}
		return $ret;
	}

	/**
	 * 升级联盟科技
	 * @author huwei
	 * @param int $techId
	 * @param int $level
	 */
	static public function upgradeUnionTechNeedCoin($techId, $nextLevel) {
		$cost      = 0;
		$nextLevel = intval($nextLevel);
		$baselist  = M_Config::getVal('union_tech');
		if (isset($baselist[$techId][$nextLevel]) && $nextLevel > 0) {
			list($lv, $add, $cost) = $baselist[$techId][$nextLevel];
		}
		return round($cost);
	}


	static public function upgradeUnionTechNeedLevel($techId, $nextLevel) {
		$cost      = 0;
		$lv        = 1;
		$nextLevel = intval($nextLevel);
		$baselist  = M_Config::getVal('union_tech');
		if (isset($baselist[$techId][$nextLevel]) && $nextLevel > 0) {
			list($lv, $add, $cost) = $baselist[$techId][$nextLevel];
		}
		return $lv;
	}

	/**
	 * 根据购买军令次数计算所需军饷数[每次固定10军令]
	 * @author chenhui on 20120220
	 * @param int $times 购买次数
	 * @return int 所需军饷数
	 */
	static public function calcBuyMilOrderCost($times) {
		$times = intval($times);
		$ret   = 0;
		if ($times > 0 && $times < T_App::SYS_VAL_LIMIT_TOP) {
			$ret = $times * 20;
		}
		return $ret;
	}

	/**
	 * 根据购买活力次数计算所需军饷数[每次固定10活力]
	 * @author chenhui on 20120220
	 * @param int $times 购买次数
	 * @return int 所需军饷数
	 */
	static public function calcBuyEnergyCost($times) {
		$times = intval($times);
		$ret   = 0;
		if ($times > 0 && $times < T_App::SYS_VAL_LIMIT_TOP) {
			$ret = ($times - 1) * 20 + 20;
		}
		return $ret;
	}

	/**
	 * 根据购买VIP资源包次数计算所需军饷数[每次1个]
	 * @author chenhui on 20120405
	 * @param int $times 购买次数
	 * @return int 所需军饷数
	 */
	static public function calcBuyShopResCost($times) {
		$times = intval($times);
		$times = min(40, $times); //超过按13次算
		$ret   = 0;
		if ($times > 0 && $times < T_App::SYS_VAL_LIMIT_TOP) {
			$ret = ($times - 1) * 5 + 5;
		}
		return $ret;
	}

	/**
	 * 根据功能标签和购买次数计算所需军饷数[每次1个]
	 * @author chenhui on 20120405
	 * @param string $funCode VIP功能标签
	 * @param int $times 购买次数
	 * @return int 所需军饷数
	 */
	static public function calcBuyFunctionCost($funCode, $times) {
		$times   = intval($times);
		$funCode = strval($funCode);
		$ret     = 0;
		if ($times > 0 && $times < T_App::SYS_VAL_LIMIT_TOP) {
			switch ($funCode) {
				case 'GOLD_INCR_YIELD':
				case 'FOOD_INCR_YIELD':
				case 'OIL_INCR_YIELD':
					$ret = ($times - 1) * 10 + 30;
					break;
				case 'ARMY_INCR_ATT':
				case 'ARMY_INCR_DEF':
					$ret = ($times - 1) * 20 + 50;
					break;
				case 'HERO_INCR_ARMY':
					$ret = ($times - 1) * 200 + 100;
					break;
				case 'ARMY_RELIFE':
					$ret = ($times - 1) * 300 + 300;
					break;
				default:
					break;
			}
		}
		return $ret;
	}

	/**
	 * 计算购买次数的所需军饷数[每次1个][通用]
	 * @author chenhui on 20121106
	 * @param array $bout_times_cost
	 * @param int $times 购买次数
	 * @return int 所需军饷数
	 */
	static public function calcAddupPerCost($times_cost, $times) {
		$times = intval($times);
		$ret   = 0;
		if ($times > 0 && $times < T_App::SYS_VAL_LIMIT_TOP) {

			$ret = min($times_cost[1] + $times_cost[2] * ($times - 1), $times_cost[3]);
		}
		return $ret;
	}

	/**
	 * 分阶段消耗军饷
	 * @author huwei
	 * @param array $baseVal array(免费次数,初始花费,累加值,最大值)
	 * @param int $times
	 * @return int
	 */
	static public function stepCost($baseVal, $times) {
		$times = intval($times);
		$ret   = 0;
		//免费次数,初始花费,累加值,最大值
		list($freeNum, $initCoin1, $stepCoin1, $maxCoin1) = $baseVal;
		if ($times > $freeNum) {
			$ret = min($initCoin1 + $stepCoin1 * ($times - $freeNum - 1), $maxCoin1);
		}

		return intval($ret);
	}

	/**
	 * 分阶段消耗军饷
	 * @author huwei
	 * @param array $baseVal array(免费次数,初始花费,累加值,最大值,次数上限)
	 * @param int $times
	 * @return int
	 */
	static public function calcStepCost($baseVal, $times) {
		$times = intval($times);
		$ret   = 0;
		//免费次数,初始花费,累加值,最大值,次数上限
		list($freeNum, $initCoin, $stepCoin, $maxCoin, $limitNum) = $baseVal;

		if ($limitNum > 0 && $times > $limitNum) {
			$ret = -1;
		} else if ($times > $freeNum) {
			$times = $times - $freeNum;
			$ret   = min($initCoin + $stepCoin * ($times - 1), $maxCoin);
		}

		return $ret;
	}

	/**
	 * 探索时间
	 * @author huwei
	 * 第1次到第3次探索    5分钟/次
	 * 第4次到第8次探索    10分钟/次
	 * 第9次到第15次探索    15分钟/次
	 * 第15次探索以上    20分钟/次
	 * @param int $times
	 * @return int
	 */
	static public function calcExploreTimeByTimes($times) {
		if ($times > 15) {
			$time = 18 * T_App::ONE_MINUTE;
		} else if ($times > 8) {
			$time = 12 * T_App::ONE_MINUTE;
		} else if ($times > 3) {
			$time = 8 * T_App::ONE_MINUTE;
		} else {
			$time = 5 * T_App::ONE_MINUTE;
		}
		return $time;
	}

	/**
	 * 计算据点下一个开启间隔几天
	 * @param int $week
	 * @param int $curWeek
	 * @return int
	 */
	static public function calcCampOpenNextWeek($week, $curWeek) {
		$n = 0;
		while ($n < 7) {
			$curWeek = ($curWeek == 6) ? 0 : $curWeek + 1;
			$n++;
			if ((M_Campaign::$campOpenWeek[$curWeek] & $week) > 0) {
				break;
			}

		}
		return $n;
	}

	/**
	 * 经验衰减计算
	 * @param int $atkMaxLv
	 * @param int $defMaxLv
	 * @return array (进攻方经验百分比,防御方经验百分比)
	 */
	static public function expDecay($atkMaxLv, $defMaxLv) {
		$diffLv = abs($atkMaxLv - $defMaxLv);

		if ($diffLv < 4) {
			$n = 100;
		} else if ($diffLv < 7) {
			$n = 98;
		} else if ($diffLv < 10) {
			$n = 96;
		} else if ($diffLv < 13) {
			$n = 94;
		} else if ($diffLv < 16) {
			$n = 92;
		} else if ($diffLv < 19) {
			$n = 90;
		} else if ($diffLv < 22) {
			$n = 88;
		} else if ($diffLv < 25) {
			$n = 86;
		} else {
			$n = 84;
		}

		if ($atkMaxLv > $defMaxLv) {
			$atkn = $n;
			$defn = 100;
		} else {
			$atkn = 100;
			$defn = $n;
		}
		return array($atkn, $defn);
	}

	/**
	 * 数组数据分页
	 * @param array $arr 要分页的数组
	 * @param int $page 当前页
	 * @param int $len 每页数据长度
	 * @return array array(总页数,当前页数, ID列表)
	 */
	static public function arrPage($arr, $page, $len = 10) {
		$totalNum  = count($arr);
		$totalPage = ceil($totalNum / $len);
		$curPage   = min(max($page, 1), $totalPage);
		$start     = ($curPage - 1) * $len;
		$ids       = array_slice($arr, $start, $len);
		return array(intval($totalPage), intval($curPage), $ids);
	}
}

?>