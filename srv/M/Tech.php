<?php

class M_Tech {
	/** 粮食 科技ID */
	const ID_FOOD = 1;
	/** 石油 科技ID */
	const ID_OIL = 2;
	/** 金钱 科技ID */
	const ID_GOLD = 3;
	/** 复活 科技ID */
	const ID_RELIVE = 4;

	/** 步兵生命 */
	const ID_FOOT_L = 5;
	/** 步兵攻击 */
	const ID_FOOT_A = 6;
	/** 步兵防御 */
	const ID_FOOT_D = 7;
	/** 步兵速度 */
	const ID_FOOT_S = 8;

	/** 炮兵生命 */
	const ID_GUN_L = 9;
	/** 炮兵攻击 */
	const ID_GUN_A = 10;
	/** 炮兵防御 */
	const ID_GUN_D = 11;
	/** 炮兵速度 */
	const ID_GUN_S = 12;

	/** 装甲生命 */
	const ID_ARMOR_L = 13;
	/** 装甲攻击 */
	const ID_ARMOR_A = 14;
	/** 装甲防御  */
	const ID_ARMOR_D = 15;
	/** 装甲速度 */
	const ID_ARMOR_S = 16;

	/** 航空生命 */
	const ID_AIR_L = 17;
	/** 航空攻击 */
	const ID_AIR_A = 18;
	/** 航空防御  */
	const ID_AIR_D = 19;
	/** 航空速度 */
	const ID_AIR_S = 20;

	/** 兵种对应科技ID */
	static $armyTechId = array(
		M_Army::ID_FOOT  => array(
			'A'             => M_Tech::ID_FOOT_A,
			'L'             => M_Tech::ID_FOOT_L,
			'D'             => M_Tech::ID_FOOT_D,
			'S'             => M_Tech::ID_FOOT_S,
			'ArmyRelifeAdd' => M_Tech::ID_RELIVE,
		),
		M_Army::ID_GUN   => array(
			'A'             => M_Tech::ID_GUN_A,
			'L'             => M_Tech::ID_GUN_L,
			'D'             => M_Tech::ID_GUN_D,
			'S'             => M_Tech::ID_GUN_S,
			'ArmyRelifeAdd' => M_Tech::ID_RELIVE,
		),
		M_Army::ID_ARMOR => array(
			'A'             => M_Tech::ID_ARMOR_A,
			'L'             => M_Tech::ID_ARMOR_L,
			'D'             => M_Tech::ID_ARMOR_D,
			'S'             => M_Tech::ID_ARMOR_S,
			'ArmyRelifeAdd' => M_Tech::ID_RELIVE,
		),
		M_Army::ID_AIR   => array(
			'A'             => M_Tech::ID_AIR_A,
			'L'             => M_Tech::ID_AIR_L,
			'D'             => M_Tech::ID_AIR_D,
			'S'             => M_Tech::ID_AIR_S,
			'ArmyRelifeAdd' => M_Tech::ID_RELIVE,
		)
	);

	/** 兵种对应科技效果 */
	static $armyTechEff = array(
		M_Army::ID_FOOT  => array(
			'A'             => 'FOOT_INCR_ATT',
			'L'             => 'FOOT_INCR_LIFE',
			'D'             => 'FOOT_INCR_DEF',
			'S'             => 'FOOT_INCR_SP',
			'ArmyRelifeAdd' => 'ARMY_RELIFE'
		),
		M_Army::ID_GUN   => array(
			'A'             => 'GUN_INCR_ATT',
			'L'             => 'GUN_INCR_LIFE',
			'D'             => 'GUN_INCR_DEF',
			'S'             => 'GUN_INCR_SP',
			'ArmyRelifeAdd' => 'ARMY_RELIFE'
		),
		M_Army::ID_ARMOR => array(
			'A'             => 'ARMOR_INCR_ATT',
			'L'             => 'ARMOR_INCR_LIFE',
			'D'             => 'ARMOR_INCR_DEF',
			'S'             => 'ARMOR_INCR_SP',
			'ArmyRelifeAdd' => 'ARMY_RELIFE'
		),
		M_Army::ID_AIR   => array(
			'A'             => 'AIR_INCR_ATT',
			'L'             => 'AIR_INCR_LIFE',
			'D'             => 'AIR_INCR_DEF',
			'S'             => 'AIR_INCR_SP',
			'ArmyRelifeAdd' => 'ARMY_RELIFE'
		)
	);

	/** 基础科技 */
	const TYPE_BASE = 1;
	/** 步兵科技 */
	const TYPE_FOOT = 2;
	/** 炮兵科技 */
	const TYPE_GUN = 3;
	/** 装甲科技 */
	const TYPE_ARMOR = 4;
	/** 航空科技 */
	const TYPE_AIR = 5;
	/** 科技类型 */
	static $type = array(
		M_Tech::TYPE_BASE  => '基础科技',
		M_Tech::TYPE_FOOT  => '步兵科技',
		M_Tech::TYPE_GUN   => '炮兵科技',
		M_Tech::TYPE_ARMOR => '装甲科技',
		M_Tech::TYPE_AIR   => '航空科技',
	);


	/**
	 * 根据科技ID获取科技基础数据
	 * @author chenhui    on 20110414
	 * @param int tech_id 科技ID
	 * @return array 科技基础数据(一维数组)
	 */
	static public function baseInfo($techId) {
		$apcKey = T_Key::BASE_TECH . '_' . $techId;
		$info   = B_Cache_APC::get($apcKey);
		if (empty($info)) {
			$info = B_DB::instance('BaseTech')->get($techId);
			APC::set($apcKey, $info);
		}
		return $info;
	}


	/**
	 * 根据科技ID获取科技升级数据
	 * @author chenhui    on 20110414
	 * @param int tech_id 科技ID
	 * @return array 科技升级数据(二维数组)
	 */
	static public function baseUpgInfo($techId) {
		$listData = M_Base::techAll();
		return isset($listData[$techId]['upg']) ? $listData[$techId]['upg'] : array();
	}

	/**
	 * 根据科技ID和等级获取科技升级数据
	 * @author chenhui    on 20110414
	 * @param int tech_id 科技ID
	 * @param int level 科技等级
	 * @return array 科技升级数据(一维数组)
	 */
	static public function getUpgInfoByLevel($techId, $level) {
		$infoall = M_Tech::baseUpgInfo($techId);
		return empty($infoall[$level]) ? array() : $infoall[$level];
	}


	/**
	 * 处理科技CD时间(允许CD队列累计时间4小时)
	 * @author chenhui on 20120830
	 * @param int $cd_tech_num 当前最大允许建筑CD队列数
	 * @param string $str_cd_tech 当前建筑CD时间json字符串
	 * @param int $cost_time 新建筑任务CD时间
	 * @return array(0/1,msg/new_str_cd_tech)
	 */
	static public function cdTech($cd_tech_num, $str_cd_tech, $cost_time) {
		$flag    = T_App::FAIL;
		$content = T_ErrNo::TECH_CD_TIME;

		$nowtime = time();

		$arrTmp = json_decode(M_City::calcCDBuild($str_cd_tech, $nowtime), true);

		if (count($arrTmp) <= $cd_tech_num) {
			$arr_cd_tech = json_decode($str_cd_tech, true);
			for ($i = 0; $i < $cd_tech_num; $i++) {
				if (isset($arr_cd_tech[$i])) {
					$arrT    = explode('_', $arr_cd_tech[$i]);
					$arrT[1] = ($arrT[0] <= $nowtime) ? T_App::ADDUP_CAN : $arrT[1];
					if (T_App::ADDUP_CAN == $arrT[1]) {
						if ($arrT[0] < $nowtime + M_City::CD_TECH_ADDUP_MAX) {
							$endT            = max($nowtime, $arrT[0]) + $cost_time;
							$fT              = ($endT < $nowtime + M_City::CD_TECH_ADDUP_MAX) ? T_App::ADDUP_CAN : T_App::ADDUP_CANT;
							$arr_cd_tech[$i] = implode('_', array($endT, $fT));
							$flag            = T_App::SUCC;
							break;
						}
					}
				} else {
					$fT              = ($cost_time < M_City::CD_TECH_ADDUP_MAX) ? T_App::ADDUP_CAN : T_App::ADDUP_CANT;
					$arr_cd_tech[$i] = implode('_', array($nowtime + $cost_time, $fT));
					$flag            = T_App::SUCC;
					break;
				}

			}

			while (count($arr_cd_tech) < $cd_tech_num) {
				$arr_cd_tech[] = implode('_', array($nowtime, T_App::ADDUP_CAN));
			}

			if (T_App::SUCC == $flag) {
				$content = json_encode($arr_cd_tech);
			}
		}

		return array($flag, $content);
	}

	/**
	 * 同步科技数据至前端接口
	 * @author chenhui on 20110815
	 * @param int $cityId 城市ID
	 */
	static public function syncTechinfo2Front($cityId, $techId, $techInfo) {
		if (!empty($cityId) && !empty($techInfo)) {
			$msRow = array(
				$techId => $techInfo
			);
			M_Sync::addQueue($cityId, M_Sync::KEY_TECH, $msRow);
		}
	}


	/**********科技模块管理后台所需接口*******************/
	/** 删除科技 基础 数据 缓存 */
	static public function delTechBaseCache() {
		APC::del(T_Key::BASE_TECH); //删除缓存，用完注释
	}

	/** 删除科技 升级 数据 缓存 */
	static public function delTechUpgCache() {
		APC::del(T_Key::UPG_TECH . '*'); //删除缓存，用完注释
	}

}

?>