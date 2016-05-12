<?php

class M_War {
	static $NextBattleInfo = null;
	/** 普通系 */
	const MARCH_NOMAL = 0;
	/** 侦察系 */
	const MARCH_SCOUT = 1;
	/** 轰炸系 */
	const MARCH_BOMB = 2;
	/** 间谍系 */
	const MARCH_SPAY = 3;
	/** 出征系 */
	static $marchType = array(
		self::MARCH_NOMAL => '普通系',
		self::MARCH_SCOUT => '侦察系',
		self::MARCH_BOMB  => '轰炸系',
		self::MARCH_SPAY  => '间谍系',
	);

	/** 未删除 */
	const DEL_NO_ONE = 0;
	/** 攻击方已删除 */
	const DEL_ATK = 1;
	/** 防守方已删除 */
	const DEL_DEF = 2;
	/** 已删除 */
	const DEL_ALL = 3;

	/** 未读 */
	const SEE_NO_ONE = 0;
	/** 攻击方已读 */
	const SEE_ATK = 1;
	/** 防守方已读 */
	const SEE_DEF = 2;
	/** 已读 */
	const SEE_ALL = 3;

	/*战役排行取得数据列数 */
	const FB_PASS_RANK_NUM = 3;

	/** 城市 */
	const BATTLE_TYPE_CITY = 1;
	/** 野外NPC */
	const BATTLE_TYPE_NPC = 2;
	/** 副本 */
	const BATTLE_TYPE_FB = 5;
	/** 据点 */
	const BATTLE_TYPE_CAMP = 6;
	/** 突围 */
	const BATTLE_TYPE_BOUT = 7;
	/** 占领城市 */
	const BATTLE_TYPE_OCCUPIED_CITY = 8;
	/** 解救城市 */
	const BATTLE_TYPE_RESCUE = 9;
	/** 爬楼 */
	const BATTLE_TYPE_FLOOR = 10;

	/** 手动战斗 */
	const FIGHT_TYPE_HAND = 0;
	/** 自动战斗 */
	const FIGHT_TYPE_AUTO = 1;
	/** 快速战斗 */
	const FIGHT_TYPE_QUICK = 2;

	/** 每个玩家的每个分类最大战斗报告数量*/
	const MAX_WAR_REPORT_NUM = 200;

	/** 行军攻击方 */
	const MARCH_OWN_ATK = 1;
	/** 行军防守方 */
	const MARCH_OWN_DEF = 2;
	/** 行军所有 */
	const MARCH_OWN_ALL = 3;

	/** 部队编制 */
	static $formation = array(
		//步兵
		M_Army::ID_FOOT  => array(
			0    => T_Lang::C_TROOPS_LEVEL_1,
			10   => T_Lang::C_TROOPS_LEVEL_2,
			30   => T_Lang::C_TROOPS_LEVEL_3,
			100  => T_Lang::C_TROOPS_LEVEL_4,
			300  => T_Lang::C_TROOPS_LEVEL_5,
			1000 => T_Lang::C_TROOPS_LEVEL_6,
			3000 => T_Lang::C_TROOPS_LEVEL_7,
			5000 => T_Lang::C_TROOPS_LEVEL_8
		),
		//装甲部队
		M_Army::ID_ARMOR => array(
			0    => T_Lang::C_TROOPS_LEVEL_1,
			9    => T_Lang::C_TROOPS_LEVEL_2,
			29   => T_Lang::C_TROOPS_LEVEL_3,
			99   => T_Lang::C_TROOPS_LEVEL_4,
			299  => T_Lang::C_TROOPS_LEVEL_5,
			699  => T_Lang::C_TROOPS_LEVEL_6,
			1499 => T_Lang::C_TROOPS_LEVEL_7,
			2999 => T_Lang::C_TROOPS_LEVEL_8
		),
		//炮兵
		M_Army::ID_GUN   => array(
			0    => T_Lang::C_TROOPS_LEVEL_1,
			5    => T_Lang::C_TROOPS_LEVEL_2,
			15   => T_Lang::C_TROOPS_LEVEL_3,
			50   => T_Lang::C_TROOPS_LEVEL_4,
			150  => T_Lang::C_TROOPS_LEVEL_5,
			350  => T_Lang::C_TROOPS_LEVEL_6,
			750  => T_Lang::C_TROOPS_LEVEL_7,
			1500 => T_Lang::C_TROOPS_LEVEL_8
		),
		//空军
		M_Army::ID_AIR   => array(
			0    => T_Lang::C_TROOPS_AIR_LEVEL_1,
			9    => T_Lang::C_TROOPS_AIR_LEVEL_2,
			29   => T_Lang::C_TROOPS_AIR_LEVEL_3,
			100  => T_Lang::C_TROOPS_AIR_LEVEL_4,
			300  => T_Lang::C_TROOPS_AIR_LEVEL_5,
			1000 => T_Lang::C_TROOPS_AIR_LEVEL_6
		)
	);

	/** 最大城市等待数 返回部队 **/
	const MAX_CITY_WAIT_NUM_MARCH = 10;
	/** 最大据点等待数 返回部队 **/
	const MAX_CAMP_WAIT_NUM_MARCH = 10;
	/** 战损开启条件*/
	static $warLoss = array(1, 2, 3, 4, 5, 6, 7, 8);


	/**
	 * 是否拥有侦察的部队
	 * @author huwei on 20110607
	 * @param int $cityId 城市ID
	 * @param array $heroIdList 英雄ID数组
	 * @return bool
	 */
	static public function hasScoutWeapon($cityId, $heroIdList) {
		if (count($heroIdList) == 1) {
			foreach ($heroIdList as $heroId) {
				$heroInfo = M_Hero::getCityHeroInfo($cityId, $heroId);
				if (isset($heroInfo['weapon_id'])) {
					$weaponInfo = M_Weapon::baseInfo($heroInfo['weapon_id']);
					if (isset($weaponInfo['march_type']) && M_War::MARCH_SCOUT == $weaponInfo['march_type']) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * 是否拥有间谍的部队
	 * @author huwei on 20110607
	 * @param int $cityId
	 * @param array $heroIdList
	 * @return bool
	 */
	static public function hasSpayWeapon($cityId, $heroIdList) {
		foreach ($heroIdList as $heroId) {
			$heroInfo = M_Hero::getCityHeroInfo($cityId, $heroId);
			if (isset($heroInfo['weapon_id'])) {
				$weaponInfo = M_Weapon::baseInfo($heroInfo['weapon_id']);
				if (isset($weaponInfo['march_type']) && M_War::MARCH_SCOUT == $weaponInfo['march_type']) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * 是否拥有轰炸的部队
	 * @author huwei on 20110607
	 * @param int $cityId 城市ID
	 * @param array $heroIdList 英雄ID数组
	 * @return bool
	 */
	static public function hasBombWeapon($cityId, $heroIdList) {
		foreach ($heroIdList as $heroId) {
			$heroInfo = M_Hero::getCityHeroInfo($cityId, $heroId);
			if (isset($heroInfo['weapon_id'])) {
				$weaponInfo = M_Weapon::baseInfo($heroInfo['weapon_id']);
				if (isset($weaponInfo['march_type']) && M_War::MARCH_SCOUT == $weaponInfo['march_type']) {
					return true;
				}
			}
		}
		return false;
	}


	/**
	 * 生成战斗地图数据
	 * @author huwei 20110622
	 * @param array $atkHeroList 进攻方英雄列表
	 * @param array $defHeroList 防守方英雄列表
	 * @param int $warMapNo 战斗地图编号
	 * @return array                (编号,标示物坐标列表,地图大小)
	 */
	static private function _buildWarMapData($atkHeroList, $defHeroList, $warMapNo) {
		$result = array();
		$map    = M_MapBattle::getWarMapInfo($warMapNo);

		if (isset($map['id']) && !empty($atkHeroList)) {
			//分解标记数据 [坐标x_y=>array(标记物类型,x,y)]
			$cellArr = json_decode($map['cell_data'], true);
			$pos     = array();
			foreach ($cellArr as $key => $val) {
				$pos[$val[0]][] = $key;
				if (in_array($val[0], array(M_MapBattle::ATT_CELL_BORN, M_MapBattle::DEF_CELL_BORN))) {
					unset($cellArr[$key]);
				}
			}

			//初始化英雄位置分配
			$atkBorn = isset($pos[M_MapBattle::ATT_CELL_BORN]) ? $pos[M_MapBattle::ATT_CELL_BORN] : array();
			$defBorn = isset($pos[M_MapBattle::DEF_CELL_BORN]) ? $pos[M_MapBattle::DEF_CELL_BORN] : array();

			$heroPos = $atkHeroPos = $defHeroPos = array();
			//初始化英雄位置[heroId=>posX_posY]
			foreach ($atkHeroList as $heroId) {
				if (!empty($atkBorn)) {
					$k                     = array_rand($atkBorn, 1);
					$heroPos[$atkBorn[$k]] = array(M_MapBattle::ATT_CELL_BORN, $heroId);
					$atkHeroPos[$heroId]   = $atkBorn[$k];
					unset($atkBorn[$k]);
				}
			}

			//初始化英雄位置[heroId=>posX_posY]
			if (is_array($defHeroList)) {
				foreach ($defHeroList as $heroId) {
					if (!empty($defBorn)) {
						$k                     = array_rand($defBorn, 1);
						$heroPos[$defBorn[$k]] = array(M_MapBattle::DEF_CELL_BORN, $heroId);
						$defHeroPos[$heroId]   = $defBorn[$k];
						unset($defBorn[$k]);
					}
				}
			}


			//构建标记数据地图
			$posList = array();
			for ($x = 0; $x < $map['area_x']; $x++) {
				for ($y = 0; $y < $map['area_y']; $y++) {
					$key = $x . '_' . $y;
					if (isset($cellArr[$key])) {
						$posList[$key] = array((int)$cellArr[$key][0]);
					} else if (isset($heroPos[$key])) {
						$posList[$key] = $heroPos[$key];
					}
				}
			}
			$result['mapNo']      = $warMapNo;
			$result['mapSize']    = array($map['area_x'], $map['area_y']);
			$result['mapCell']    = $posList; //json数据格式
			$result['mapSecne']   = $map['secne_data']; //json数据格式
			$result['mapBgNo']    = $map['bg_no'];
			$result['mapName']    = $map['name'];
			$result['defHeroPos'] = $defHeroPos; //防守方的英雄坐标 [英雄ID=>坐标XY]
			$result['atkHeroPos'] = $atkHeroPos; //攻击方的英雄坐标 [英雄ID=>坐标XY]
		} else {
			$msg = array(__METHOD__, 'WAR MAP NOT EXIST', func_get_args());
			Logger::error($msg);
		}
		return $result;
	}

	/**
	 * 攻打玩家或NPC城市 战斗数据
	 * @author huwei
	 * @param int $atkCityId
	 * @param int $defCityId
	 * @param array $atkHeroList
	 * @param array $atkPos (zone, x, y)
	 * @param array $defPosNo
	 * @param int $atkAi
	 */
	static public function buildNormalWarBattleData($marchInfo) {
		$battleType  = '';
		$result      = false;
		$atkCityId   = $marchInfo['atk_city_id'];
		$defCityId   = $marchInfo['def_city_id'];
		$atkPosNo    = $marchInfo['atk_pos'];
		$defPosNo    = $marchInfo['def_pos'];
		$atkAi       = $marchInfo['auto_fight'];
		$atkHeroList = json_decode($marchInfo['hero_list'], true);
		$mapRow      = M_MapWild::getWildMapInfo($defPosNo);

		if (!empty($mapRow['pos_no']) &&
			$mapRow['city_id'] != $atkCityId &&
			!empty($mapRow['type'])
		) //非空地
		{
			$defCityId = $atkNpcId = $defNpcId = $defLv = $defMarchId = 0;

			$defPosArr = M_MapWild::calcWildMapPosXYByNo($defPosNo);
			//获取战斗地图场景编号
			$warMapNo = M_MapBattle::getMapNoByZone($defPosArr[0], $mapRow['terrain']);
			//获取防守方的部队列表
			$defHeroList = array();
			$atkCityInfo = M_City::getInfo($atkCityId);
			$atkLv       = $atkCityInfo['level'];

			switch ($mapRow['type']) {
				case T_Map::WILD_MAP_CELL_CITY:
					$heroType   = 'city';
					$battleType = M_War::BATTLE_TYPE_CITY;

					$defColonyInfo = M_ColonyCity::getInfo($mapRow['city_id']); //要占领城市信息
					if (!empty($defColonyInfo['atk_city_id'])) //若玩家已被占领，则于占领的城市打斗
					{
						$holdCityId = $defColonyInfo['atk_city_id'];
						list($z, $x, $y) = $defPosArr;
						//被占领城市信息
						$beHoldCityInfo = M_City::getInfo($mapRow['city_id']);

						if ($holdCityId == $atkCityId) { //如果占领方为自己 撤回行军
							$content = array(T_Lang::C_WILD_CITY_NOT_ATK_SELF, $beHoldCityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
							M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
							return false;
						}

						$holdCityInfo = M_City::getInfo($holdCityId);
						if (!empty($atkCityInfo['union_id']) && !empty($holdCityInfo['union_id']) && $atkCityInfo['union_id'] == $holdCityInfo['union_id']) {
							$content = array(T_Lang::C_WILD_CITY_NOT_ATK_UNION, $beHoldCityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
							M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
							return false;
						}

						$content = array(T_Lang::C_WILD_CITY_NOT_ATK, $beHoldCityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
						M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
						return false;
					} else {
						$defCityId   = $mapRow['city_id'];
						$defHeroList = M_Hero::getFightHeroList($defCityId);
						$defCityInfo = M_City::getInfo($defCityId);
					}

					$defLv = $cityLv = $defCityInfo['level'];

					break;
				case T_Map::WILD_MAP_CELL_NPC:
					//攻打野地
					$heroType   = 'npc';
					$battleType = M_War::BATTLE_TYPE_NPC;

					if (!empty($mapRow['city_id'])) {
						$defCityInfo = M_City::getInfo($mapRow['city_id']);
						$defCityId   = $mapRow['city_id'];
						$defNpcId    = $mapRow['npc_id'];
						$defLv       = $defCityInfo['level'];
						if (!empty($mapRow['march_id'])) {
							//如果有玩家占领 获取玩家占领的部队数据
							$defMarchInfo = M_March_Info::get($mapRow['march_id']);
							if (!empty($defMarchInfo['hero_list'])) {
								$defHeroList = json_decode($defMarchInfo['hero_list'], true);
								$defMarchId  = $defMarchInfo['id'];
							}
						}

						//Logger::debug(array(__METHOD__, $defHeroList));
					} else {
						//否则取NPC的部队数据
						$defCityInfo = M_NPC::getInfo($mapRow['npc_id']);
						if ($defCityInfo['type'] == M_NPC::TMP_NPC) //临时NPC数据
						{
							$refreshData = M_NPC::getRandTempNpcRefreshData();
							if (empty($refreshData[$mapRow['npc_id']]['end_time']) ||
								time() > $refreshData[$mapRow['npc_id']]['end_time']
							) {
								//到达时间到达超过结束时间 遣返
								$battleType = ''; //空的战斗类型遣返部队
							}
						} else if ($defCityInfo['type'] == M_NPC::FASCIST_NPC) //固定法西斯
						{
							$refreshData = M_NPC::getFixedTempNpcRefreshData();
							if (empty($refreshData[$mapRow['npc_id']]['end_time']) ||
								time() > $refreshData[$mapRow['npc_id']]['end_time']
							) {
								//到达时间到达超过结束时间 遣返
								$battleType = ''; //空的战斗类型遣返部队
							}
						}
						//Logger::debug(array(__METHOD__, $defCityInfo));
						$defHeroList = json_decode($defCityInfo['army_data'], true);
						$defNpcId    = $defCityInfo['id'];
						$defLv       = $defCityInfo['level'];
					}
					break;
				case T_Map::WILD_MAP_CELL_SPACE:
					$battleType = ''; //空地 遣返部队
					break;
				default:
					$battleType = '';
					$msg        = __METHOD__ . ':' . T_ErrNo::WAR_MAP_TYPE_ERR . ':' . json_encode(func_get_args());
					Logger::debug($msg);
					break;
			}

			//不为空的战斗类型 该坐标存在战斗对象
			if (!empty($battleType)) {
				//默认自动战斗
				$defAi = 1;
				//获取进攻方数据(城市ID,所在州,坐标,是否自动战斗,英雄ID列表)
				$atkHero = array();

				$armyIds        = array();
				$weaponIds      = array();
				$newAtkHeroList = $newDefHeroList = array();

				$atkData = self::_buildBattleCityData($atkCityId, $atkCityInfo);
				foreach ($atkHeroList as $atkHeroId) {
					$heroInfo = M_Hero::buildHeroBattleInfo($atkHeroId, $atkData);
					if ($heroInfo['army_num'] > 0) {
						$atkHero[$atkHeroId]               = $heroInfo;
						$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
						$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
						$newAtkHeroList[]                  = $atkHeroId;
					}
				}

				$result['atkHero'] = $atkHero;

				$result['atkData'] = array($atkCityId, $atkNpcId, $atkPosNo, $atkAi, $atkLv, $atkCityInfo['nickname'], $atkCityInfo['face_id'], $atkCityInfo['gender']);

				$defData = self::_buildBattleCityData($defCityId, $defCityInfo);

				$defHero = array();
				foreach ($defHeroList as $defHeroId) {
					$heroInfo = M_Hero::buildHeroBattleInfo($defHeroId, $defData);
					if ($heroInfo['army_num'] > 0) {
						$defHero[$defHeroId]               = $heroInfo;
						$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
						$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
						$newDefHeroList[]                  = $defHeroId;
					}
				}

				$result['defHero'] = $defHero;
				//获取防御数据
				$gender = !empty($defCityInfo['gender']) ? $defCityInfo['gender'] : 0;

				$objPlayerDef = new O_Player($defCityId);
				if ($objPlayerDef->City()->isOnline()) {
					$defAi = 0;
				}
				$result['defData'] = array($defCityId, $defNpcId, $defPosNo, $defAi, $defLv, $defCityInfo['nickname'], $defCityInfo['face_id'], $gender);

				//获取地图数据
				$result['mapData'] = self::_buildWarMapData($newAtkHeroList, $newDefHeroList, $warMapNo);
				//获取天气
				$result['weather']    = $mapRow['weather'];
				$result['battleType'] = $battleType;

				//获取基础兵种武器数据
				$result['army_data']   = array_values($armyIds);
				$result['weapon_data'] = array_values($weaponIds);
				$result['atkMarchId']  = $marchInfo['id'];
				$result['defMarchId']  = $defMarchId;
			}
		}

		if (empty($battleType)) {
			$content = array(T_Lang::AIM_NOT_EXIST);
			M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content));
		}

		return $result;
	}

	/**
	 * 占领城市 战斗数据
	 * @author duhuihui
	 * @param int $atkCityId
	 * @param int $defCityId
	 * @param array $atkHeroList
	 * @param array $atkPos (zone, x, y)
	 * @param array $defPosNo
	 * @param int $atkAi
	 */
	static public function buildOccupiedWarBattleData($marchInfo) {
		$battleType  = '';
		$result      = false;
		$atkCityId   = $marchInfo['atk_city_id']; //攻击方
		$defCityId   = $marchInfo['def_city_id']; //防守方
		$atkPosNo    = $marchInfo['atk_pos']; //出发坐标
		$defPosNo    = $marchInfo['def_pos']; //目的地坐标
		$atkAi       = $marchInfo['auto_fight'];
		$atkHeroList = json_decode($marchInfo['hero_list'], true); //攻击方英雄列表
		$mapRow      = M_MapWild::getWildMapInfo($defPosNo);

		if (!empty($mapRow['pos_no']) &&
			!empty($mapRow['type']) &&
			$mapRow['type'] == T_Map::WILD_MAP_CELL_CITY
		) //非空地
		{
			$defCityId = $atkNpcId = $defNpcId = $defLv = $defMarchId = 0;

			$defPosArr   = M_MapWild::calcWildMapPosXYByNo($defPosNo);
			$warMapNo    = M_MapBattle::getMapNoByZone($defPosArr[0], $mapRow['terrain']); //获取战斗地图场景编号
			$defHeroList = array(); //获取防守方的部队列表
			$atkCityInfo = M_City::getInfo($atkCityId);
			$atkLv       = $atkCityInfo['level'];
			$heroType    = 'city';
			$battleType  = M_War::BATTLE_TYPE_OCCUPIED_CITY;
			if (!empty($mapRow['city_id'])) {
				$beHoldCityInfo = M_City::getInfo($mapRow['city_id']);
				list($z, $x, $y) = $defPosArr;
				$beHoldCityColonyInfo = M_ColonyCity::getInfo($mapRow['city_id']);
				$defColonyInfo        = M_ColonyCity::getInfo($mapRow['city_id']); //要占领城市信息
				if (!empty($defColonyInfo['atk_city_id'])) //已被占领
				{ //如果有玩家占领 获取玩家占领的部队数据

					$holdCityId = $defColonyInfo['atk_city_id'];

					//被占领城市信息


					if ($holdCityId == $atkCityId) { //如果占领方为自己 撤回行军
						$content = array(T_Lang::C_WILD_CITY_NOT_OCCUPIED_SELF, $beHoldCityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
						M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));

						return false;
					}

					$holdCityInfo = M_City::getInfo($holdCityId);

					//改变行军的防守方数据
					M_March::changeOccupiedMarchData($marchInfo, $holdCityId, $holdCityInfo['nickname']);

					if (!empty($atkCityInfo['union_id']) &&
						!empty($holdCityInfo['union_id']) &&
						$atkCityInfo['union_id'] == $holdCityInfo['union_id']
					) {
						$content = array(T_Lang::C_WILD_CITY_NOT_OCCUPIED_UNION, $beHoldCityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
						M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));

						return false;
					}


					if (!empty($defColonyInfo['atk_march_id'])) {
						$defMarchInfo = M_March_Info::get($defColonyInfo['atk_march_id']);
						$defHeroList  = isset($defMarchInfo['hero_list']) ? json_decode($defMarchInfo['hero_list'], true) : '';
						$defMarchId   = $defMarchInfo['id'];
					}

					$defCityId = $holdCityId;

				} else //未被占领
				{
					$defCityId   = $mapRow['city_id'];
					$defHeroList = M_Hero::getFightHeroList($defCityId);
					if (!empty($atkCityInfo['union_id']) &&
						!empty($beHoldCityInfo['union_id']) &&
						$atkCityInfo['union_id'] == $beHoldCityInfo['union_id']
					) {
						$content = array(T_Lang::C_WILD_CITY_NOT_OCCUPIED_UNION, $beHoldCityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
						M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));

						return false;
					}

				}
				$holdTimeInterval = M_Config::getVal('hold_city_time_interval');
				if ($beHoldCityColonyInfo['hold_time'] > T_App::ONE_HOUR * $holdTimeInterval) {
					$content = array(T_Lang::C_WILD_CITY_NOT_OCCUPIED_OUT, $beHoldCityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y, $holdTimeInterval);
					M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));

					return false;
				}

				$defCityInfo = M_City::getInfo($defCityId);

			}

			//不为空的战斗类型 该坐标存在战斗对象
			if (!empty($battleType)) {
				//获取进攻方数据(城市ID,所在州,坐标,是否自动战斗,英雄ID列表)
				$atkHero = $armyIds = $weaponIds = $newAtkHeroList = $newDefHeroList = array();

				$atkData = self::_buildBattleCityData($atkCityId, $atkCityInfo);

				foreach ($atkHeroList as $atkHeroId) {
					$heroInfo = M_Hero::buildHeroBattleInfo($atkHeroId, $atkData);
					if ($heroInfo['army_num'] > 0) {
						$atkHero[$atkHeroId]               = $heroInfo;
						$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
						$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
						$newAtkHeroList[]                  = $atkHeroId;
					}
				}

				$result['atkHero'] = $atkHero;
				$result['atkData'] = array($atkCityId, $atkNpcId, $atkPosNo, $atkAi, $atkLv, $atkCityInfo['nickname'], $atkCityInfo['face_id'], $atkCityInfo['gender']);

				$defHero = array();
				if (!empty($defHeroList)) {
					$defData = self::_buildBattleCityData($defCityId, $defCityInfo);

					foreach ($defHeroList as $defHeroId) {
						$heroInfo = M_Hero::buildHeroBattleInfo($defHeroId, $defData);
						if ($heroInfo['army_num'] > 0) {
							$defHero[$defHeroId]               = $heroInfo;
							$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
							$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
							$newDefHeroList[]                  = $defHeroId;
						}
					}
				}

				$result['defHero'] = $defHero;
				//获取防御数据
				$gender = !empty($defCityInfo['gender']) ? $defCityInfo['gender'] : 0;
				$defLv  = $defCityInfo['level'];

				$defAi        = 1; //默认自动战斗
				$objPlayerDef = new O_Player($defCityId);
				if ($objPlayerDef->City()->isOnline()) {
					$defAi = 0;
				}

				$result['defData'] = array($defCityId, $defNpcId, $defPosNo, $defAi, $defLv, $defCityInfo['nickname'], $defCityInfo['face_id'], $gender);

				//获取地图数据
				$result['mapData'] = self::_buildWarMapData($newAtkHeroList, $newDefHeroList, $warMapNo);
				//获取天气
				$result['weather']    = $mapRow['weather'];
				$result['battleType'] = $battleType;

				//获取基础兵种武器数据
				$result['army_data']   = array_values($armyIds);
				$result['weapon_data'] = array_values($weaponIds);

				$result['atkMarchId'] = $marchInfo['id'];
				$result['defMarchId'] = $defMarchId;
				//Logger::debug(array(__METHOD__, $result['atkMarchId'], $result['defMarchId']));

			}
		}

		if (empty($battleType)) {
			$content = array(T_Lang::AIM_NOT_EXIST);
			M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content));
		}

		return $result;
	}

	/**
	 * 解救城市 战斗数据
	 * @author huwei
	 * @param int $atkCityId
	 * @param int $defCityId
	 * @param array $atkHeroList
	 * @param array $atkPos (zone, x, y)
	 * @param array $defPosNo
	 * @param int $atkAi
	 */
	static public function buildRescueWarBattleData($marchInfo) {
		$battleType  = '';
		$result      = false;
		$atkCityId   = $marchInfo['atk_city_id'];
		$defCityId   = $marchInfo['def_city_id'];
		$atkPosNo    = $marchInfo['atk_pos'];
		$defPosNo    = $marchInfo['def_pos'];
		$atkAi       = $marchInfo['auto_fight'];
		$atkHeroList = json_decode($marchInfo['hero_list'], true);
		$defHeroList = array();
		$mapRow      = M_MapWild::getWildMapInfo($defPosNo);

		if (!empty($mapRow['pos_no']) &&
			!empty($mapRow['type']) &&
			$mapRow['type'] == T_Map::WILD_MAP_CELL_CITY
		) //非空地
		{
			$defCityId = $atkNpcId = $defNpcId = $defLv = $defMarchId = 0;

			$defPosArr = M_MapWild::calcWildMapPosXYByNo($defPosNo);
			//获取战斗地图场景编号
			list($z, $x, $y) = $defPosArr;
			$warMapNo    = M_MapBattle::getMapNoByZone($defPosArr[0], $mapRow['terrain']);
			$atkCityInfo = M_City::getInfo($atkCityId);
			$atkLv       = $atkCityInfo['level'];

			$heroType   = 'city';
			$battleType = M_War::BATTLE_TYPE_RESCUE;
			if (!empty($mapRow['city_id'])) {
				$holdColonyInfo = M_ColonyCity::getInfo($mapRow['city_id']); //要占领城市信息

				if (!empty($holdColonyInfo['atk_city_id'])) { //已被占领
					$defCityId    = $holdColonyInfo['atk_city_id'];
					$holdCityInfo = M_City::getInfo($defCityId);

					if (!empty($holdCityInfo)) {
						if (!empty($holdColonyInfo['atk_march_id'])) {
							$defMarchInfo = M_March_Info::get($holdColonyInfo['atk_march_id']);
							$defMarchId   = $holdColonyInfo['atk_march_id'];
							$defHeroList  = isset($defMarchInfo['hero_list']) ? json_decode($defMarchInfo['hero_list'], true) : '';
						}

						if (!empty($holdCityInfo['union_id']) &&
							$holdCityInfo['union_id'] == $atkCityInfo['union_id']
						) {
							$beHoldCityInfo = M_City::getInfo($mapRow['city_id']);

							$content = array(T_Lang::C_WILD_CITY_NOT_RESCUE_UNION, $beHoldCityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
							M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));

							return false;
						}

						$defLv       = $cityLv = $holdCityInfo['level'];
						$defCityInfo = $holdCityInfo;
					} else { //占领城市信息为空
						return false;
					}
				} else { //未被占领
					return false;
				}
			}

			//获取进攻方数据(城市ID,所在州,坐标,是否自动战斗,英雄ID列表)
			$atkHero = $armyIds = $weaponIds = $newAtkHeroList = $newDefHeroList = array();

			$atkData = self::_buildBattleCityData($atkCityId, $atkCityInfo);
			foreach ($atkHeroList as $atkHeroId) {
				$heroInfo = M_Hero::buildHeroBattleInfo($atkHeroId, $atkData);
				if ($heroInfo['army_num'] > 0) {
					$atkHero[$atkHeroId]               = $heroInfo;
					$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
					$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
					$newAtkHeroList[]                  = $atkHeroId;
				}
			}

			$result['atkHero'] = $atkHero;
			$result['atkData'] = array($atkCityId, $atkNpcId, $atkPosNo, $atkAi, $atkLv, $atkCityInfo['nickname'], $atkCityInfo['face_id'], $atkCityInfo['gender']);

			$defHero = array();
			$defData = self::_buildBattleCityData($defCityId, $defCityInfo);
			foreach ($defHeroList as $defHeroId) {
				$heroInfo = M_Hero::buildHeroBattleInfo($defHeroId, $defData);
				if ($heroInfo['army_num'] > 0) {
					$defHero[$defHeroId]               = $heroInfo;
					$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
					$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
					$newDefHeroList[]                  = $defHeroId;
				}
			}

			$result['defHero'] = $defHero;
			$gender            = !empty($defCityInfo['gender']) ? $defCityInfo['gender'] : 0;
			$defAi             = 1; //默认自动战斗
			$objPlayerDef      = new O_Player($defCityId);
			if ($objPlayerDef->City()->isOnline()) {
				$defAi = 0;
			}
			$result['defData']     = array($defCityId, $defNpcId, $defPosNo, $defAi, $defLv, $defCityInfo['nickname'], $defCityInfo['face_id'], $gender);
			$result['mapData']     = self::_buildWarMapData($newAtkHeroList, $newDefHeroList, $warMapNo); //获取地图数据
			$result['weather']     = $mapRow['weather']; //获取天气
			$result['battleType']  = $battleType;
			$result['army_data']   = array_values($armyIds); //获取基础兵种数据
			$result['weapon_data'] = array_values($weaponIds); //获取基础武器数据
			$result['atkMarchId']  = $marchInfo['id'];
			$result['defMarchId']  = $defMarchId; //撤回部队 会影响到地图上的marchId 不能用地图上的march_id判断这里
			//Logger::debug(array(__METHOD__, $result['atkMarchId'], $result['defMarchId']));
		}

		if (empty($battleType)) {
			$content = array(T_Lang::AIM_NOT_EXIST);
			M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content));
		}

		return $result;
	}

	/**
	 * 副本战斗数据
	 * @author huwei
	 * @param int $atkCityId
	 * @param array $atkPos
	 * @param array $defPos
	 * @param array $atkHeroList
	 * @param int $atkAi
	 */
	static public function buildFBWarBattleData($atkCityId, $atkPosNo, $defPosNo, $atkHeroList, $atkAi) {
		$result = array();
		list($chapterNo, $campaignNo, $pointNo) = M_Formula::calcParseFBNo($defPosNo);
		$fbData = M_SoloFB::getDetail($chapterNo, $campaignNo, $pointNo);
		if (!empty($fbData)) {
			$defCityId   = $atkNpcId = $defNpcId = $defLv = 0;
			$defAi       = 1;
			$atkCityInfo = M_City::getInfo($atkCityId);
			$atkLv       = $atkCityInfo['level'];

			//副本数据结构
			//关卡名称,地形编号,天气编号,战斗地图编号,NPC部队ID,动画编号,场景对话
			list($pName, $pTerrain, $pWether, $warMapNo, $defNpcId, $cgNo, $dialog) = $fbData;

			$defCityInfo = M_NPC::getInfo($defNpcId);

			$defHeroList = json_decode($defCityInfo['army_data'], true);

			//获取进攻方数据(城市ID,所在州,坐标,是否自动战斗,英雄ID列表)
			$atkHero        = $armyIds = $weaponIds = array();
			$newAtkHeroList = $newDefHeroList = array();

			$atkData = self::_buildBattleCityData($atkCityId, $atkCityInfo);
			//Logger::debug($atkData);
			foreach ($atkHeroList as $atkHeroId) {
				$heroInfo = M_Hero::buildHeroBattleInfo($atkHeroId, $atkData);
				if ($heroInfo['army_num'] > 0) {
					$atkHero[$atkHeroId]               = $heroInfo;
					$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
					$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
					$newAtkHeroList[]                  = $atkHeroId;
				}
			}
			$result['atkHero'] = $atkHero;
			$result['atkData'] = array($atkCityId, $atkNpcId, $atkPosNo, $atkAi, $atkLv, $atkCityInfo['nickname'], $atkCityInfo['face_id'], $atkCityInfo['gender']);

			//获取防御数据
			$defHero = array();
			if (is_array($defHeroList)) {
				$defCityId = 0;
				$defData   = self::_buildBattleCityData($defCityId, $defCityInfo);

				foreach ($defHeroList as $defHeroId) {
					$heroInfo = M_Hero::buildHeroBattleInfo($defHeroId, $defData);

					if ($heroInfo['army_num'] > 0) {
						$defHero[$defHeroId]               = $heroInfo;
						$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
						$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
						$newDefHeroList[]                  = $defHeroId;
					}
				}
			}

			$result['defHero'] = $defHero;
			$gender            = !empty($defCityInfo['gender']) ? $defCityInfo['gender'] : 0;
			$result['defData'] = array($defCityId, $defNpcId, $defPosNo, $defAi, $defLv, $defCityInfo['nickname'], $defCityInfo['face_id'], $gender);

			//获取地图数据
			if ($atkAi == M_War::FIGHT_TYPE_QUICK) { //快速战斗地图 减少寻路和障碍 加快计算
				$quickmapno = M_War::quickmapno();
				if ($quickmapno) {
					$warMapNo = $quickmapno;
				}
			}

			$result['mapData'] = self::_buildWarMapData($newAtkHeroList, $newDefHeroList, $warMapNo);
			//获取天气
			$result['weather']    = T_App::WEATHER_CLEAR;
			$result['battleType'] = M_War::BATTLE_TYPE_FB;
			//获取基础兵种武器数据
			$result['army_data']   = array_values($armyIds);
			$result['weapon_data'] = array_values($weaponIds);
			$result['atkMarchId']  = 0;
			$result['defMarchId']  = 0;

		}
		return $result;
	}

	/**
	 * 突围战斗数据
	 * @author chenhui on 20121025
	 * @param int $atkCityId
	 * @param string $atkPosNo
	 * @param string $defPosNo '突围ID_关编号从1开始'
	 * @param array $npcData npc部队ID,地图ID
	 * @param array $defPos
	 * @param array $atkHeroList
	 * @param int $atkAi
	 * @return array()
	 */
	static public function buildBoutWarBattleData($atkCityId, $atkPosNo, $defPosNo, $arrOutpost, $atkAi, $atkHeroList) // $defPosNo,
	{
		$result      = array();
		$defCityId   = $atkNpcId = 0;
		$defAi       = 1;
		$atkCityInfo = M_City::getInfo($atkCityId);
		$atkLv       = $atkCityInfo['level'];

		$defNpcId    = $arrOutpost[0];
		$defCityInfo = M_NPC::getInfo($defNpcId);
		$defLv       = $defCityInfo['level'];
		$defHeroList = json_decode($defCityInfo['army_data'], true);

		//获取进攻方数据(城市ID,所在州,坐标,是否自动战斗,英雄ID列表)
		$atkHero        = $armyIds = $weaponIds = array();
		$newAtkHeroList = $newDefHeroList = array();

		$atkData = self::_buildBattleCityData($atkCityId, $atkCityInfo);
		foreach ($atkHeroList as $atkHeroId) {
			$heroInfo = M_Hero::buildHeroBattleInfo($atkHeroId, $atkData);
			if ($heroInfo['army_num'] > 0) {
				$atkHero[$atkHeroId]               = $heroInfo;
				$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
				$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
				$newAtkHeroList[]                  = $atkHeroId;
			}
		}
		$result['atkHero'] = $atkHero;
		$result['atkData'] = array($atkCityId, $atkNpcId, $atkPosNo, $atkAi, $atkLv, $atkCityInfo['nickname'], $atkCityInfo['face_id'], $atkCityInfo['gender']);

		//获取防御数据
		$defHero = array();
		if (!empty($defHeroList) && is_array($defHeroList)) {
			$defCityId = 0;
			$defData   = self::_buildBattleCityData($defCityId, $defCityInfo);
			foreach ($defHeroList as $defHeroId) {
				$heroInfo = M_Hero::buildHeroBattleInfo($defHeroId, $defData);
				if ($heroInfo['army_num'] > 0) {
					$defHero[$defHeroId]               = $heroInfo;
					$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
					$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
					$newDefHeroList[]                  = $defHeroId;
				}
			}
		}

		$result['defHero'] = $defHero;
		$gender            = !empty($defCityInfo['gender']) ? $defCityInfo['gender'] : 0;
		$result['defData'] = array($defCityId, $defNpcId, $defPosNo, $defAi, $defLv, $defCityInfo['nickname'], $defCityInfo['face_id'], $gender);

		$warMapNo = $arrOutpost[1];
		//获取地图数据
		if ($atkAi == M_War::FIGHT_TYPE_QUICK) { //快速战斗地图 减少寻路和障碍 加快计算
			$quickmapno = M_War::quickmapno();
			if ($quickmapno) {
				$warMapNo = $quickmapno;
			}
		}
		$result['mapData'] = self::_buildWarMapData($atkHeroList, $defHeroList, $warMapNo);
		//获取天气
		$result['weather']    = T_App::WEATHER_CLEAR;
		$result['battleType'] = M_War::BATTLE_TYPE_BOUT;
		//获取基础兵种武器数据
		$result['army_data']   = array_values($armyIds);
		$result['weapon_data'] = array_values($weaponIds);
		$result['atkMarchId']  = 0;
		$result['defMarchId']  = 0;

		return $result;

	}

	/**
	 * 爬楼战斗数据
	 * @author huwei
	 * @param int $atkCityId
	 * @param string $atkPosNo
	 * @param string $defPosNo
	 * @param array $arrOutpost npc部队ID,地图ID
	 * @param array $defPos
	 * @param array $atkHeroList
	 * @param int $atkAi
	 * @return array()
	 */
	static public function buildFloorWarBattleData($atkCityId, $atkPosNo, $defPosNo, $npcData, $atkAi, $atkHeroList) // $defPosNo,
	{
		$result = array();

		$defCityId   = $atkNpcId = 0;
		$defAi       = 1;
		$atkCityInfo = M_City::getInfo($atkCityId);
		$atkLv       = $atkCityInfo['level'];

		list($defNpcId, $warMapNo) = $npcData;

		$defCityInfo = M_NPC::getInfo($defNpcId);

		$defLv       = $defCityInfo['level'];
		$defHeroList = json_decode($defCityInfo['army_data'], true);

		//获取进攻方数据(城市ID,所在州,坐标,是否自动战斗,英雄ID列表)
		$atkHero        = $armyIds = $weaponIds = array();
		$newAtkHeroList = $newDefHeroList = array();

		$atkData = self::_buildBattleCityData($atkCityId, $atkCityInfo);
		foreach ($atkHeroList as $atkHeroId) {
			$heroInfo = M_Hero::buildHeroBattleInfo($atkHeroId, $atkData);
			if ($heroInfo['army_num'] > 0) {
				$atkHero[$atkHeroId]               = $heroInfo;
				$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
				$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
				$newAtkHeroList[]                  = $atkHeroId;
			}
		}
		$result['atkHero'] = $atkHero;
		$result['atkData'] = array($atkCityId, $atkNpcId, $atkPosNo, $atkAi, $atkLv, $atkCityInfo['nickname'], $atkCityInfo['face_id'], $atkCityInfo['gender']);

		//获取防御数据
		$defHero = array();

		if (!empty($defHeroList) && is_array($defHeroList)) {
			$defCityId = 0;
			$defData   = self::_buildBattleCityData($defCityId, $defCityInfo);
			foreach ($defHeroList as $defHeroId) {
				$heroInfo = M_Hero::buildHeroBattleInfo($defHeroId, $defData);
				if ($heroInfo['army_num'] > 0) {
					$defHero[$defHeroId]               = $heroInfo;
					$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
					$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
					$newDefHeroList[]                  = $defHeroId;
				}
			}
		}

		$result['defHero'] = $defHero;
		$gender            = !empty($defCityInfo['gender']) ? $defCityInfo['gender'] : 0;
		$result['defData'] = array($defCityId, $defNpcId, $defPosNo, $defAi, $defLv, $defCityInfo['nickname'], $defCityInfo['face_id'], $gender);

		//获取地图数据
		if ($atkAi == M_War::FIGHT_TYPE_QUICK) { //快速战斗地图 减少寻路和障碍 加快计算
			$quickmapno = M_War::quickmapno();
			if ($quickmapno) {
				$warMapNo = $quickmapno;
			}
		}
		$result['mapData'] = self::_buildWarMapData($atkHeroList, $defHeroList, $warMapNo);
		//获取天气
		$result['weather']    = T_App::WEATHER_CLEAR;
		$result['battleType'] = M_War::BATTLE_TYPE_FLOOR;
		//获取基础兵种武器数据
		$result['army_data']   = array_values($armyIds);
		$result['weapon_data'] = array_values($weaponIds);
		$result['atkMarchId']  = 0;
		$result['defMarchId']  = 0;

		return $result;
	}

	/**
	 * 据点战斗数据
	 * @author huwei
	 * @param int $atkCityId
	 * @param array $atkPos
	 * @param array $defPos
	 * @param array $atkHeroList
	 * @param int $atkAi
	 */
	static public function buildCampWarBattleData($marchInfo) {
		$atkCityId   = $marchInfo['atk_city_id'];
		$atkPosNo    = $marchInfo['atk_pos'];
		$defPosNo    = $marchInfo['def_pos'];
		$atkAi       = $marchInfo['auto_fight'];
		$atkHeroList = json_decode($marchInfo['hero_list'], true);
		$result      = array();
		list($type, $campId, $defLineNo) = M_MapWild::calcWildMapPosXYByNo($defPosNo);

		$campInfo = M_Campaign::getInfo($campId);
		//不属于自己同盟的据点基地
		$campBaseList = M_Base::campaignAll();
		$campBaseInfo = $campBaseList[$campId];

		$defLineNo      = strval($defLineNo);
		$defLineNoField = 'no_' . $defLineNo;

		list($defNpcId, $warMapNo) = explode('|', $campBaseInfo[$defLineNoField]);

		$defMarchId  = $defCityId = $atkNpcId = $defLv = 0;
		$defAi       = 1;
		$atkCityInfo = M_City::getInfo($atkCityId);
		$atkLv       = $atkCityInfo['level'];

		//@todo 获取据点基地的驻军
		//如果有联盟占领 查看是驻军的部队是否在战斗
		//如果没有直接和NPC战斗
		$isHold  = false;
		$marchId = 0;
		list($defUnionId, $marchIds) = json_decode($campInfo[$defLineNoField], true);

		//Logger::dev("战斗前=1=unionid#{$defUnionId}marchIds:".json_encode($marchIds));
		if (!empty($defUnionId)) { //已被占领
			$isHold = true;
			//防守行军ID
			$marchId = $marchIds[0];
		}

		$defCityInfo = M_NPC::getInfo($defNpcId);
		//默认NPC数据
		$defHeroList = json_decode($defCityInfo['army_data'], true);

		if ($isHold) { //如果被占领
			//Logger::dev("如果被占领");
			//无防守
			$defNpcId    = 0;
			$defHeroList = array();
			if (!empty($marchId)) { //有防守
				//Logger::dev("有防守:{$marchId}");
				$tmpMarchInfo = M_March_Info::get($marchId);
				if ($tmpMarchInfo && $tmpMarchInfo['flag'] == M_March::MARCH_FLAG_HOLD) { //防守方为驻守状态
					//Logger::dev("防守方为驻守状态:".json_encode($tmpMarchInfo));
					$defCityInfo = M_City::getInfo($tmpMarchInfo['atk_city_id']);
					$defHeroList = json_decode($tmpMarchInfo['hero_list'], true);
					$defCityId   = $tmpMarchInfo['atk_city_id'];
					$defMarchId  = $marchId;
				} else {
					Logger::error(array(__METHOD__, $defUnionId, $marchIds, $tmpMarchInfo));
				}
			}
		}

		if (empty($defCityInfo['nickname'])) {
			Logger::error(array(__METHOD__, 'Err DefCityInfo', $defCityInfo));
		}

		//获取进攻方数据(城市ID,所在州,坐标,是否自动战斗,英雄ID列表)
		$atkHero        = $armyIds = $weaponIds = array();
		$newAtkHeroList = $newDefHeroList = array();
		$atkData        = self::_buildBattleCityData($atkCityId, $atkCityInfo);
		foreach ($atkHeroList as $atkHeroId) {
			$heroInfo = M_Hero::buildHeroBattleInfo($atkHeroId, $atkData);
			if ($heroInfo['army_num'] > 0) {
				$atkHero[$atkHeroId]               = $heroInfo;
				$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
				$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
				$newAtkHeroList[]                  = $atkHeroId;
			}
		}

		$result['atkHero'] = $atkHero;
		$result['atkData'] = array($atkCityId, $atkNpcId, $atkPosNo, $atkAi, $atkLv, $atkCityInfo['nickname'], $atkCityInfo['face_id'], $atkCityInfo['gender']);

		//获取防御数据
		$defHero = array();
		if (is_array($defHeroList)) {
			$defData = self::_buildBattleCityData($defCityId, $defCityInfo);
			foreach ($defHeroList as $defHeroId) {
				$heroInfo = M_Hero::buildHeroBattleInfo($defHeroId, $defData);
				if ($heroInfo['army_num'] > 0) {
					$defHero[$defHeroId]               = $heroInfo;
					$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
					$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
					$newDefHeroList[]                  = $defHeroId;
				}
			}
		}

		$result['defHero'] = $defHero;
		$gender            = !empty($defCityInfo['gender']) ? $defCityInfo['gender'] : 0;
		$result['defData'] = array($defCityId, $defNpcId, $defPosNo, $defAi, $defLv, $defCityInfo['nickname'], $defCityInfo['face_id'], $gender);

		//获取地图数据
		$result['mapData'] = self::_buildWarMapData($newAtkHeroList, $newDefHeroList, $warMapNo);
		//获取天气
		$result['weather']    = T_App::WEATHER_CLEAR;
		$result['battleType'] = M_War::BATTLE_TYPE_CAMP;
		//获取基础兵种武器数据
		$result['army_data']   = array_values($armyIds);
		$result['weapon_data'] = array_values($weaponIds);
		$result['atkMarchId']  = $marchInfo['id'];
		$result['defMarchId']  = $defMarchId;


		Logger::dev("攻击行军数据构建结束:" . json_encode($result['atkData']));
		Logger::dev("防御行军数据构建结束:" . json_encode($result['defData']));
		return $result;
	}

	/**
	 * 据点战斗数据
	 * @author huwei
	 * @param int $atkCityId
	 * @param array $atkPos
	 * @param array $defPos
	 * @param array $atkHeroList
	 * @param int $atkAi
	 */
	static public function buildMultiFBWarBattleData($marchInfo) {
		$atkCityId   = $marchInfo['atk_city_id'];
		$atkPosNo    = $marchInfo['atk_pos'];
		$defPosNo    = $marchInfo['def_pos'];
		$atkAi       = $marchInfo['auto_fight'];
		$atkHeroList = json_decode($marchInfo['hero_list'], true);
		$result      = array();
		list($type, $multiFBId, $defLineNo) = M_MapWild::calcWildMapPosXYByNo($defPosNo);

		$baseMultiFB = M_MultiFB::getBaseList();
		if (!isset($baseMultiFB[$multiFBId]['def_line'][$defLineNo])) {
			return false;
		}

		list($defNpcId, $warMapNo, $point) = $baseMultiFB[$multiFBId]['def_line'][$defLineNo];

		$atkMultiFBInfo = M_MultiFB::getCityInfo($atkCityId);
		if (!$atkMultiFBInfo['team_id']) {
			return false;
		}

		$teamInfo = M_MultiFB::getInfo($atkMultiFBInfo['team_id']);
		if (!$teamInfo['id']) {
			return false;
		}

		$defMarchId  = $defCityId = $atkNpcId = $defLv = 0;
		$defAi       = 1;
		$atkCityInfo = M_City::getInfo($atkCityId);
		$atkLv       = $atkCityInfo['level'];

		$holdDefLine = json_decode($teamInfo['hold_def_line'], true);
		if (isset($holdDefLine[$defLineNo])) { //已经占领
			$holdDefLine[$defLineNo][$atkCityId] = $marchInfo['id'];
			$upInfo                              = array(
				'id'            => $atkMultiFBInfo['team_id'],
				'hold_def_line' => json_encode($holdDefLine),
			);
			$ret                                 = M_MultiFB::setInfo($upInfo);

			$ret && M_March::setMarchHold($marchInfo);
			return $ret;
		}

		//需要攻击NPC
		$defCityInfo = M_NPC::getInfo($defNpcId);
		//默认NPC数据
		$defHeroList = json_decode($defCityInfo['army_data'], true);

		//获取进攻方数据(城市ID,所在州,坐标,是否自动战斗,英雄ID列表)
		$atkHero        = $armyIds = $weaponIds = array();
		$newAtkHeroList = $newDefHeroList = array();

		$atkData = self::_buildBattleCityData($atkCityId, $atkCityInfo);
		foreach ($atkHeroList as $atkHeroId) {
			$heroInfo = M_Hero::buildHeroBattleInfo($atkHeroId, $atkData);
			if ($heroInfo['army_num'] > 0) {
				$atkHero[$atkHeroId]               = $heroInfo;
				$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
				$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
				$newAtkHeroList[]                  = $atkHeroId;
			}
		}

		$result['atkHero'] = $atkHero;
		$result['atkData'] = array($atkCityId, $atkNpcId, $atkPosNo, $atkAi, $atkLv, $atkCityInfo['nickname'], $atkCityInfo['face_id'], $atkCityInfo['gender']);

		//获取防御数据
		$defHero = array();
		if (is_array($defHeroList)) {
			$defData = self::_buildBattleCityData($defCityId, $defCityInfo);
			foreach ($defHeroList as $defHeroId) {
				$heroInfo = M_Hero::buildHeroBattleInfo($defHeroId, $defData);
				if ($heroInfo['army_num'] > 0) {
					$defHero[$defHeroId]               = $heroInfo;
					$armyIds[$heroInfo['army_id']]     = $heroInfo['army_info'];
					$weaponIds[$heroInfo['weapon_id']] = $heroInfo['weapon_info'];
					$newDefHeroList[]                  = $defHeroId;
				}
			}
		}

		$result['defHero'] = $defHero;
		$gender            = !empty($defCityInfo['gender']) ? $defCityInfo['gender'] : 0;
		$result['defData'] = array($defCityId, $defNpcId, $defPosNo, $defAi, $defLv, $defCityInfo['nickname'], $defCityInfo['face_id'], $gender);

		//获取地图数据
		$result['mapData'] = self::_buildWarMapData($newAtkHeroList, $newDefHeroList, $warMapNo);
		//获取天气
		$result['weather']    = T_App::WEATHER_CLEAR;
		$result['battleType'] = M_War::BATTLE_TYPE_CAMP;
		//获取基础兵种武器数据
		$result['army_data']   = array_values($armyIds);
		$result['weapon_data'] = array_values($weaponIds);
		$result['atkMarchId']  = $marchInfo['id'];
		$result['defMarchId']  = $defMarchId;

		return $result;
	}

	/**
	 * 插入战斗记录数据
	 * @param array $result
	 *        array($atkCityId,$atkPos,$atkAi,$atkHeroList)
	 *        array($defCityId,$defPos,$defAi,$defHeroList)
	 * @param int marchId 行军ID, 副本为0
	 */
	static public function insertWarBattle($result, $isAutoFight = 0) {
		$ret = false;
		$now = time();

		if (!empty($result['atkData']) &&
			!empty($result['defData']) &&
			!empty($result['mapData']) &&
			!empty($result['battleType'])
		) {
			$initData = array(
				M_Battle_Calc::REPORT_TYPE_ATK,
				$result['atkData'][0],
				$result['defData'][0],
				array($result['atkData'][5], $result['atkData'][6], $result['atkData'][2], $result['atkData'][7]),
				array($result['defData'][5], $result['defData'][6], $result['defData'][2], $result['defData'][7]),
				$result['battleType']
			);

			$bid = M_WarReport::initWarReport($initData);
			if (!empty($bid)) {
				$setArr = array(
					'id'              => $bid,
					'cur_op_bout_num' => 1,
					'type'            => $result['battleType'],
					'status'          => T_Battle::STATUS_WAIT,
					'cur_op'          => T_Battle::CUR_OP_ATK,
					'wait_time'       => 0,
					'atk_march_id'    => isset($result['atkMarchId']) ? $result['atkMarchId'] : 0,
					'def_march_id'    => isset($result['defMarchId']) ? $result['defMarchId'] : 0,

					'atk_city_id'     => $result['atkData'][0],
					'atk_npc_id'      => $result['atkData'][1],
					'atk_pos'         => $result['atkData'][2],
					'atk_is_ai'       => $result['atkData'][3],
					'atk_lv'          => $result['atkData'][4],
					'atk_nickname'    => $result['atkData'][5],
					'atk_face_id'     => $result['atkData'][6],
					'atk_gender'      => $result['atkData'][7],

					'atk_hero_data'   => $result['atkHero'],

					'def_city_id'     => $result['defData'][0],
					'def_npc_id'      => $result['defData'][1],
					'def_pos'         => $result['defData'][2],
					'def_is_ai'       => $result['defData'][3],
					'def_lv'          => $result['defData'][4],
					'def_nickname'    => $result['defData'][5],
					'def_face_id'     => $result['defData'][6],
					'def_gender'      => $result['defData'][7],

					'def_hero_data'   => $result['defHero'],

					'terrian'         => 1,
					'weather'         => $result['weather'],
					'map_data'        => $result['mapData'],

					'army_data'       => $result['army_data'],
					'weapon_data'     => $result['weapon_data'],
					'create_at'       => $now,
				);


				//初始化战斗数据 to 缓存中
				if ($isAutoFight == 2) {

					//快速战斗
					$WBQ = new M_Battle_Quick($setArr);
					$ret = $WBQ->run();
				} else {
					$BD  = M_Battle_Handler::initData($setArr);
					$ret = isset($BD['Id']) ? $BD['Id'] : false;
				}
			}
		}
		return $ret;
	}

	/**
	 * 占领野地部队返回
	 * @author huwei
	 * @param array $marchInfo
	 */
	static public function setHoldWildBack($marchInfo) {
		//更新属地行军数据
		$ret = M_MapWild::setWildMapInfo($marchInfo['def_pos'], array('march_id' => 0));
		//获取属地位置

		$objPlayer = new O_Player($marchInfo['atk_city_id']);
		list($no,) = $objPlayer->ColonyNpc()->getNoByPos($marchInfo['def_pos']);
		$objPlayer->ColonyNpc()->buildSyncData($no);

		M_MapWild::syncWildMapBlockCache($marchInfo['def_pos']);
		return $ret;
	}

	/**
	 * 占领据点部队返回
	 * @author huwei
	 * @param array $marchInfo
	 */
	static public function setHoldCampBack($marchInfo) {
		list($type, $campId, $defLineNo) = M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']);
		$byDef    = false;
		$campInfo = M_Campaign::getInfo($campId);

		//据点编号
		$defLineNo      = strval($defLineNo);
		$defLineNoField = 'no_' . $defLineNo;

		$defLinePos = M_MapWild::calcWildMapPosNoByXY(T_App::MAP_CAMPAIGN, $campId, $defLineNo);
		$obj_ml     = new M_March_List($defLinePos);
		$bDel       = $obj_ml->del($marchInfo['id']);
		if ($bDel) {
			$ret = M_Campaign::delCampainHoldId($campInfo, $defLineNoField, $marchInfo['id']);
		}
		return $ret;
	}

	/**
	 * 占领城市部队返回
	 * @author duhuihui
	 * @param array $marchInfo
	 */
	static public function setHoldCityBack($marchInfo) {
		$now = time();
		list($type, $campId, $defLineNo) = M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']);
		Logger::dev('----------------------' . $marchInfo['def_pos']);
		$ret1       = M_MapWild::setWildMapInfo($marchInfo['def_pos'], array('march_id' => 0));
		$mapRow     = M_MapWild::getWildMapInfo($marchInfo['def_pos']); //目的坐标
		$cityColony = M_ColonyCity::getInfo($mapRow['city_id']); //占领信息
		$updateFlag = true;
		if (!empty($cityColony['atk_city_id']) && !empty($cityColony['atk_march_id'])) {
			$updInfo    = array('atk_march_id' => 0);
			$updateFlag = M_ColonyCity::setInfo($mapRow['city_id'], $updInfo);
		}
		$wildInfo = M_ColonyCity::getNoByPosNo($marchInfo['atk_city_id'], $marchInfo['def_pos']);
		if (!empty($wildInfo)) {
			$marchId  = $level = $zone = $posx = $poxy = 0;
			$nickname = '';
			$no       = $wildInfo['no'];
			$noInfo   = $wildInfo['val'];
			if (!empty($noInfo[1])) {
				$mapInfo          = M_MapWild::getWildMapInfo($noInfo[1]);
				$defCityColony    = M_ColonyCity::getInfo($mapInfo['city_id']);
				$colonyHoldTime   = isset($defCityColony['hold_time']) ? $defCityColony['hold_time'] : 0;
				$holdTimeInterval = M_Config::getVal('hold_city_time_interval');
				$holdTime         = $now + (T_App::ONE_HOUR * $holdTimeInterval - $colonyHoldTime);
				$marchId          = $mapInfo['march_id'];
				list($zone, $posx, $poxy) = M_MapWild::calcWildMapPosXYByNo($noInfo[1]);
				$cityInfo = M_City::getInfo($mapInfo['city_id']);
				$nickname = $cityInfo['nickname'];
				$level    = $cityInfo['level'];
				$faceId   = $cityInfo['face_id'];
			}

			$msRow[$no] = array(
				'IsOpen'        => $noInfo[0],
				'FaceId'        => $faceId,
				'Name'          => $nickname,
				'PosX'          => $posx,
				'PosY'          => $poxy,
				'PosArea'       => $zone,
				'Level'         => intval($level),
				'MarchId'       => $marchId > 0 ? intval($marchId) : 0, //行军中
				'MarchType'     => 0, //行军中
				'TaxExprieTime' => $noInfo[2],
				'ExprieTime'    => !empty($holdTime) ? $holdTime : 0,
				'IntervalTime'  => $noInfo[2] - $now,
			);

			M_Sync::addQueue($marchInfo['atk_city_id'], M_Sync::KEY_CITY_COLONY, $msRow); //同步属地数据
		}

		M_MapWild::syncWildMapBlockCache($marchInfo['def_pos']);
		$ret = $ret1 && $updateFlag;
		return $ret;
	}

	/**
	 * 战斗结束，更新排队中的数据
	 * @author huwei on 20120107
	 * @param int $cityId 城市ID
	 * @return bool
	 */
	static public function setNextBattle($posNo) {
		$ret      = false;
		$mw       = new M_March_Wait($posNo);
		$battleId = $mw->getBattleId();

		//Logger::debug(array(__METHOD__, "if next battle ? posno#{$posNo},battle ID#{$battleId}"));

		if (!empty($posNo) && empty($battleId)) {
			//获取当前坐标 排队中的行军ID
			$marchArr = $mw->get();

			//检测是否有 排队的 行军 的数据
			if (!empty($marchArr[0])) {
				//删除等待队列中的 行军ID
				$mw->del($marchArr[0]);

				$tmpData = M_March_Info::get($marchArr[0]);
				if (isset($tmpData['action_type'])) {
					//Logger::debug(array(__METHOD__, "next battle march tmpData:".json_encode($tmpData)));

					if ($tmpData['action_type'] == M_March::MARCH_ACTION_ATT) {
						$ret = M_March_Action::toCityBattle($tmpData);
					} else if ($tmpData['action_type'] == M_March::MARCH_ACTION_HOLD) {
						$ret = M_March_Action::toHoldBattle($tmpData);
					} else if ($tmpData['action_type'] == M_March::MARCH_ACTION_CAMP) {
						$ret = M_March_Action::toCampBattle($tmpData);
					} else if ($tmpData['action_type'] == M_March::MARCH_ACTION_CITY) {
						$ret = M_March_Action::toOccupiedCityBattle($tmpData);
					} else if ($tmpData['action_type'] == M_March::MARCH_ACTION_RESCUE_CITY) {
						$ret = M_March_Action::toRescueCityBattle($tmpData);
					}

				}

				if (!$ret) {
					//Logger::debug(array(__METHOD__, "setNextBattle DefPos#{$posNo}"));
					M_War::setNextBattle($posNo);
				}
			}
		}

		return $ret;
	}

	/**
	 * 解析战斗报告格式
	 * @author huwei on 20111022
	 * @param array $info
	 * @return array
	 */
	static public function parseReportInfo($info, $cityId) {
		$data = array();
		if (!empty($info['id']) && !empty($info['atk_info']) && !empty($info['def_info'])) {
			$fbname  = '';
			$defInfo = json_decode($info['def_info'], true);
			if ($info['battle_type'] == M_War::BATTLE_TYPE_FB) {
				list($defPosZ, $defPosX, $defPosY) = M_Formula::calcParseFBNo($defInfo[2]);
				$fbinfo = M_SoloFB::getDetail($defPosZ, $defPosX);
				$fbname = $fbinfo['name'];
			} else {
				list($defPosZ, $defPosX, $defPosY) = M_MapWild::calcWildMapPosXYByNo($defInfo[2]);
			}

			$atkInfo = json_decode($info['atk_info'], true);
			list($atkPosZ, $atkPosX, $atkPosY) = M_MapWild::calcWildMapPosXYByNo($atkInfo[2]);

			$viewContent = true;
			if ($info['type'] == M_March::MARCH_ACTION_SCOUT &&
				$info['def_city_id'] == $cityId
			) {
				//[侦察类型] 被侦察方   无法查看内容
				$viewContent = false;
			}

			$content = json_decode($info['content'], true);
			if ($info['atk_city_id'] == $cityId) {
				$op = 'Def';
			} else if ($info['def_city_id'] == $cityId) {
				$op = 'Atk';
			}

			if (!empty($info['def_city_id']) && !empty($op)) {
				//过滤掉对方玩家信息
				if (isset($content[$op])) {
					foreach ($content[$op] as $hId => $val) {
						unset($content[$op][$hId]['Level']);
						unset($content[$op][$hId]['WeaponId']);
						unset($content[$op][$hId]['ArmyId']);
						unset($content[$op][$hId]['Exp']);
						unset($content[$op][$hId]['ArmyNum']);
					}
				}

				$armyExpField           = $op . 'ArmyExp';
				$content[$armyExpField] = array();
			}

			$data = array(
				'ID'               => $info['id'],
				'BattleId'         => $info['id'],
				'Type'             => $info['type'],
				'BattleType'       => isset($info['battle_type']) ? $info['battle_type'] : 0,
				'AttCityId'        => $info['atk_city_id'],
				'DefCityId'        => $info['def_city_id'],

				'AtkIsPlayer'      => !empty($info['atk_city_id']) ? 1 : 0,
				'DefIsPlayer'      => !empty($info['def_city_id']) ? 1 : 0,

				'AttInfo'          => array($atkInfo[0], $atkInfo[1], $atkPosZ, $atkPosX, $atkPosY, $atkInfo[3]),
				'DefInfo'          => array($defInfo[0], $defInfo[1], $defPosZ, $defPosX, $defPosY, $defInfo[3]),
				'FBName'           => $fbname,
				'AttTime'          => $info['atk_time'],
				'Content'          => $viewContent ? $content : '',
				'Reward'           => M_Award::toText(json_decode($info['reward'], true)),
				'IsSucc'           => $info['is_succ'],
				'FlagSee'          => $info['flag_see'],
				'ReplayAddressKey' => $info['replay_address'],
			);
		}
		return $data;
	}

	/**
	 * 获取战斗胜利方掠夺到的资源数组
	 * @author chenhui on 20110930
	 * @param int $atkCityId 攻击方城市ID
	 * @param int $defCityId 防守方城市ID
	 * @param int $atkCarry 攻击方部队运载量
	 * @return array array('res'=>array('gold'=>num, 'food'=>num, 'oil'=>num))
	 */
	static public function getAtkPlunderRes($atkCityId, $defCityId, $atkCarry) {
		$pGold       = $pFood = $pOil = 0;
		$res         = array();
		$res['gold'] = $pGold;
		$res['food'] = $pFood;
		$res['oil']  = $pOil;
		$atkCityId   = intval($atkCityId);
		$defCityId   = intval($defCityId);
		$atkCarry    = intval($atkCarry);

		if ($atkCityId > 0 && $defCityId > 0 && $atkCarry > 0) {
			$rc         = new B_Cache_RC(T_Key::CITY_ATK_CITY, $atkCityId . '_' . $defCityId);
			$strTimes   = $rc->get();
			$todayStamp = mktime(0, 0, 0); //今天时间戳
			$arr        = !empty($strTimes) ? json_decode($strTimes, true) : array($todayStamp, 0);
			if ($todayStamp != $arr[0]) {
				$arr = array($todayStamp, 0);
			}

			$defObjCity  = new O_Player($defCityId);
			$defRes      = $defObjCity->getObjRes()->get();
			$oldTimes    = $arr[1];
			$x           = M_Formula::calcPlunderTimesRate($oldTimes + 1);
			$preAllRes   = ($defRes['gold'] + $defRes['food'] + $defRes['oil']) * 0.3 * $x;
			$allRes      = min($atkCarry, $preAllRes);
			$pGold       = round($allRes * $defRes['gold'] / ($defRes['gold'] + $defRes['food'] + $defRes['oil']));
			$pFood       = round($allRes * $defRes['food'] / ($defRes['gold'] + $defRes['food'] + $defRes['oil']));
			$pOil        = round($allRes - $pGold - $pFood);
			$res['gold'] = $pGold;
			$res['food'] = $pFood;
			$res['oil']  = $pOil;

			$arr[1] = $oldTimes + 1;
			$rc->set(json_encode($arr), T_App::ONE_DAY);
		}

		return $res;
	}

	/**
	 * 根据侦察情报值获取某城市情报数据
	 * @author chenhui on 20111025
	 * @param int $infoVal 侦察情报值
	 * @param int $cityId 被侦察城市ID
	 * @return array 情报数据
	 */
	static public function getScoutDataByInfoval($infoVal, $cityId) {
		$objPlayer = new O_Player($cityId);

		$infoVal = intval($infoVal);
		$cityId  = intval($cityId);
		$ret     = array();
		if ($infoVal > 0) {
			$tmpRes     = $objPlayer->Res()->get();
			$arrRes     = array('gold' => floor($tmpRes['gold']), 'food' => floor($tmpRes['food']), 'oil' => floor($tmpRes['oil']));
			$ret['res'] = $arrRes; //资源
		}
		if ($infoVal > 4) {
			$tmpBuild = $objPlayer->Build()->get();
			$arrBuild = array();
			foreach ($tmpBuild as $bid => $binfo) {
				if (!in_array($bid, array(M_Build::ID_HOUSE, M_Build::ID_STORAGE))) //屏蔽民房和仓库
				{
					foreach ($binfo as $bpos => $blev) {
						$arrBuild[] = array($bid, $blev); //建筑ID,建筑等级
					}
				}
			}
			$ret['build'] = $arrBuild; //建筑
		}
		if ($infoVal > 8) {
			$arrArmy = array();

			$arrCityArmy = $objPlayer->Army()->toData();
			foreach ($arrCityArmy as $armyId => $cityArmy) {
				$armyBaseInfo = M_Army::baseInfo($armyId);
				$arrArmy[]    = array(0, $armyBaseInfo['name'], 0, $cityArmy['number'], $armyId, $cityArmy['level']);
			}

			$arrHeroId = M_Hero::getCityHeroList($cityId);
			foreach ($arrHeroId as $k => $heroId) {
				$heroInfo = M_Hero::getHeroInfo($heroId);
				if (!empty($heroInfo['weapon_id']) && $heroInfo['army_num'] > 0) {
					$arrArmy[] = array($heroInfo['id'], $heroInfo['nickname'], $heroInfo['weapon_id'], $heroInfo['army_num'], $heroInfo['army_id'], $heroInfo['level']);
				}
			}
			$ret['army'] = $arrArmy; //部队
		}
		if ($infoVal > 14) {
			$objTech = $objPlayer->Tech();
			$tmpTech = $objTech->get();
			$arrTech = array();
			foreach ($tmpTech as $tid => $lev) {
				if ($lev > 0) {
					$arrTech[] = array($tid, $lev);
				}
			}
			$ret['tech'] = $arrTech; //科技
		}
		if ($infoVal > 19) {
			$arrHeroId = M_Hero::getCityHeroList($cityId);
			$arrHero   = array();
			foreach ($arrHeroId as $k => $heroId) {
				$heroInfo  = M_Hero::getHeroInfo($heroId);
				$arrHero[] = array($heroInfo['id'],
					$heroInfo['nickname'],
					$heroInfo['quality'],
					$heroInfo['level'],
					$heroInfo['exp'],
					$heroInfo['is_legend'],
					$heroInfo['attr_lead'],
					$heroInfo['attr_command'],
					$heroInfo['attr_military'],
					$heroInfo['attr_energy'],
					$heroInfo['attr_mood'],
					$heroInfo['weapon_id'],
					$heroInfo['army_num']
				);
			}
			$ret['hero'] = $arrHero; //军官
		}
		return $ret;
	}


	/**
	 * 被攻击城市 损失部队(预备役+已配给军官)
	 * @author duhuihui on 20121207
	 * @author huwei modify by 20121210
	 * @param int $cityId 城市ID
	 * @return array 军官和预备役 ID => 死兵数量
	 */

	static public function failToDecrArmyNum($atkCityId, $defCityId) {
		$atkCityObj = new O_Player($atkCityId);
		$defCityObj = new O_Player($defCityId);

		$atkPunishment = M_Config::getVal('atk_punishment');
		$tmpStr        = 'city_level_' . $defCityObj->level;
		$baseTimes     = isset($atkPunishment[$tmpStr]['num']) ? $atkPunishment[$tmpStr]['num'] : 0;
		$rc            = new B_Cache_RC(T_Key::CITY_ATK_TIMES, date('Ymd') . $defCityId);
		$tmpTimes      = $rc->get();
		$defTimes      = !empty($tmpTimes) ? $tmpTimes : 0;
		//Logger::debug(array(__METHOD__, $baseTimes, $defCityId, $defTimes));

		if ($defTimes < $baseTimes) {
			$rc->incrby(1);

			list($z, $x, $y) = M_MapWild::calcWildMapPosXYByNo($atkCityObj->pos_no);
			list($z1, $x1, $y1) = M_MapWild::calcWildMapPosXYByNo($defCityObj->pos_no);

			$defArmyObj = $defCityObj->getObjArmy();
			$armyList   = $defArmyObj->get();

			$decLoss   = isset($atkPunishment[$tmpStr]['loss']) ? $atkPunishment[$tmpStr]['loss'] : 0;
			$decrRate  = $decLoss / 100; //损失1%
			$arrArmy   = array(); //兵种ID => 死兵数量
			$upArmyArr = array();

			$newPeople = $defCityObj->cur_people;

			foreach ($armyList as $armyId => $armyInfo) {
				//数量,等级,经验
				list($num, $lv, $exp) = $armyInfo;
				$armybaseinfo     = M_Army::baseInfo($armyId); //兵种基础信息
				$decrNum          = round($num * $decrRate);
				$arrArmy[$armyId] = $decrNum;

				//占用人口减少
				$defCityObj->cur_people -= $decrNum * $armybaseinfo['cost_people'];
				//兵种数减少
				$defArmyObj->addNum($armyId, -$decrNum);

				//更新城市兵种数量
			}

			//Logger::debug(array(__METHOD__, $decLoss, $upArmyArr, $arrArmy));

			if ($defCityObj->cur_people >= 0) {
				$ret = $defCityObj->save();
				if ($ret) {
					$msRow = $defArmyObj->get();
					M_Sync::addQueue($defCityObj->id, M_Sync::KEY_ARMY, $msRow);

					$upData = array('cur_people' => $defCityObj->cur_people);
					M_Sync::addQueue($defCityObj->id, M_Sync::KEY_CITY_INFO, $upData);
				}
				//给被攻击方发送邮件
				$a       = !empty($arrArmy[1]) ? array(T_Lang::C_ARMY_1, $arrArmy[1]) : '';
				$b       = !empty($arrArmy[2]) ? array(T_Lang::C_ARMY_2, $arrArmy[2]) : '';
				$c       = !empty($arrArmy[3]) ? array(T_Lang::C_ARMY_3, $arrArmy[3]) : '';
				$d       = !empty($arrArmy[4]) ? array(T_Lang::C_ARMY_4, $arrArmy[4]) : '';
				$content = array(T_Lang::C_WILD_CITY_ATK_FALL, $atkCityObj->nickname, array(T_Lang::$Map[$z]), $x . ',' . $y, $a, $b, $c, $d);
				M_Message::sendSysMessage($defCityId, json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content));
				//给攻击方发送邮件
				$content = array(T_Lang::C_WILD_CITY_ATK_SUCC, $defCityObj->nickname, array(T_Lang::$Map[$z1]), $x1 . ',' . $y1, $a, $b, $c, $d);
				M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content));
			}
		}
		return true;
	}

	/**
	 * 被攻击城市 损失部队开启条件(预备役+已配给军官)
	 * @author duhuihui on 20121207
	 * @param int $cityId 城市ID
	 * @return array 军官和预备役 ID => 死兵数量
	 */

	static public function openDecrArmyNum($atkRenown, $defRenown) {
		$diff        = abs($atkRenown - $defRenown);
		$isOpen      = 0;
		$atkLossOpen = M_Config::getVal('atk_loss_open');
		foreach (self::$warLoss as $val) {
			if (!empty($atkLossOpen[1]['before']) && ($atkRenown < $atkLossOpen[1]['before'] || $defRenown < $atkLossOpen[1]['before'])) {
				$isOpen = 0;
				break;
			} else if (!empty($atkLossOpen[$val]['before']) && !empty($atkLossOpen[$val]['after']) && !empty($atkLossOpen[$val]['diff']) && ($atkLossOpen[$val]['before'] <= $atkRenown && $atkRenown <= $atkLossOpen[$val]['after']) && ($atkLossOpen[$val]['before'] <= $defRenown && $defRenown <= $atkLossOpen[$val]['after']) && $diff <= $atkLossOpen[$val]['diff']) {
				$isOpen = 1;
				break;
			} else if (!empty($atkLossOpen[$val]['before']) && empty($atkLossOpen[$val]['after']) && empty($atkLossOpen[$val]['diff']) && $atkLossOpen[$val]['before'] <= $atkRenown && $atkLossOpen[$val]['before'] <= $defRenown) {
				$isOpen = 1;
				break;
			}
		}
		return $isOpen;
	}

	/**
	 * 构建城市战斗相关数据
	 * @author huwei on 2013/02/27
	 * @param int $cityId
	 * @param array $cityInfo
	 * @return array
	 */
	static private function _buildBattleCityData($cityId, $cityInfo) {
		$unionInfo = array();
		$zone      = $milRank = 0;

		$objPlayer   = new O_Player($cityId);
		$cityVipFunc = array();
		if ($cityId > 0) {
			$unionInfo = M_Union::getInfo($cityInfo['union_id']);
			$milRank   = $cityInfo['mil_rank'];
			list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($cityInfo['pos_no']);

			$cityVipFunc = $objPlayer->Vip()->get();
		}

		$objTech = $objPlayer->Tech();

		$retData = array(
			'cityId'         => $cityId,
			'cityInfo'       => $cityInfo,
			//城市兵种数据
			'cityArmy'       => $objPlayer->Army()->toData(),
			//城市科技数据
			'cityTech'       => $objTech->get(),
			//正在使用的道具
			'cityUsingProps' => $objPlayer->Props()->get(),
			//VIP加成
			'cityVipAdd'     => $objPlayer->Vip()->getBattleAdd(),
			//联盟加成
			'cityUnionAdd'   => M_Battle_Calc::getBattleAddByUnion($unionInfo),
			//军衔加成
			'cityRankAdd'    => M_Battle_Calc::getBattleAddByMilRank($milRank),
			'zone'           => $zone,
		);
		return $retData;
	}

	/**
	 * 快速战斗优化
	 * (地图减少障碍和寻路,无法查看战报)
	 */
	static public function quickmapno() {
		$ret  = 0;
		$base = M_Config::getVal('quick_map_no');
		if (!empty($base)) {
			shuffle($base);
			$ret = isset($base[0]) ? intval($base[0]) : 0;
		}
		return $ret;
	}
}

?>