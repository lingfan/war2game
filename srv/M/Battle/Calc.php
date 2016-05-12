<?php

class M_Battle_Calc {
	//平局
	const BATTLE_FAIL = 1;
	//失败
	const BATTLE_DRAW = 2;
	//获胜
	const BATTLE_WIN = 3;

	//战斗胜负结果对应英雄字段
	static $resultStatus = array(
		self::BATTLE_FAIL => 'fail_num',
		self::BATTLE_DRAW => 'draw_num',
		self::BATTLE_WIN  => 'win_num',
	);

	/** 对已兵种攻击类型 */
	static $useAtkType = array(
		M_Army::ID_FOOT  => 'att_land',
		M_Army::ID_GUN   => 'att_land',
		M_Army::ID_ARMOR => 'att_land',
		M_Army::ID_AIR   => 'att_sky',
	);

	/** 兵种防守类型 */
	static $useDefType = array(
		M_Army::ID_FOOT  => 'def_land',
		M_Army::ID_GUN   => 'def_land',
		M_Army::ID_ARMOR => 'def_land',
		M_Army::ID_AIR   => 'def_sky',
	);

	/** 地形影响攻击加成 */
	static $terrianEff = array(
		T_App::TERRAIN_WATER  => array(M_Army::ID_AIR => '-10'),
		T_App::TERRAIN_FOREST => array(M_Army::ID_FOOT => '-10', M_Army::ID_GUN => '-10', M_Army::ID_ARMOR => '-10'),
	);

	/** 战报类型 进攻 */
	const REPORT_TYPE_ATK = 1;
	/** 战报类型 侦察 */
	const REPORT_TYPE_SCOUT = 2;
	/** 战报类型 占领 */
	//const REPORT_TYPE_HOLD	= 3;
	/** 战报类型 空袭 */
	const REPORT_TYPE_BOMB = 4;

	/**
	 * 获取 通过验证的战斗数据
	 * @author huwei on 20110704
	 * @param int $battleId
	 * @param int $cityId
	 * @return array
	 */
	static public function getVerifyBattleData($battleId, $cityId) {
		$BD      = M_Battle_Info::get($battleId);
		$cityArr = array($BD[T_Battle::CUR_OP_ATK]['CityId'], $BD[T_Battle::CUR_OP_DEF]['CityId']);
		if (!empty($BD) && in_array($cityId, $cityArr)) {
			return $BD;
		}
		return false;
	}

	/**
	 * 添加每步战斗操作记录
	 * @author chenhui on 20110708
	 * @param int $battleId 战斗ID
	 * @param array $row 每行数据
	 * @return bool
	 */
	static public function addOpLog($battleId, $row) {
		$rc           = new B_Cache_RC(T_Key::BATTLE_RECORD_INFO, $battleId);
		$reportData   = $rc->jsonget();
		$reportData   = !empty($reportData) ? $reportData : array();
		$reportData[] = $row;
		$ret          = $rc->jsonset($reportData, T_App::ONE_HOUR);
		return $ret;
	}

	/**
	 * 获取未看的非本人手动战斗操作记录
	 * @author chenhui on 20110708
	 * @param int $battleId 战斗ID
	 * @param int $cityId 本人城市ID
	 * @return array
	 */
	static public function getOpLog($battleId, $cityId) {
		$ret = array();
		if (!empty($battleId) && !empty($cityId)) {
			$rc1        = new B_Cache_RC(T_Key::BATTLE_RECORD_INFO, $battleId);
			$reportData = $rc1->jsonget();

			$rc        = new B_Cache_RC(T_Key::BATTLE_RECORD_NUM, $cityId . '_' . $battleId);
			$record    = $rc->get();
			$record    = !empty($record) ? $record : 0;
			$recordNum = is_array($reportData) ? count($reportData) : 0;
			$arr       = array();
			for ($i = $record; $i < $recordNum + 1; $i++) {
				if (isset($reportData[$i])) {
					if (!($cityId == $reportData[$i][1] && T_Battle::OP_M == $reportData[$i][3])) {
						$arr[] = $reportData[$i];
					}
				}
			}

			Logger::battle("总记录#{$recordNum}玩家记录#{$record}获取战场日志" . json_encode($arr), $battleId, 0);
			$rc->set($recordNum, T_App::ONE_DAY);
			$ret = $arr;
		}
		return $ret;
	}

	/**
	 * 设置从最新战斗操作记录序号开始
	 * @author huwei on 20111020
	 * @param int $battleId 战斗ID
	 * @param int $cityId 本人城市ID
	 * @return bool
	 */
	static public function setNewOpLogNum($battleId, $cityId) {
		if (!empty($battleId) && !empty($cityId)) {
			$rc1        = new B_Cache_RC(T_Key::BATTLE_RECORD_INFO, $battleId);
			$reportData = $rc1->jsonget();
			$recordNum  = is_array($reportData) ? count($reportData) : 0;
			$rc         = new B_Cache_RC(T_Key::BATTLE_RECORD_NUM, $cityId . '_' . $battleId);
			return $rc->set($recordNum, T_App::ONE_DAY);
		}
	}

	/**
	 * 战斗完成后数据存日志
	 * @author chenhui on 20110711
	 * @param int $battleId 战斗ID
	 * @return string 字符串格式日志
	 */
	static public function makeOpLogFile($BD, $reportContent) {
		$battleId = $BD['Id'];

		$rc1            = new B_Cache_RC(T_Key::BATTLE_RECORD_INFO, $battleId);
		$reportOpAction = $rc1->jsonget();
		$fileData       = '';
		if (!empty($reportOpAction)) {
			//基础战场数据格式
			$bgArr = M_Config::getVal('map_war_bg');

			$atkHero = M_Battle_Calc::filterHeroInfo($BD[T_Battle::CUR_OP_ATK]);
			unset($atkHero['ChangeBoutTime']);
			unset($atkHero['SkillEffect']);
			unset($atkHero['PlayFinish']);
			unset($atkHero['IsAI']);
			unset($atkHero['CalcAI']);
			unset($atkHero['ViewRange']);
			$atkHero['HeroPosData'] = $atkHero['InitHeroPosData'];
			unset($atkHero['InitHeroPosData']);
			$defHero = M_Battle_Calc::filterHeroInfo($BD[T_Battle::CUR_OP_DEF]);
			unset($defHero['ChangeBoutTime']);
			unset($defHero['SkillEffect']);
			unset($defHero['PlayFinish']);
			unset($defHero['IsAI']);
			unset($defHero['CalcAI']);
			unset($defHero['ViewRange']);
			$defHero['HeroPosData'] = $defHero['InitHeroPosData'];
			unset($defHero['InitHeroPosData']);

			list($atkPosZ, $atkPosX, $atkPosY) = M_MapWild::calcWildMapPosXYByNo($BD['AtkPos']);
			list($defPosZ, $defPosX, $defPosY) = M_MapWild::calcWildMapPosXYByNo($BD['DefPos']);

			$baseData = array(
				'WarName'        => $BD['MapName'],
				'WarSize'        => $BD['MapSize'],
				//'WarBgNo'		=> $bgArr[$BD['MapBgNo']][1],
				'WarBgNo'        => $BD['MapNo'],
				'WarMapCell'     => base64_encode(M_MapBattle::chrCellData($BD['MapCell'])),
				'WarMapSecne'    => $BD['MapSecne'],
				'Weather'        => $BD['Weather'],
				'AttTime'        => $BD['StartTime'], //战斗开始时间
				'AttCityId'      => $atkHero['CityId'],
				'DefCityId'      => $defHero['CityId'],
				'AttInfo'        => array($atkHero['Nickname'], $atkHero['FaceId'], $atkPosZ, $atkPosX, $atkPosY, $atkHero['Gender']),
				'DefInfo'        => array($defHero['Nickname'], $defHero['FaceId'], $defPosZ, $defPosX, $defPosY, $defHero['Gender']),
				'AtkHero'        => $atkHero,
				'DefHero'        => $defHero,
				'ArmyData'       => $BD['ArmyData'], //基础兵种数据格式
				'WeaponData'     => $BD['WeaponData'], //基础武器数据格式
				'ReportContent'  => $reportContent, //战斗报告数据格式
				'ReportOpAction' => $reportOpAction,
			);
			$fileData = gzcompress(json_encode($baseData), 9);
		}

		$fileName = dechex(crc32(($battleId)));
		$dirname  = date('Ym') . '/' . date('d') . '/' . date('H'); //战报回放二进制文件存储基本目录名

		$no        = B_Cache_File::server(SERVER_NO);
		$prePath   = RPT_PATH . '/' . $no . '/' . $dirname; //战报文件目录名
		$fieldPath = $no . '/' . $dirname . '/' . $fileName; //数据库存储文件名
		$realPath  = RPT_PATH . '/' . $fieldPath; //文件完整路径+文件名
		if (!is_dir($prePath)) {
			@mkdir($prePath, 0777, true); //目录不存在则循环创建
		}

		file_put_contents($realPath, $fileData); //写文件

		return $fieldPath;
	}

	/**
	 * 获取最近的英雄目标
	 * @param array $aPosData 自己的坐标(x,y)
	 * @param array $bHeroPosData 对方的英雄坐标数组
	 * @return int
	 */
	static public function getLastDistanceAim($aPosData, $bHeroPosData) {
		$maxDistance = 1000;
		$heroId      = 0;
		foreach ($bHeroPosData as $key => $posVal) {
			$distance = abs($aPosData[0] - $posVal[0]) * abs($aPosData[1] - $posVal[1]);
			if ($maxDistance > $distance) {
				$maxDistance = $distance;
				$heroId      = $key;
			}
		}
		return $heroId;
	}

	/**
	 * 计算攻击力
	 * @author huwei 20110701
	 * @param array $atkHeroInfo 攻击方英雄信息
	 * @param array $defHeroInfo 防守方英雄信息
	 * @param int $terrianId 地形
	 * @param int $moodId 情绪
	 * @param array $posArr 双方距离
	 * @param  $type 1攻击2反击3被攻击
	 */
	static public function calcAtkForce($atkHeroInfo, $defHeroInfo, $terrianId, $moodId, $posArr = array(), $type) {
		//攻击方一般是玩家
		$atkWeaponId     = $atkHeroInfo['weapon_id'];
		$atkArmyId       = $atkHeroInfo['army_id'];
		$atkArmyNum      = $atkHeroInfo['left_num'];
		$atkWeaponEffect = $atkHeroInfo['add_effect'];
		$defWeaponId     = $defHeroInfo['weapon_id'];
		$defArmyId       = $defHeroInfo['army_id'];
		$critOdds        = 0;
		//攻击类型
		$atkType = self::$useAtkType[$defArmyId];
		//获取攻击科技加成
		$tmp['atkTechAdd'] = $atkHeroInfo['tech_add']['A'];
		//获取道具加成
		$tmp['atkPropsAdd'] = $atkHeroInfo['props_add']['A'];
		//军衔加成
		$tmp['atkRankAdd'] = $atkHeroInfo['rank_add']['A'];
		//获取洲加成
		$tmp['atkZoneAdd'] = $atkHeroInfo['zone_add']['A'];

		//获取装备加成
		//array(几率,[所有0|空1|地2],加成)
		$atkEquipAdd = 0;
		$equipData   = $atkHeroInfo['equip_add'];
		if ($equipData) {
			//Logger::debug(array(__METHOD__, $equipData));
		}

		$atkEquipAdd += self::_calcEquipAddNum($equipData, 'TZ_AL_ATK', $atkType, $defArmyId);

		$tmp['atkEquipAdd'] = $atkEquipAdd;
		if ($atkEquipAdd > 0) {
			//Logger::debug(array(__METHOD__, 'atkEquipAdd', $atkEquipAdd));
		}

		$critOdds += self::_calcEquipAddNum($equipData, 'TZ_CRIT', $atkType, $defArmyId);

		if ($critOdds > 0) {
			//Logger::debug(array(__METHOD__, 'critOdds', $critOdds));
		}

		$hurtEquipAdd = 0;

		$hurtEquipAdd += self::_calcEquipAddNum($equipData, 'TZ_AL_ADD_HURT', $atkType, $defArmyId);

		if ($hurtEquipAdd > 0) {
			//Logger::debug(array(__METHOD__, 'hurtEquipAdd', $hurtEquipAdd));
		}

		//获取技能加成
		//array(几率,[所有0|空1|地2],加成)
		$atkSkillAdd = 0;

		$skillData = $atkHeroInfo['skill_add'];


		$atkSkillAdd += self::_calcSkillAddNum($skillData, 'INCR_ATK', $type, $atkType, $defArmyId);

		$val = self::_calcSkillAddNum($skillData, 'COM_INCR_ATK', $type, $atkType, $defArmyId);
		if (!empty($val)) {
			list($fac, $atkVal) = explode(',', $val);
			$tmpAdd = ceil($atkHeroInfo['total_command'] / $fac) * $atkVal;
			$atkSkillAdd += $tmpAdd;
		}

		$val = self::_calcSkillAddNum($skillData, 'LEA_INCR_ATK', $type, $atkType, $defArmyId);
		if (!empty($val)) {
			list($fac, $atkVal) = explode(',', $val);
			$tmpAdd = ceil($atkHeroInfo['total_lead'] / $fac) * $atkVal;
			$atkSkillAdd += $tmpAdd;
		}

		//步兵部队兵力≤初始兵力50%，攻击加成+200%；步兵部队兵力≤初始兵力25%，攻击加成+400%
		$val = self::_calcSkillAddNum($skillData, 'DECR_ARMY_INCR_ATK', $type, $atkType, $defArmyId);
		if (!empty($val)) {
			$valArr = explode(';', $val);
			$tmpAdd = 0;
			$tmpFac = $atkHeroInfo['left_num'] / $atkHeroInfo['army_num'] * 100;
			foreach ($valArr as $tval) {
				list($fac, $atkVal) = explode(',', $tval);
				if ($tmpFac < $fac) {
					$tmpAdd = $atkVal;
				}
			}
			Logger::debug(array(__METHOD__, 'DECR_ARMY_INCR_ATK', $type, $atkType, $defArmyId, $valArr, "{$atkHeroInfo['left_num']}/{$atkHeroInfo['army_num']}={$tmpFac}", $tmpAdd));
			$atkSkillAdd += $tmpAdd;
		}

		//敌方部队数量＞己方炮兵部队数量150%，加成炮兵伤害+40%
		$val = self::_calcSkillAddNum($skillData, 'GT_ARMY_INCR_ATK', $type, $atkType, $defArmyId);
		if (!empty($val)) {
			$valArr = explode(';', $val);
			$tmpAdd = 0;
			$tmpFac = $defHeroInfo['army_num'] / $atkHeroInfo['army_num'] * 100;
			foreach ($valArr as $tval) {
				list($fac, $atkVal) = explode(',', $tval);
				if ($tmpFac > $fac) {
					$tmpAdd = $atkVal;
				}
			}
			Logger::debug(array(__METHOD__, 'GT_ARMY_INCR_ATK', $type, $atkType, $defArmyId, $valArr, "{$defHeroInfo['army_num']}/{$atkHeroInfo['army_num']}={$tmpFac}", $tmpAdd));
			$atkSkillAdd += $tmpAdd;
		}


		$val = self::_calcSkillAddNum($skillData, 'RANGE_INCR_ATK', $type, $atkType, $defArmyId);
		if (!empty($val)) {
			list($atkPos, $defPos) = $posArr;
			$valArr = explode(';', $val);
			$tmpAdd = 0;
			$tmpFac = M_Formula::aiCalcDistance($atkPos, $defPos);
			foreach ($valArr as $tval) {
				list($fac, $atkVal) = explode(',', $tval);
				if ($tmpFac == $fac) {
					$tmpAdd = $atkVal;
				}
			}
			$atkSkillAdd += $tmpAdd;
		}


		$tmp['atkSkillAdd'] = $atkSkillAdd;

		$val = self::_calcSkillAddNum($skillData, 'MIL_INCR_CRIT', $type, $atkType, $defArmyId);
		if (!empty($val)) { //军事增加暴击几率
			list($fac, $atkVal) = explode(',', $val);
			$tmpAdd = ceil($atkHeroInfo['total_military'] / $fac) * $atkVal;
			$critOdds += $tmpAdd;
		}

		$critOdds += self::_calcSkillAddNum($skillData, 'INCR_CRIT', $type, $atkType, $defArmyId);
		$hurtEquipAdd += self::_calcSkillAddNum($skillData, 'ADD_HURT', $type, $atkType, $defArmyId);

		$setDefUnAtk = 0;
		$setDefUnAtk += self::_calcSkillAddNum($skillData, 'UNATK', $type, $atkType, $defArmyId);

		$setDefUnMove = 0;
		$setDefUnMove += self::_calcSkillAddNum($skillData, 'UNMOVE', $type, $atkType, $defArmyId);

		$setDefAtkHurt = 0;
		$setDefUnMove += self::_calcSkillAddNum($skillData, 'ATK_HURT', $type, $atkType, $defArmyId);


		//联盟加暴击几率
		if (!empty($atkHeroInfo['union_add']['INCR_CRIT'])) {
			$critOdds += $atkHeroInfo['union_add']['INCR_CRIT'];
		}

		//军衔加暴击几率
		$critOdds += $atkHeroInfo['rank_add']['INCR_CRIT'];

		//vip加成
		$tmp['atkVipAdd'] = $atkHeroInfo['vip_add']['A'];

		//通过防御方兵种类型获取基础攻击力
		$baseForce = $atkHeroInfo[$atkType];
		//英雄加成(装备属性点已加入英雄属性点)
		$tmp['atkHeroAdd'] = ceil($atkHeroInfo['total_command'] / 10);
		//计算是否对防御方的武器有加成
		$addEffectInfo       = json_decode($atkWeaponEffect, true);
		$tmp['atkWeaponAdd'] = isset($addEffectInfo[$defWeaponId]) ? $addEffectInfo[$defWeaponId] : 0;
		//地形加成
		$tmp['terrianAdd'] = self::_getTerrianAdd($terrianId, $atkArmyId);
		//情绪加成
		$atkMoodType       = array(T_Battle::MOOD_INCR_ATK, T_Battle::MOOD_DECR_ATK);
		$tmp['atkMoodAdd'] = in_array($moodId, $atkMoodType) ? T_Battle::$moodType[$moodId]['val'] : 0;
		//联盟加成
		$tmp['unionAdd'] = isset($atkHeroInfo['union_add'][$atkArmyId]) ? $atkHeroInfo['union_add'][$atkArmyId] : 0;
		$tmp['base']     = 100;
		$addForce        = array_sum(array_values($tmp)) / 100;
		$tmpForce        = M_Formula::calcBattleForce($baseForce, $addForce, $atkArmyNum);
		//Logger::debug('----------$critOdds > 0 && B_Utils::odds($critOdds) ? true : false;----------'.$critOdds);
		$ret['crit']          = $critOdds > 0 && B_Utils::odds($critOdds) ? true : false;
		$ret['force']         = M_Formula::calcAtkCritForce($tmpForce, $ret['crit']);
		$ret['add_hurt']      = $hurtEquipAdd;
		$ret['setDefAtkHurt'] = $setDefAtkHurt; //持续伤害
		$ret['setDefUnAtk']   = $setDefUnAtk;
		$ret['setDefUnMove']  = $setDefUnMove;
		//Logger::debug(array(__METHOD__, 'atk', "{$atkHeroInfo['city_id']}#{$atkHeroInfo['id']}", $baseForce, $addForce, $tmpForce, $tmp, $ret));
		return $ret;

	}

	/**
	 * 计算防御力
	 * @author huwei 20110701
	 * @param array $atkHeroInfo 攻击方英雄信息
	 * @param array $defHeroInfo 防守方英雄信息
	 */
	static public function calcDefForce($atkHeroInfo, $defHeroInfo, $terrianId, $moodId, $type) {
		$atkArmyId   = $atkHeroInfo['army_id'];
		$atkWeaponId = $atkHeroInfo['weapon_id'];
		$defWeaponId = $defHeroInfo['weapon_id'];
		$defArmyId   = $defHeroInfo['army_id'];
		$defCityId   = isset($defHeroInfo['city_id']) ? $defHeroInfo['city_id'] : 0;
		$defArmyNum  = $defHeroInfo['left_num'];
		//攻击方一般是玩家

		//通过攻击方兵种类型获取防御类型,计算防守方防御力
		$defType   = self::$useDefType[$atkArmyId];
		$baseForce = $defHeroInfo[$defType];
		//获取防御科技加成
		$tmp['defTechAdd'] = $defHeroInfo['tech_add']['D'];
		//获取装备加成
		//array(几率,[所有0|空1|地2],加成)
		$defEquipAdd = 0;
		$equipData   = $defHeroInfo['equip_add'];

		$defEquipAdd += self::_calcEquipAddNum($equipData, 'TZ_AL_DEF', $defType, $atkArmyId);

		$tmp['defEquipAdd'] = $defEquipAdd;
		if ($defEquipAdd > 0) {
			//Logger::debug(array(__METHOD__, 'defEquipAdd', $defEquipAdd));
		}

		$hurtEquipDef = 0;
		$hurtEquipDef += self::_calcEquipAddNum($equipData, 'TZ_AL_DEF_HURT', $defType, $atkArmyId);

		if ($hurtEquipDef > 0) {
			//Logger::debug(array(__METHOD__, 'hurtEquipDef', $hurtEquipDef));
		}

		//获取防御技能加成
		//array(几率,[所有0|空1|地2],加成)
		$skillData   = $defHeroInfo['skill_add'];
		$defSkillAdd = 0;
		//Logger::debug('------def---------'.json_encode($defHeroInfo['skill_add']));

		$defSkillAdd += self::_calcSkillAddNum($skillData, 'INCR_DEF', $type, $defType, $atkArmyId);


		$val = self::_calcSkillAddNum($skillData, 'LEA_INCR_DEF', $type, $defType, $atkArmyId);
		if (!empty($val)) {
			list($fac, $defVal) = explode(',', $val);
			$tmpAdd = ceil($defHeroInfo['total_lead'] / $fac) * $defVal;
			$defSkillAdd += $tmpAdd;
		}

		$tmp['defSkillAdd'] = $defSkillAdd;

		$hurtEquipDef += self::_calcSkillAddNum($skillData, 'DEL_HURT', $type, $defType, $atkArmyId);


		$missOdds = 0;
		$missOdds += self::_calcSkillAddNum($skillData, 'INCR_MISS', $type, $defType, $atkArmyId);


		$setDefRestorAn = 0;
		$setDefRestorAn += self::_calcSkillAddNum($skillData, 'RESTOR_AN', $type, $defType, $atkArmyId);

		//获取防御道具加成
		$tmp['defPropsAdd'] = $defHeroInfo['props_add']['D'];
		//获取防御军衔加成
		$tmp['defRankAdd'] = $defHeroInfo['rank_add']['D'];
		//获取防御洲加成
		$tmp['defZoneAdd']   = $defHeroInfo['zone_add']['D'];
		$tmp['defWeaponAdd'] = 0;
		//地形加成
		$tmp['terrianAdd'] = 0;
		//vip加成
		$tmp['defVipAdd'] = $defHeroInfo['vip_add']['A'];
		//英雄加成(装备属性点已加入英雄属性点)
		$tmp['defHeroAdd'] = ceil($defHeroInfo['total_lead'] / 10);
		//情绪加成
		$defMoodType       = array(T_Battle::MOOD_INCR_DEF, T_Battle::MOOD_DECR_DEF);
		$tmp['defMoodAdd'] = in_array($moodId, $defMoodType) ? T_Battle::$moodType[$moodId]['val'] : 0;
		//联盟加成
		$tmp['unionAdd']       = isset($defHeroInfo['union_add'][$defArmyId]) ? $defHeroInfo['union_add'][$defArmyId] : 0;
		$tmp['base']           = 100;
		$addForce              = array_sum(array_values($tmp)) / 100;
		$ret['force']          = M_Formula::calcBattleForce($baseForce, $addForce, $defArmyNum);
		$ret['miss']           = B_Utils::odds($missOdds);
		$ret['def_hurt']       = $hurtEquipDef;
		$ret['setDefRestorAn'] = $setDefRestorAn; //恢复兵数
		//Logger::debug(array(__METHOD__, 'def', "{$defHeroInfo['city_id']}#{$defHeroInfo['id']}", $baseForce, $addForce, $tmp, $ret));
		return $ret;
	}

	/**
	 * 计算战斗单个兵种生命值
	 * @author huwei 20110701
	 * @param array $heroInfo 英雄信息
	 */
	static public function calcBattleHp($heroInfo, $type) {
		$armyId  = $heroInfo['army_id'];
		$cityId  = isset($heroInfo['city_id']) ? $heroInfo['city_id'] : 0;
		$armyNum = $heroInfo['army_num'];
		//获取生命科技加成
		$tmp['armyTechAdd'] = $heroInfo['tech_add']['L'];
		//获取生命英雄加成(装备属性点已加入英雄属性点)
		$tmp['heroAdd'] = ceil($heroInfo['total_military'] / 10);
		//获取生命技能加成
		//array(几率,[所有0|空1|地2],加成)
		$skillData = $heroInfo['skill_add'];
		$skillAdd  = 0;
		//Logger::debug("LIF=========================".json_encode($skillData));

		$skillAdd += self::_calcSkillAddNum($skillData, 'INCR_LIF', $type);

		if (isset($skillData['INCR_LIF']) && ($skillData['INCR_LIF'][2] == $type || $skillData['INCR_LIF'][2] == 'ATK&DEF')) {
			if (B_Utils::odds($skillData['INCR_LIF'][0])) {
				$skillAdd = $skillData['INCR_LIF'][1];
				//Logger::debug("INCR_LIF=========================".$skillData['INCR_LIF'][1]);
			}
		}

		//军事增加生命
		$val = self::_calcSkillAddNum($skillData, 'MIL_INCR_LIF', $type);
		if (!empty($val)) {
			list($fac, $facnum) = explode(',', $val);
			$tmpAdd = ceil($heroInfo['total_military'] / $fac) * $facnum;
			$skillAdd += $tmpAdd;
		}

		$equipData = $heroInfo['equip_add'];
		$equipAdd  = 0;
		if (isset($equipData['TZ_AL_LIFE'])) {
			$equipAdd = $equipData['TZ_AL_LIFE'][0];
		}

		$tmp['skillAdd'] = $skillAdd;

		$tmp['equipAdd'] = $equipAdd;
		//获取生命道具加成
		$tmp['propsAdd'] = $heroInfo['props_add']['L'];
		//获取生命军衔加成
		$tmp['rankAdd'] = $heroInfo['rank_add']['L'];
		//获取生命洲加成
		$tmp['zoneAdd'] = $heroInfo['zone_add']['L'];
		//获取生命VIP加成
		$tmp['vipAdd'] = $heroInfo['vip_add']['L'];
		//联盟加成
		$tmp['unionAdd'] = isset($heroInfo['union_add'][$armyId]) ? $heroInfo['union_add'][$armyId] : 0;
		$tmp['base']     = 100;
		$totalAdd        = array_sum(array_values($tmp)) / 100;
		return $heroInfo['life_value'] * $totalAdd;
	}

	/**
	 * 获取兵种对应的科技加成
	 * @author huwei on 20110701
	 * @param int $cityId
	 * @param int $armyId
	 * @return array array('A'=>0,'D'=>0,'L'=>0,'ArmyRelifeAdd'=>0)
	 */
	static public function getArmyTechAdd($techInfo, $armyId) {
		$attrAddArr = array('A' => 0, 'D' => 0, 'L' => 0, 'ArmyRelifeAdd' => 0);
		if (!empty($techInfo)) {
			foreach ($attrAddArr as $key => $val) {
				//兵种对应攻击科技ID
				$armyTechId = M_Tech::$armyTechId[$armyId][$key];
				//当前兵种科技等级
				$armyTechLv = isset($techInfo[$armyTechId]) ? $techInfo[$armyTechId] : 0;

				//兵种科技详细信息
				$armyTechInfo = M_Tech::getUpgInfoByLevel($armyTechId, $armyTechLv);
				if (isset($armyTechInfo['effect'])) {
					//兵种对应攻击科技效果定义
					$techEff          = M_Tech::$armyTechEff[$armyId][$key];
					$effect           = json_decode($armyTechInfo['effect'], true);
					$attrAddArr[$key] = isset($effect[$techEff]) ? $effect[$techEff] : 0;
				}
			}
		}

		return $attrAddArr;
	}

	/**
	 * 获取城市影响战斗的道具加成
	 * @author huwei on 20110701
	 * @param int $cityId
	 * @return array array('A'=>0,'D'=>0,'L'=>0)
	 */
	static public function getBattleAddByCityPorps($usePropsInfo) {
		//$attrAddArr = array('A'=>0,'D'=>0,'L'=>0);
		$propsAdd = array('A' => 0, 'D' => 0, 'L' => 0, 'HeroExpAdd' => 0, 'ArmyExpAdd' => 0, 'ArmyRelifeAdd' => 0);
		if (!empty($usePropsInfo)) {
			//攻击加成
			if (!empty($usePropsInfo['ARMY_INCR_ATT'])) {
				$ret           = $usePropsInfo['ARMY_INCR_ATT'];
				$propsAdd['A'] = isset($ret['effect_val']) ? $ret['effect_val'] : 0;
			}

			//防御加成
			if (!empty($usePropsInfo['ARMY_INCR_DEF'])) {
				$ret           = $usePropsInfo['ARMY_INCR_DEF'];
				$propsAdd['D'] = isset($ret['effect_val']) ? $ret['effect_val'] : 0;
			}

			//生命加成
			if (!empty($usePropsInfo['ARMY_INCR_LIFE'])) {
				$ret           = $usePropsInfo['ARMY_INCR_LIFE'];
				$propsAdd['L'] = isset($ret['effect_val']) ? $ret['effect_val'] : 0;
			}

			//英雄经验加成
			if (!empty($usePropsInfo['HERO_WAR_EXP_INCR'])) {
				$ret                    = $usePropsInfo['HERO_WAR_EXP_INCR'];
				$propsAdd['HeroExpAdd'] = isset($ret['effect_val']) ? $ret['effect_val'] : 0;
			}

			//兵种经验加成
			if (!empty($usePropsInfo['ARMY_WAR_EXP_INCR'])) {
				$ret                    = $usePropsInfo['ARMY_WAR_EXP_INCR'];
				$propsAdd['ArmyExpAdd'] = isset($ret['effect_val']) ? $ret['effect_val'] : 0;
			}

			//兵复活加成
			if (!empty($usePropsInfo['ARMY_RELIFE'])) {
				$ret                       = $usePropsInfo['ARMY_RELIFE'];
				$propsAdd['ArmyRelifeAdd'] = isset($ret['effect_val']) ? $ret['effect_val'] : 0;
			}
		}

		return $propsAdd;
	}

	/**
	 * 获取城市影响战斗的道具加成
	 * @author chenhui on 20120602
	 * @return array array('A'=>0,'D'=>0,'L'=>0,'INCR_CRIT'=>0)
	 */
	static public function getBattleAddByMilRank($cityMilRank) {
		//$cityInfo['mil_rank']
		$rankAdd = array('A' => 0, 'D' => 0, 'L' => 0, 'INCR_CRIT' => 0); //攻击、防御、生命、暴击
		if (!empty($cityMilRank)) {
			$confList = M_Config::getVal('mil_rank_renown');
			if (isset($confList[$cityMilRank])) {
				$arr     = explode('_', $confList[$cityMilRank][3]);
				$rankAdd = array('A' => $arr[0], 'D' => $arr[1], 'L' => $arr[2], 'INCR_CRIT' => $arr[3]);
			}
		}

		return $rankAdd;
	}


	/**
	 * 获取影响战斗联盟加成
	 * @author huwei 20111223
	 * @param int $cityId
	 * @return array    兵种 对应的 加成值
	 */
	static public function getBattleAddByUnion($unionInfo) {
		$ret = array(
			M_Army::ID_FOOT  => 0,
			M_Army::ID_GUN   => 0,
			M_Army::ID_ARMOR => 0,
			M_Army::ID_AIR   => 0,
			'INCR_CRIT'      => 0,
		);

		if (!empty($unionInfo)) {
			$campAdd = M_Campaign::getAddition(M_Campaign::CAMP_TYPE_MIL, $unionInfo['id']);

			$ret = array(
				M_Army::ID_FOOT  => M_Union::getUnionTechAddition($unionInfo, M_Union::TECH_FOOT) + $campAdd,
				M_Army::ID_GUN   => M_Union::getUnionTechAddition($unionInfo, M_Union::TECH_GUN) + $campAdd,
				M_Army::ID_ARMOR => M_Union::getUnionTechAddition($unionInfo, M_Union::TECH_ARMOR) + $campAdd,
				M_Army::ID_AIR   => M_Union::getUnionTechAddition($unionInfo, M_Union::TECH_AIR) + $campAdd,
				'INCR_CRIT'      => M_Union::getUnionTechAddition($unionInfo, M_Union::TECH_CRIT),
			);
		}

		return $ret;
	}

	/**
	 * 获取城市所在洲影响战斗的加成
	 * @author chenhui on 20111209
	 * @param int $cityId
	 * @return array array('A'=>0,'D'=>0,'L'=>0)
	 */
	static public function getBattleAddByCityZone($zone, $armyId) {
		$zone    = intval($zone);
		$armyId  = intval($armyId);
		$zoneAdd = array('A' => 0, 'D' => 0, 'L' => 0);
		if ($zone > 0 && $armyId > 0) {
			$zoneAdd = M_City::$zone_army_add[$zone][$armyId];
		}
		return $zoneAdd;
	}

	/**
	 * 地形加成
	 * @param int $terrianId 地形ID
	 * @param int $armyId 兵种ID
	 */
	static private function _getTerrianAdd($terrianId, $armyId) {
		return isset(self::$terrianEff[$terrianId][$armyId]) ? self::$terrianEff[$terrianId][$armyId] : 0;
	}

	/**
	 * 点击逃跑退出按钮时，先取剩余部队数量，在按撤退的损失比列30%计算损耗，
	 * 计算公式：逃跑损失部队1=向上取整（剩余部队1*30%），逃跑损失部队2=向上取整（剩余部队2*30%）....
	 * @author huwei on 20111017
	 * @param array $BD
	 */
	static public function calcEscapeLoss(&$BD) {
		$curOp = $BD['CurOp'];
		foreach ($BD[$curOp]['HeroDataList'] as $k => $v) {
			//计算失去士兵数量
			$lossArmyNum                                = ceil($v['left_num'] * 30 / 100);
			$BD[$curOp]['HeroDataList'][$k]['left_num'] = max(0, $v['left_num'] - $lossArmyNum);
		}
	}

	/**
	 * 计算经验值,荣誉值
	 * @author huwei
	 * @param array $BD 战场数据
	 * @return array    荣誉值,攻击方(英雄经验,状态),防御方(英雄经验,状态)
	 */
	static public function calcExp($BD) {
		$now           = time();
		$atkFlag       = $defFlag = $atkValArr = $defValArr = array();
		$defHero       = $atkHero = $atkArmy = $defArmy = array();
		$atkArmyValue  = $defArmyValue = array();
		$atkTotalValue = $defTotalValue = $atkDieValue = $defDieValue = 0;

		$objPlayerAtk = new O_Player($BD[T_Battle::CUR_OP_ATK]['CityId']);
		$atkAddicRate = $objPlayerAtk->City()->getAntiAddicRate();
		$objPlayerDef = new O_Player($BD[T_Battle::CUR_OP_DEF]['CityId']);
		$defAddicRate = $objPlayerDef->City()->getAntiAddicRate();

		$isNPC = empty($BD[T_Battle::CUR_OP_DEF]['CityId']) ? true : false;

		$atkLv = $defLv = array();
		foreach ($BD[T_Battle::CUR_OP_ATK]['HeroDataList'] as $k1 => $v1) {
			$atkLv[] = $v1['level'];
			//计算临时兵种价值
			$tmpArmyValue                 = $v1['army_num'] * $v1['total_value'];
			$tmpValue                     = isset($atkArmyValue[$v1['army_id']]) ? $atkArmyValue[$v1['army_id']] : 0;
			$atkArmyValue[$v1['army_id']] = $tmpValue + $tmpArmyValue;

			$atkValArr[$k1] = $tmpArmyValue;
			$dieNum         = max($v1['army_num'] - $v1['left_num'], 0);

			$atkDieValue += $dieNum * $v1['total_value']; //进攻方所有死兵的总价值

			//如果是副本战斗结束状态直接空闲
			$flag = T_Hero::FLAG_MOVE;
			if (in_array($BD['Type'], array(M_War::BATTLE_TYPE_FB, M_War::BATTLE_TYPE_BOUT, M_War::BATTLE_TYPE_FLOOR))) {
				$flag = T_Hero::FLAG_FREE;
			}

			$dieRate = M_March_Action::FAIL_DEAD_RATE;
			if ($BD['Type'] == M_War::BATTLE_TYPE_CAMP) {
				$dieRate = M_March_Action::FAIL_DEAD_RATE_CAMP;
			}

			$atkFlag[$k1] = ($v1['left_num'] == 0 && $BD['CurWin'] != T_Battle::CUR_OP_ATK) && B_Utils::odds($dieRate) ? T_Hero::FLAG_DIE : $flag;

			$lifeNum = 0;
			if ($atkFlag[$k1] != T_Hero::FLAG_DIE) {
				//复活士兵数量	chenhui20120408改动
				$relifeAdd = isset($v1['props_add']['ArmyRelifeAdd']) ? $v1['props_add']['ArmyRelifeAdd'] : 0;
				$relifeAdd += isset($v1['tech_add']['ArmyRelifeAdd']) ? $v1['tech_add']['ArmyRelifeAdd'] : 0;
				$relifeAdd += isset($v1['vip_add']['ArmyRelifeAdd']) ? $v1['vip_add']['ArmyRelifeAdd'] : 0;
				$tmpRelifeNum = M_Formula::calcPerNum($dieNum, $relifeAdd);
				$lifeNum      = floor(min($tmpRelifeNum, $dieNum));
			}

			$atkArmyRelifeNum[$k1] = $lifeNum;

			$newArmyNum = $v1['left_num'] + $atkArmyRelifeNum[$k1];

			$atkArmyNum[$k1] = $newArmyNum;
		}
		$atkTotalNum   = array_sum($atkValArr);
		$atkTotalValue = max($atkTotalNum, 1);

		foreach ($BD[T_Battle::CUR_OP_DEF]['HeroDataList'] as $k2 => $v2) {
			$defLv[] = $v2['level'];
			//计算临时兵种价值
			$tmpArmyValue                 = $v2['army_num'] * $v2['total_value'];
			$tmpValue                     = isset($defArmyValue[$v2['army_id']]) ? $defArmyValue[$v2['army_id']] : 0;
			$defArmyValue[$v2['army_id']] = $tmpValue + $tmpArmyValue;

			$defValArr[$k2] = $tmpArmyValue;
			$dieNum         = max($v2['army_num'] - $v2['left_num'], 0);
			$defDieValue += $dieNum * $v2['total_value']; //防守方所有死兵的总价值

			if ($isNPC) {
				$defFlag[$k2] = T_Hero::FLAG_FREE;
			} else {
				$dieRate = M_March_Action::FAIL_DEAD_RATE;
				if ($BD['Type'] == M_War::BATTLE_TYPE_CAMP) {
					$dieRate = M_March_Action::FAIL_DEAD_RATE_CAMP;
				}

				$defFlag[$k2] = ($v2['left_num'] == 0 && $BD['CurWin'] != T_Battle::CUR_OP_DEF) && B_Utils::odds($dieRate) ? T_Hero::FLAG_DIE : T_Hero::FLAG_FREE;
			}

			$lifeNum = 0;
			if ($defFlag[$k2] != T_Hero::FLAG_DIE) {
				//复活士兵数量	chenhui20120408改动
				$relifeAdd = isset($v2['props_add']['ArmyRelifeAdd']) ? $v2['props_add']['ArmyRelifeAdd'] : 0;
				$relifeAdd += isset($v2['tech_add']['ArmyRelifeAdd']) ? $v2['tech_add']['ArmyRelifeAdd'] : 0;
				$relifeAdd += isset($v2['vip_add']['ArmyRelifeAdd']) ? $v2['vip_add']['ArmyRelifeAdd'] : 0;

				$tmpRelifeNum = M_Formula::calcPerNum($dieNum, $relifeAdd);
				$lifeNum      = floor(min($tmpRelifeNum, $dieNum));
			}

			$defArmyRelifeNum[$k2] = $lifeNum;

			$newArmyNum = $v2['left_num'] + $defArmyRelifeNum[$k2];

			$defArmyNum[$k2] = $newArmyNum;

		}
		$defTotalNum   = array_sum($defValArr);
		$defTotalValue = max($defTotalNum, 1);

		//军官死亡产出总经验值
		$atkHeroExp = ceil($atkDieValue / 2000);
		//兵种死亡产出总熟练度
		$atkArmyExp = ceil($atkDieValue / 15000);
		$atkDiePct  = round($atkDieValue / $atkTotalValue, 2);
		$defDiePct  = round($defDieValue / $defTotalValue, 2);

		if (empty($BD[T_Battle::CUR_OP_DEF]['CityId']) && !empty($BD['DefNpcId'])) {
			$npcInfo    = M_NPC::getInfo($BD['DefNpcId']);
			$pct        = floor($defDiePct * $npcInfo['exp_num']);
			$defHeroExp = ceil($pct);
			$defArmyExp = ceil($pct / 5);
		} else {
			//军官死亡产出总经验值
			$defHeroExp = ceil($defDieValue / 2000);
			//兵种死亡产出总熟练度
			$defArmyExp = ceil($defDieValue / 15000);
		}
		$atkSumLv = array_sum($atkLv);
		$atkAvgLv = round($atkSumLv / count($atkLv), 2);

		//Logger::debug(array(__METHOD__, $atkDieValue, $defDieValue, $atkHeroExp, $defHeroExp));

		$atkMaxLv = max($atkLv);
		$defMaxLv = max($defLv);
		list($atkn, $defn) = M_Formula::expDecay($atkMaxLv, $defMaxLv);
		$atkHeroExp = ceil($atkHeroExp * $defn / 100); //进攻方产生的英雄经验
		$atkArmyExp = ceil($atkArmyExp * $defn / 100); //进攻方产生的兵种经验
		$defHeroExp = ceil($defHeroExp * $atkn / 100); //防御方产生的英雄经验
		$defArmyExp = ceil($defArmyExp * $atkn / 100); //防御方产生的兵种经验

		//Logger::debug(array(__METHOD__, $atkHeroExp, $defHeroExp));

		//进攻计算英雄获取经验
		$atkArmyExpAdd = $defArmyExpAdd = 0;
		foreach ($BD[T_Battle::CUR_OP_ATK]['HeroDataList'] as $k3 => $v3) {
			$atkHero[$k3]['id'] = $k3;

			//计算进攻方英雄经验值
			$expNum = $atkValArr[$k3] / $atkTotalValue * $defHeroExp;

			$expAdd              = isset($v3['props_add']['HeroExpAdd']) ? $v3['props_add']['HeroExpAdd'] : 0;
			$tmpExpAdd           = M_Formula::calcAdd($expNum, $expAdd);
			$atkHero[$k3]['exp'] = ceil($tmpExpAdd * $atkAddicRate);

			$atkHero[$k3]['flag']            = $atkFlag[$k3];
			$atkHero[$k3]['die_num']         = $v3['army_num'] - $atkArmyNum[$k3];
			$atkHero[$k3]['old_num']         = $v3['army_num'];
			$atkHero[$k3]['army_num']        = $atkArmyNum[$k3];
			$atkHero[$k3]['army_relife_num'] = $atkArmyRelifeNum[$k3];

			if ($atkFlag[$k3] == T_Hero::FLAG_DIE) {
				$relifeTime                  = M_Formula::heroRelifeTime($v3);
				$atkHero[$k3]['relife_time'] = $now + $relifeTime;
			}
			$atkArmyExpAdd = isset($v3['props_add']['ArmyExpAdd']) ? $v3['props_add']['ArmyExpAdd'] : 0;
		}


		//防御计算英雄获取经验
		foreach ($BD[T_Battle::CUR_OP_DEF]['HeroDataList'] as $k4 => $v4) {
			$defHero[$k4]['id'] = $k4;
			if ($isNPC) {
				$defHero[$k4]['exp'] = 0;
			} else {
				//计算防守方英雄经验值
				$expNum              = $defValArr[$k4] / $defTotalValue * $atkHeroExp;
				$expAdd              = isset($v4['props_add']['HeroExpAdd']) ? $v4['props_add']['HeroExpAdd'] : 0;
				$tmpExpAdd           = M_Formula::calcAdd($expNum, $expAdd);
				$defHero[$k4]['exp'] = ceil($tmpExpAdd * $defAddicRate);
			}


			$defHero[$k4]['flag']     = $defFlag[$k4];
			$defHero[$k4]['die_num']  = $v4['army_num'] - $defArmyNum[$k4];
			$defHero[$k4]['old_num']  = $v4['army_num'];
			$defHero[$k4]['army_num'] = $defArmyNum[$k4];

			$defHero[$k4]['army_relife_num'] = $defArmyRelifeNum[$k4];

			if ($defFlag[$k4] == T_Hero::FLAG_DIE) {
				$relifeTime                  = M_Formula::heroRelifeTime($v4);
				$defHero[$k4]['relife_time'] = $now + $relifeTime;
			}
			$defArmyExpAdd = isset($v4['props_add']['ArmyExpAdd']) ? $v4['props_add']['ArmyExpAdd'] : 0;
		}

		//攻击方兵种熟练度
		foreach ($atkArmyValue as $armyId => $val) {
			//计算进攻方兵种熟练度
			$expNum = $val / $atkTotalValue * $defArmyExp;
			$tmpNum = M_Formula::calcAdd($expNum, $atkArmyExpAdd);
			if ($tmpNum > 0) {
				$atkArmy[$armyId] = ceil($tmpNum * $atkAddicRate);
			}
		}

		//防守方兵种经验值
		foreach ($defArmyValue as $armyId => $val) {
			//防守方兵种熟练度
			$expNum = $val / $defTotalValue * $atkArmyExp;
			$tmpNum = M_Formula::calcAdd($expNum, $defArmyExpAdd);
			if ($tmpNum > 0) {
				$defArmy[$armyId] = ceil($tmpNum * $defAddicRate);
			}
		}

		//威望
		$atkRenownValue = ceil($defDieValue / 50000);
		$defRenownValue = ceil($atkDieValue / 50000);

		//功勋
		$atkWarexpValue = ceil($defDieValue / 10000);
		$defWarexpValue = ceil($atkDieValue / 10000);

		$ret = array(
			'atkRenownValue' => $atkRenownValue,
			'defRenownValue' => $defRenownValue,
			'atkWarexpValue' => $atkWarexpValue,
			'defWarexpValue' => $defWarexpValue,
			//'atkValue'=>$atkValue,
			//'defValue'=>$defValue,
			'atkHero'        => $atkHero,
			'defHero'        => $defHero,
			'atkArmy'        => $atkArmy,
			'defArmy'        => $defArmy,
			'atkDiePct'      => $atkDiePct, //防守方伤亡比率
			'defDiePct'      => $defDiePct, //防守方伤亡比率//
			'atkAvgLv'       => $atkAvgLv,
		);
		//Logger::debug(array(__METHOD__, $ret));
		return $ret;
	}


	/**
	 * 战斗前端英雄数据
	 * @author huwei
	 * @param array $arr
	 * @param bool $isVideo
	 */
	static public function filterHeroInfo($arr, $isVideo = false) {
		foreach ($arr['HeroDataList'] as $key => $val) {
			$tmp = array(
				'id'             => $val['id'],
				'nickname'       => $val['nickname'],
				'city_id'        => !empty($val['city_id']) ? $val['city_id'] : 0,
				'gender'         => $val['gender'],
				'quality'        => $val['quality'],
				'level'          => $val['level'],
				'face_id'        => $val['face_id'],
				'is_legend'      => $val['is_legend'],
				'army_id'        => $val['army_id'],
				'weapon_id'      => $val['weapon_id'],

				'army_num'       => $val['army_num'],
				'left_num'       => $val['left_num'],
				'view_range'     => $val['view_range'],
				'move_range'     => $val['move_range'],
				'shot_range_min' => $val['shot_range_min'],
				'shot_range_max' => $val['shot_range_max'],
				'move_type'      => $val['move_type'],
				'shot_type'      => $val['shot_type'],
				'left_dmg'       => $val['left_dmg'],
				'atk_hurt'       => $val['atk_hurt'], //持续伤害值
				//'attr_lead'		=> $val['attr_lead'],
				//'attr_command'	=> $val['attr_command'],
				//'attr_military'	=> $val['attr_military'],
				//'attr_energy'		=> $val['attr_energy'],
				//'attr_mood'		=> $val['attr_mood'],
				//'life_value'		=> $val['life_value'],

				//'skill_slot'		=> $val['skill_slot'],
				//'skill_slot_1'	=> $val['skill_slot_1'],
				//'skill_slot_2'	=> $val['skill_slot_2'],

			);
			if ($isVideo) {
				$tmp = implode('|', $tmp);
			}

			$arr['HeroDataList'][$key] = $tmp;
		}
		return $arr;
	}

	/**
	 * 更新战斗英雄初始化标记
	 *
	 */
	static public function updateHeroFlag($heroList) {
		//更新战斗英雄列表
		$tmpHeroData = array();
		foreach ($heroList as $kId => $vData) {
			list($pos, $act, $skillEffect) = $vData;
			$tmpHeroData[$kId] = array($pos, T_Battle::OP_HERO_INIT_FLAG, $skillEffect);
		}
		return $tmpHeroData;
	}

	/**
	 * 玩家是否在查看战场  ,npc为不在线
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $battleId 战场ID
	 * @return bool
	 */
	static public function isViewOL($cityId, $battleId) {
		$now      = time();
		$ret      = false;
		$cityId   = intval($cityId);
		$battleId = intval($battleId);
		if ($cityId > 0 && $battleId > 0) {
			$subKey       = 'B' . $battleId;
			$rc           = new B_Cache_RC(T_Key::CITY_VISIT, $cityId);
			$lastVistTime = $rc->hget($subKey);
			if ($lastVistTime > 0 && $now - $lastVistTime < 10) {
				$ret = true;
			}
		}
		return $ret;
	}

	/**
	 * 更新完就最后查看战场时间
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $battleId 战场ID
	 * @return bool
	 */
	static public function upViewOl($cityId, $battleId) {
		$ret      = false;
		$now      = time();
		$cityId   = intval($cityId);
		$battleId = intval($battleId);
		if ($cityId > 0 && $battleId > 0) {
			$subKey = 'B' . $battleId;
			$rc     = new B_Cache_RC(T_Key::CITY_VISIT, $cityId);
			$up     = array($subKey => $now);
			$ret    = $rc->hmset($up);
		}
		return $ret;
	}

	/**
	 * 删除最后查看战场时间
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $battleId 战场ID
	 * @return bool
	 */
	static public function delViewOl($cityId, $battleId) {
		$cityId   = intval($cityId);
		$battleId = intval($battleId);
		if ($cityId > 0 && $battleId > 0) {
			$subKey = 'B' . $battleId;
			$rc     = new B_Cache_RC(T_Key::CITY_VISIT, $cityId);
			$ret    = $rc->hdel($subKey);
		}
		return $ret;
	}

	static public function _genReportBin($reportData) {
		$reportOpAction = '';
		//战斗操作数据格式
		foreach ($reportData as $arrRow) {
			$binStr    = '';
			$binBout   = B_Utils::mapDec2Bin($arrRow[0], 2); //2字节当前第几回合
			$binCity   = B_Utils::mapDec2Bin($arrRow[1], 4); //4字节操作方城市ID
			$binType   = B_Utils::mapDec2Bin($arrRow[2], 1); //1字节操作类型
			$binOpType = B_Utils::mapDec2Bin($arrRow[3], 1); //1字节操作方式
			$binLast   = '';
			switch (intval($arrRow[2])) {
				case T_Battle::OP_ACT_MOVE:
					$binId  = B_Utils::mapDec2Bin($arrRow[4], 4); //4字节英雄ID
					$binPos = '';
					foreach ($arrRow[5] as $x_y) {
						$arrPos = explode('_', $x_y);
						$x      = B_Utils::mapDec2Bin($arrPos[0], 2); //2字节X坐标
						$y      = B_Utils::mapDec2Bin($arrPos[1], 2); //2字节Y坐标
						$binPos .= $x . $y;
					}
					$binLast = $binId . $binPos;
					break;
				case T_Battle::OP_ACT_ATK:
					$binId       = B_Utils::mapDec2Bin($arrRow[4], 4); //4字节英雄ID
					$binAimId    = B_Utils::mapDec2Bin($arrRow[5], 4); //4字节目的英雄ID
					$binHurt     = B_Utils::mapDec2Bin($arrRow[6], 2); //2字节伤害值
					$binAttDead  = B_Utils::mapDec2Bin($arrRow[7], 2); //2字节攻击死亡兵数
					$binBack     = B_Utils::mapDec2Bin($arrRow[8], 2); //2字节反击值
					$binDead     = B_Utils::mapDec2Bin($arrRow[9], 2); //2字节死亡兵数
					$binAttType  = B_Utils::mapDec2Bin($arrRow[10], 1); //1字节攻击效果
					$binBackType = B_Utils::mapDec2Bin($arrRow[11], 1); //1字节反击效果
					$binLast     = $binId . $binAimId . $binHurt . $binAttDead . $binBack . $binDead . $binAttType . $binBackType;
					break;
				case T_Battle::OP_ACT_PLOY:
					$arrPos       = explode('_', $arrRow[4]);
					$x            = B_Utils::mapDec2Bin($arrPos[0], 2); //2字节X坐标
					$y            = B_Utils::mapDec2Bin($arrPos[1], 2); //2字节Y坐标
					$binPos       = $x . $y;
					$eff          = $arrRow[5]; //计策效果定义
					$effLen       = B_Utils::mapDec2Bin(mb_strlen($eff), 1); //1字节计策效果定义长度
					$binEffVal    = B_Utils::mapDec2Bin($arrRow[6], 2); //2字节计策效果值
					$binLastRound = B_Utils::mapDec2Bin($arrRow[7], 1); //1字节持续回合数
					$binLast      = $binPos . $effLen . $eff . $binEffVal . $binLastRound;
					break;
				case T_Battle::OP_ACT_END:
					break;
				case T_Battle::OP_ACT_ESC:
					break;
				case T_Battle::OP_ACT_AI:
					$binLast = B_Utils::mapDec2Bin($arrRow[4], 1); //1字节当前AI模式
					break;
				default:
					break;
			}
			$binStr    = $binBout . $binCity . $binType . $binOpType . $binLast;
			$binRowLen = B_Utils::mapDec2Bin(mb_strlen($binStr), 2);
			$reportOpAction .= $binRowLen . $binStr;
		}
		return $reportOpAction;
	}

	/**
	 * 计算装备加成
	 * @param array $equipData
	 * @param string $type
	 * @param string $curEffectType
	 * @param int $armyId
	 * @return int
	 */
	static private function _calcEquipAddNum($equipData, $label, $curEffectType, $armyId) {
		$num = 0;
		if (isset($equipData[$label])) {
			$data       = $equipData[$label];
			$effectType = $data[3];
			$tmpType    = ($effectType == 'SKY') ? 'att_sky' : 'att_land';

			if (empty($effectType) || $tmpType == $curEffectType) { //所有类型有效 || 指定类型有效
				if (empty($data[2]) || ($data[2] == $armyId)) { //所有兵种有效
					$num = $data[0];
					//Logger::debug(array(__METHOD__, func_get_args(), $num));
				}
			}
		}
		return $num;
	}

	/**
	 * 计算技能加成
	 * @param array $skillData
	 * @param string $label
	 * @param string $opType
	 * @param string $curEffectType
	 * @param int $armyId
	 * @return int
	 */
	static private function _calcSkillAddNum($skillData, $label, $opType, $curEffectType = '', $armyId = '') {
		if (isset($skillData['DEL_HURT']) || isset($skillData['DEL_HURT'])) { //调试技能 是否有效果
			Logger::debug(array(__METHOD__, func_get_args()));
		}
		//技能效果值
		$effectVal = 0;
		if (isset($skillData[$label]) && ($skillData[$label][2] == $opType || $skillData[$label][2] == 'ATK&DEF')) { //增加防御力
			$data = $skillData[$label];
			//所有类型有效
			if (B_Utils::odds($data[0])) {
				$tmpType = ($data[5] == 'SKY') ? 'def_sky' : 'def_land';
				if (empty($data[5]) || empty($curEffectType) || $tmpType == $curEffectType) {
					if (empty($data[4]) || empty($armyId) || ($data[4] == $armyId)) { //所有兵种有效
						$effectVal = $data[1];
						//Logger::debug(array(__METHOD__, func_get_args(), $num));
					}
				}
			}
			if ($label == 'DEL_HURT' || $label == 'DEL_HURT') { //调试技能 是否有效果
				Logger::debug(array(__METHOD__, $data, $effectVal));
			}
		}

		return $effectVal;
	}
}

?>