<?php

/**
 * 行军接口
 */
class C_March extends C_I {
	/**
	 * 出征部队列表
	 * @author HeJunyun
	 */
	public function AOutList() {
		$flag = T_App::SUCC;
		$errNo = '';
		$now = time();
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$cityId = intval($cityInfo['id']);
		$list = M_March::getMarchList($cityId, M_War::MARCH_OWN_ATK);
		if (!empty($cityInfo['fb_battle_id'])) { //副本战斗列表
			$BD = M_Battle_Info::get($cityInfo['fb_battle_id']);
			if (empty($BD['Id']) || $BD['CurStatus'] == T_Battle::STATUS_END) {
				$upData = array('fb_battle_id' => 0);
				M_City::setCityInfo($cityId, $upData);
			} else if (!empty($BD['Id'])) {
				$def_pos = M_MapWild::calcWildMapPosXYByNo($BD['DefPos']);
				$data[] = array(
					'Id' => M_March::FB_MARCH_ID, //副本战斗行军id为1
					'AttCityId' => $BD[T_Battle::CUR_OP_ATK]['CityId'],
					'DefCityId' => $BD[T_Battle::CUR_OP_DEF]['CityId'],
					'AttCityNickName' => $BD[T_Battle::CUR_OP_ATK]['Nickname'],
					'DefCityNickName' => $BD[T_Battle::CUR_OP_DEF]['Nickname'],
					'ActionType' => M_March::MARCH_ACTION_FB,
					'HeroList' => array_keys($BD[T_Battle::CUR_OP_ATK]['HeroDataList']),
					'AttPos' => M_MapWild::calcWildMapPosXYByNo($BD['AtkPos']),
					'DefPos' => $def_pos,
					'ArrivedTime' => $BD['StartTime'],
					'RemainingTime' => 0,
					'Award' => array(),
					'Flag' => M_March::MARCH_FLAG_BATTLE,
					'BattleId' => $BD['Id'],
					'CampStartPos' => array(0, 0, 0),
				);
			}
		}

		$info = M_BreakOut::getCityBreakOut($cityId);
		if (!empty($info['battle_id'])) { //突围战斗列表
			$BD = M_Battle_Info::get($info['battle_id']);
			if (empty($BD['Id']) || $BD['CurStatus'] == T_Battle::STATUS_END) {
				$upData = array('battle_id' => 0);
				M_BreakOut::updateCityBreakOut($cityId, $upData);
			} else if (!empty($BD['Id'])) {

				$def_pos = explode('_', $BD['DefPos']);
				array_unshift($def_pos, 0); //开头插入值保证格式统一
				$data[] = array(
					'Id' => M_March::BOUT_MARCH_ID, //突围战斗行军id为2
					'AttCityId' => $BD[T_Battle::CUR_OP_ATK]['CityId'],
					'DefCityId' => $BD[T_Battle::CUR_OP_DEF]['CityId'],
					'AttCityNickName' => $BD[T_Battle::CUR_OP_ATK]['Nickname'],
					'DefCityNickName' => $BD[T_Battle::CUR_OP_DEF]['Nickname'],
					'ActionType' => M_March::MARCH_ACTION_BOUT,
					'HeroList' => array_keys($BD[T_Battle::CUR_OP_ATK]['HeroDataList']),
					'AttPos' => M_MapWild::calcWildMapPosXYByNo($BD['AtkPos']),
					'DefPos' => $def_pos,
					'ArrivedTime' => $BD['StartTime'],
					'RemainingTime' => 0,
					'Award' => array(),
					'Flag' => M_March::MARCH_FLAG_BATTLE,
					'BattleId' => $BD['Id'],
					'CampStartPos' => array(0, 0, 0),
				);
			}
		}


		$objPlayer = new O_Player($cityId);
		$objFloor = $objPlayer->Floor();
		$bId = $objFloor->getBId();
		if (!empty($bId)) { //爬楼
			$BD = M_Battle_Info::get($bId);
			if (empty($BD['Id']) || $BD['CurStatus'] == T_Battle::STATUS_END) {
				$battleId = 0;
				$objFloor->setBId($battleId);
				$objPlayer->save();
			} else if (!empty($BD['Id'])) {
				$def_pos = explode('_', $BD['DefPos']);
				array_unshift($def_pos, 0); //开头插入值保证格式统一
				$data[] = array(
					'Id' => M_March::FLOOR_MARCH_ID,
					'AttCityId' => $BD[T_Battle::CUR_OP_ATK]['CityId'],
					'DefCityId' => $BD[T_Battle::CUR_OP_DEF]['CityId'],
					'AttCityNickName' => $BD[T_Battle::CUR_OP_ATK]['Nickname'],
					'DefCityNickName' => $BD[T_Battle::CUR_OP_DEF]['Nickname'],
					'ActionType' => M_March::MARCH_ACTION_FLOOR,
					'HeroList' => array_keys($BD[T_Battle::CUR_OP_ATK]['HeroDataList']),
					'AttPos' => M_MapWild::calcWildMapPosXYByNo($BD['AtkPos']),
					'DefPos' => $def_pos,
					'ArrivedTime' => $BD['StartTime'],
					'RemainingTime' => 0,
					'Award' => array(),
					'Flag' => M_March::MARCH_FLAG_BATTLE,
					'BattleId' => $BD['Id'],
					'CampStartPos' => array(0, 0, 0),
				);
			}
		}

		if ($list) {
			foreach ($list as $key => $val) {
				$data[] = array(
					'Id' => $val['id'],
					'AttCityId' => $val['atk_city_id'],
					'DefCityId' => $val['def_city_id'],
					'AttCityNickName' => $val['atk_nickname'],
					'DefCityNickName' => $val['def_nickname'],
					'ActionType' => $val['action_type'],
					'HeroList' => json_decode($val['hero_list'], true),
					'AttPos' => M_MapWild::calcWildMapPosXYByNo($val['atk_pos']),
					'DefPos' => M_MapWild::calcWildMapPosXYByNo($val['def_pos']),
					'ArrivedTime' => $val['arrived_time'],
					'RemainingTime' => max($val['arrived_time'] - $now, 0),
					'Award' => json_decode($val['award'], true),
					'Flag' => $val['flag'],
					'BattleId' => $val['battle_id'],
					'CampStartPos' => M_MapWild::calcWildMapPosXYByNo($val['start_pos_ext']),
				);
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 出征部队详细
	 * @author HeJunyun
	 * @param int $marchId 行军部队ID
	 */
	public function AOutInfo($marchId) {

		$errNo = T_ErrNo::ERR_ACTION;
		$marchId = intval($marchId);
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if ($marchId > 0) {
			if ($marchId == M_March::FB_MARCH_ID) {
				$list = array();
				//副本战斗的行军信息
				$BD = M_Battle_Info::get($cityInfo['fb_battle_id']);
				if (!empty($BD['Id'])) {
					$heroList = array_keys($BD[T_Battle::CUR_OP_ATK]['HeroDataList']);

					if (!empty($heroList) && is_array($heroList)) {

						foreach ($heroList as $key => $val) {
							$heroInfo = M_Hero::getHeroInfo($val); //根据指挥官ID获取详细信息
							$list[] = $heroInfo;
						}
					}
				}
			} else if ($marchId == M_March::BOUT_MARCH_ID) {
				$list = array();
				$info = M_BreakOut::getCityBreakOut($cityInfo['id']);
				$BD = M_Battle_Info::get($info['battle_id']);
				if (!empty($BD['Id'])) {
					$heroList = array_keys($BD[T_Battle::CUR_OP_ATK]['HeroDataList']);

					if (!empty($heroList) && is_array($heroList)) {
						foreach ($heroList as $key => $val) {
							$heroInfo = M_Hero::getHeroInfo($val); //根据指挥官ID获取详细信息
							$list[] = $heroInfo;
						}
					}
				}
			} else if ($marchId == M_March::FLOOR_MARCH_ID) {
				$objPlayer = new O_Player($cityInfo['id']);
				$objFloor = $objPlayer->Floor();
				$bId = $objFloor->getBId();

				$BD = M_Battle_Info::get($bId);
				$list = array();
				if (!empty($BD['Id'])) {
					$heroList = array_keys($BD[T_Battle::CUR_OP_ATK]['HeroDataList']);
					foreach ($heroList as $key => $val) {
						$heroInfo = M_Hero::getHeroInfo($val); //根据指挥官ID获取详细信息
						$list[] = $heroInfo;
					}
				}
			} else {
				$list = M_March::getHeroListByMarchId($marchId, $cityInfo['id'], M_War::MARCH_OWN_ATK);
			}

			if (!empty($list) && is_array($list)) {
				//筛选返回给前端的数据数据
				foreach ($list as $key => $val) {
					$data[] = array(
						'NickName' => $val['nickname'],
						'Quality' => $val['quality'],
						'Level' => $val['level'],
						'IsLegend' => $val['is_legend'],
						'FaceId' => $val['face_id'],
						'AttrLead' => $val['attr_lead'],
						'AttrCommand' => $val['attr_command'],
						'AttrMilitary' => $val['attr_military'],
						'AttrEnergy' => $val['attr_energy'],
						'ArmyId' => $val['army_id'],
						'ArmyNum' => $val['army_num'],
						'WeaponId' => $val['weapon_id'],
						'SkillSlot' => $val['skill_slot'],
						'SkillSlot1' => $val['skill_slot_1'],
						'SkillSlot2' => $val['skill_slot_2'],
					);
				}
			}
		}

		if ($data) {

			$errNo = '';
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 敌情警报列表
	 * @author HeJunyun
	 */
	public function AEnemyList() {
		$flag = T_App::SUCC;
		$errNo = '';
		$data = array();
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$level = $objPlayer->Build()->getLevel(M_Build::ID_RADAR);
		if ($level > 0) //验证雷达是否建立
		{
			$list = M_March::getEnemyForcesById($cityInfo['id']);
			if ($list) {
				foreach ($list as $key => $val) {
					$data[] = array(
						'Id' => $val['id'],
						'AttCityId' => $val['atk_city_id'],
						'DefCityId' => $val['def_city_id'],
						'AttCityNickName' => $val['atk_nickname'],
						'DefCityNickName' => $val['def_nickname'],
						'ActionType' => $val['action_type'],
						'HeroList' => json_decode($val['hero_list'], true),
						'AttPos' => M_MapWild::calcWildMapPosXYByNo($val['atk_pos']),
						'DefPos' => M_MapWild::calcWildMapPosXYByNo($val['def_pos']),
						'ArrivedTime' => $val['arrived_time'],
						'RemainingTime' => max($val['arrived_time'] - $now, 0),
						'Award' => json_decode($val['award'], true),
						'Flag' => $val['flag'],
						'CreateAt' => $val['create_at'],
						'BattleId' => $val['battle_id'],
					);
				}
			}
		} else {
			//$errNo = T_ErrNo::NO_EXIST_RADAR;
			$errNo = '';
			$data = array();
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 敌情警报详细
	 * @author HeJunyun
	 * @param int $marchId 行军部队ID
	 */
	public function AEnemyInfo($marchId) {
		$errNo = T_ErrNo::ERR_ACTION;
		$marchId = intval($marchId);
		$data = array();


		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if ($marchId > 0) {
			$list = M_March::getHeroListByMarchId($marchId, $cityInfo['id'], M_War::MARCH_OWN_DEF);

			if ($list) {
				$marchInfo = M_March_Info::get($marchId);
				$atkCityId = $marchInfo['atk_city_id'];
				$objPlayerAtk = new O_Player($atkCityId);

				$myLevel = $objPlayer->Build()->getLevel(M_Build::ID_RADAR);
				$atkLevel = $objPlayerAtk->Build()->getLevel(M_Build::ID_RADAR);
				//筛选返回给前端的数据数据
				$resNum = $myLevel - $atkLevel;
				if ($resNum >= -20) {
					foreach ($list as $key => $val) {
						$data[$key] = array(
							'NickName' => $resNum >= -20 ? $val['nickname'] : '',
							'Quality' => $resNum >= -20 ? $val['quality'] : 0,
							'IsLegend' => $resNum >= -20 ? $val['is_legend'] : 0,
							'FaceId' => $resNum >= -20 ? $val['face_id'] : '',
							'ArmyId' => $resNum >= 0 ? $val['army_id'] : 0,
							'WeaponId' => $resNum >= 5 ? $val['weapon_id'] : 0,
							'ArmyNum' => T_Lang::UNKNOW,
						);

						if ($resNum >= -10) {
							$formatList = M_War::$formation;
							$format = $formatList[$val['army_id']];
							foreach ($format as $k => $v) {
								if ($val['army_num'] > $k) {
									$data[$key]['ArmyNum'] = $v;
								}
							}
						}
					}
				}
			}
		}

		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 部队出征
	 * @author huwei
	 * @param int $type 行军类型[1进攻,2侦察,3占领,4空袭,5增援,6返回]
	 * @param string $defPosStr 目的地的坐标(zone,x,y)  用,分隔符
	 * @param string $heroIdList 英雄列表 array(id1,id2,id3)  用,分隔符 最小1个,最大5个
	 * @param int $isAuto 是否自动战斗(1是,0否)
	 * @param int $spPercent 减少出征时间(百分比值)
	 */
	public function AOut($type, $defPosStr, $heroIdList, $isAuto = 1, $spPercent = 0) {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$defPosArr = !empty($defPosStr) ? explode(',', $defPosStr) : array();
		$tmpArr = !empty($heroIdList) ? explode(',', $heroIdList) : array();
		$now = time();
		$attHeroIdArr = array_flip(array_flip($tmpArr));
		$heroNum = count($attHeroIdArr);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (count($defPosArr) == 3 && //检查坐标是否有(area,x,y)
			$heroNum > 0 &&
			$heroNum <= M_Config::getVal('hero_num_troop') && //检查英雄数量是否正确
			isset(M_March::$marchAction[$type])
		) //检查类型是否正确
		{
			$atkCityId = $cityInfo['id'];
			$cityId = $cityInfo['id'];
			$defPosNo = M_MapWild::calcWildMapPosNoByXY($defPosArr[0], $defPosArr[1], $defPosArr[2]);
			$mapInfo = M_MapWild::getWildMapInfo($defPosNo);

			//校正地图数据
			M_MapWild::fixWildMapHoldInfo($mapInfo);

			$defCityId = !empty($mapInfo['city_id']) ? $mapInfo['city_id'] : 0;
			$err = '';
			$vipLv = $cityInfo['vip_level'];
			$needMilPay = M_Vip::getDecrMarchTimeCost($vipLv, $spPercent);
			if ($needMilPay) {
				$objPlayer->City()->mil_pay -= $needMilPay;
			}

			if (empty($mapInfo['pos_no']) || $mapInfo['type'] == T_Map::WILD_MAP_CELL_SPACE) {
				$err = T_ErrNo::WILD_POS_IS_SPACE;
			} else if ($objPlayer->Props()->isAvoidWar()) {
				$err = T_ErrNo::AVOID_WAR_SELF;
			} else if (!M_Vip::isDecrMarchTime($vipLv, $spPercent)) //VIP减少出征时间
			{
				$err = T_ErrNo::VIP_NOT_LEVEL;
			} else if ($objPlayer->City()->mil_pay < 0) {
				$err = T_ErrNo::NO_ENOUGH_MILIPAY;
			} else if ($objPlayer->City()->newbie == M_City::NEWBIE_GUARD_YES) {
				$err = T_ErrNo::USER_ATK_IS_PROTECTED;
			} else if (M_March_Hold::exist($objPlayer->City()->pos_no)) {
				$err = T_ErrNo::MARCH_NO;
			} else if (T_Map::WILD_MAP_CELL_NPC == $mapInfo['type']) {
				$type = M_March::MARCH_ACTION_HOLD;
				$atkCityLv = $objPlayer->City()->level;
				$npcInfo = M_NPC::getInfo($mapInfo['npc_id']);
				$diffLv = $npcInfo['level'] - $atkCityLv;
				if ($diffLv < -2) {
					$err = T_ErrNo::USER_ATK_LEVEL_DOWN;
				} else if ($diffLv > 2) {
					$err = T_ErrNo::USER_ATK_LEVEL_OVER;
				}
			} else if ($defCityId > 0 &&
				T_Map::WILD_MAP_CELL_CITY == $mapInfo['type']
			) // 判断是否保护期
			{
				$objPlayerDef = new O_Player($defCityId);

				if ($objPlayerDef->City()->union_id && $objPlayerDef->City()->union_id == $objPlayer->union_id) {
					$err = T_ErrNo::UNION_THE_SAME;
				} else if ($objPlayerDef->Props()->isAvoidWar()) {
					$err = T_ErrNo::AVOID_WAR_ENEMY;
				} else if ($objPlayerDef->City()->newbie == M_City::NEWBIE_GUARD_YES) {
					$err = T_ErrNo::USER_DEF_IS_PROTECTED;
				} else if (M_March_Hold::exist($mapInfo['pos_no'])) {
					$err = T_ErrNo::NO_ATK_HOLD_CITY;
				} else {
					$atkCityLv = $objPlayer->City()->level;
					$defCityLv = $objPlayerDef->City()->level;

					$diffLv = $defCityLv - $atkCityLv;
					if ($diffLv < -2) {
						$err = T_ErrNo::USER_ATK_LEVEL_DOWN;
					} else if ($diffLv > 2) {
						$err = T_ErrNo::USER_ATK_LEVEL_OVER;
					}
				}
			}


			$errNo = $err;
			if (empty($err)) {
				switch ($type) {
					case M_March::MARCH_ACTION_BOMB:
						//非自己的 玩家城市才可以被轰炸
						$ret = $mapInfo['type'] == T_Map::WILD_MAP_CELL_CITY && $defCityId != $cityId ? M_War::hasBombWeapon($atkCityId, $attHeroIdArr) : false;
						$ret = false;
						break;
					case M_March::MARCH_ACTION_SCOUT:
						//非自己玩家城市才可以被侦查 并且部队只有一支
						$ret = $mapInfo['type'] == T_Map::WILD_MAP_CELL_CITY && $defCityId != $cityId ? M_War::hasScoutWeapon($atkCityId, $attHeroIdArr) : false;
						break;
					case M_March::MARCH_ACTION_HOLD:
						//NPC野地 才可以被占领
						$ret = $mapInfo['type'] == T_Map::WILD_MAP_CELL_NPC && $defCityId != $cityId;
						break;
					case M_March::MARCH_ACTION_HELP:
						//必须是 自己的NPC野地 才可以被增援
						$ret = in_array($mapInfo['type'], array(T_Map::WILD_MAP_CELL_CITY, T_Map::WILD_MAP_CELL_NPC)) && $defCityId == $cityId;
						$ret = false;
						break;
					case M_March::MARCH_ACTION_ATT:
						//非自己的 城市 和 NPC野地 和 临时NPC  才可以被攻击
						$ret = in_array($mapInfo['type'], array(T_Map::WILD_MAP_CELL_CITY, T_Map::WILD_MAP_CELL_NPC)) && $defCityId != $cityId;
						break;
					default:
						$ret = false;
						break;
				}

				//获取出征信息
				$tmpMarchData = M_Hero::getArmyMarchInfo($objPlayer, $attHeroIdArr);

				if ($ret && !empty($tmpMarchData[0])) {
					list($speed, $costOil, $costFood) = $tmpMarchData;

					$posDistance = M_Formula::calcMarchDistance($objPlayer->City()->pos_no, $defPosNo);
					$marchTime = M_Formula::calcMarchTime($speed, $posDistance, $spPercent);
					$totalCostOil = M_Formula::calcMarchCost($costOil, $marchTime);
					$totalCostFood = M_Formula::calcMarchCost($costFood, $marchTime);

					$objPlayer->City()->mil_order -= T_App::MARCH_CITY_COST_MILORDER;

					$errMarchTime = false;
					$defInfo = array();
					if ($mapInfo['type'] == T_Map::WILD_MAP_CELL_NPC) {
						if ($mapInfo['npc_id'] > 0) {
							$npcInfo = M_NPC::getInfo($mapInfo['npc_id']);
							$defInfo = array(
								'city_id' => $defCityId,
								'nickname' => $npcInfo['nickname'],
								'pos_no' => $defPosNo,
								'gender' => 1,
								'face_id' => $npcInfo['face_id'],
							);

							if ($npcInfo['type'] == M_NPC::TMP_NPC) //临时NPC 检测消失时间
							{
								$refreshData = M_NPC::getRandTempNpcRefreshData();

								if (!isset($refreshData[$mapInfo['npc_id']])) {
									$upData = array('npc_id' => 0);
									M_MapWild::setWildMapInfo($defPosNo, $upData);
									M_MapWild::syncWildMapBlockCache($defPosNo);
								}

								if (empty($refreshData[$mapInfo['npc_id']]['end_time']) ||
									($now + $marchTime) > $refreshData[$mapInfo['npc_id']]['end_time']
								) {
									//计算到达时间是否超过NPC消失时间
									$errMarchTime = true;
								}
							} else if ($npcInfo['type'] == M_NPC::FASCIST_NPC) //临时NPC 检测消失时间
							{
								$refreshData = M_NPC::getFixedTempNpcRefreshData();

								if (!isset($refreshData[$mapInfo['npc_id']])) {
									$upData = array('npc_id' => 0);
									M_MapWild::setWildMapInfo($defPosNo, $upData);
									M_MapWild::syncWildMapBlockCache($defPosNo);
								}

								if (empty($refreshData[$mapInfo['npc_id']]['end_time']) ||
									($now + $marchTime) > $refreshData[$mapInfo['npc_id']]['end_time']
								) {
									//计算到达时间是否超过NPC消失时间
									$errMarchTime = true;
								}
							}
						}
					} else if ($mapInfo['type'] == T_Map::WILD_MAP_CELL_CITY) {
						if ($mapInfo['city_id'] > 0) {
							$objPlayerDef = new O_Player($mapInfo['city_id']);
							$defCityInfo = $objPlayerDef->getCityBase();
							$defInfo = array(
								'city_id' => $defCityInfo['id'],
								'nickname' => $defCityInfo['nickname'],
								'pos_no' => $defPosNo,
								'gender' => $defCityInfo['gender'],
								'face_id' => $defCityInfo['face_id'],
							);
						}
					}

					$objRes = $objPlayer->Res();

					if ($objPlayer->City()->mil_order < 0) {
						$errNo = T_ErrNo::NO_ENOUGH_ENERGY;
					} else if ($objRes->incr('oil', -$totalCostOil) < 0 || $objRes->incr('food', -$totalCostFood) < 0) {
						$errNo = T_ErrNo::CITY_RES_LACK;
					} else if ($errMarchTime) {
						$errNo = T_ErrNo::ERR_MARCH_TIME;
					} else {
						$marchData = array($marchTime, $totalCostOil, $totalCostFood);
						$bCost = true;
						if ($spPercent > 0) {
							$bCost = false;
						}
						if ($needMilPay > 0) {
							$objPlayer->Log()->expense(T_App::MILPAY, $needMilPay, B_Log_Trade::E_ReductionMarchTime, $spPercent);							$bCost = true;
						}

						$ret = $objPlayer->save();
						if ($bCost && $ret) {
							$atkInfo = array(
								'city_id' => $cityInfo['id'],
								'nickname' => $cityInfo['nickname'],
								'pos_no' => $cityInfo['pos_no'],
								'gender' => $cityInfo['gender'],
								'face_id' => $cityInfo['face_id'],
							);

							$info = M_March::buildWarMarch($atkInfo, $defInfo, $type, $attHeroIdArr, $marchData, $isAuto);

							if (empty($info['ErrNo'])) {
								if (T_Map::WILD_MAP_CELL_NPC == $mapInfo['type']) {
									$objPlayer = new O_Player($cityInfo['id']);
									$objPlayer->Quest()->check('atk_wildnpc', array('num' => 1));
									$objPlayer->save();
								} else if (T_Map::WILD_MAP_CELL_CITY == $mapInfo['type']) {
									$objPlayer = new O_Player($cityInfo['id']);
									$objPlayer->Quest()->check('atk_player', array('num' => 1));
									$objPlayer->save();
								}

								$data = array(
									'MarchId' => $info['MarchId'],
									'Distance' => $posDistance,
									'MarchTime' => $marchTime,
									'TotalCostOil' => $totalCostOil,
									'TotalCostFood' => $totalCostFood,
								);


								$flag = T_App::SUCC;
								$errNo = '';


								//更新缓存
								//M_March::syncOutForcesById($atkCityId);
								M_March::syncMarch2Front($info['MarchId']);
							} else {
								$errNo = $info['ErrNo'];
							}
						}
					}
				} else {
					Logger::error(array(__METHOD__, $type, $mapInfo['type'], $ret, $tmpMarchData));
					$errNo = T_ErrNo::ARMY_MARCH_FAIL;
				}
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 撤销出征
	 * @author HeJunyun on 20110620
	 * @param int 行军ID
	 */
	public function ABack($marchId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$marchId = intval($marchId);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$res = false;

		if ($marchId) {
			$now = time();
			$marchInfo = M_March_Info::get($marchId);

			if (!empty($marchInfo['id'])) {
				if ($marchInfo['atk_city_id'] != $cityInfo['id']) { //不是该城的出征部队
					$errNo = T_ErrNo::ERR_ACTION;
				} else if ($marchInfo['action_type'] == M_March::MARCH_ACTION_BACK) { //部队正在返回
					$errNo = T_ErrNo::MARCH_ON_BACK;
				} else if ($marchInfo['flag'] == M_March::MARCH_FLAG_BATTLE) { //部队正在战斗
					$errNo = T_ErrNo::MARCH_ON_FIGHT;
				} else if ($marchInfo['flag'] == M_March::MARCH_FLAG_MOVE) { //需要判断还要多久到达
					if (($marchInfo['arrived_time'] - $now) > T_App::CAN_NOT_BACK) { //修改行军状态=>返回
						$res = M_March::setMarchBack($marchInfo['id']);
					} else { //行军最后N秒不能撤退
						$errNo = T_ErrNo::MARCH_CAN_NOT_BACK;
					}
				} else if ($marchInfo['flag'] == M_March::MARCH_FLAG_WAIT) { //修改行军状态=>返回
					$res = M_March::setMarchBack($marchInfo['id']);
				} else if ($marchInfo['flag'] == M_March::MARCH_FLAG_HOLD) {
					$bBack = false;
					if ($marchInfo['action_type'] == M_March::MARCH_ACTION_HOLD) {
						$bBack = M_War::setHoldWildBack($marchInfo);
					} else if ($marchInfo['action_type'] == M_March::MARCH_ACTION_CAMP) {
						$bBack = M_War::setHoldCampBack($marchInfo);
					} else if ($marchInfo['action_type'] == M_March::MARCH_ACTION_CITY) {
						$bBack = M_War::setHoldCityBack($marchInfo);
					} else if ($marchInfo['action_type'] == M_March::MARCH_ACTION_HOLD_CITY) {
						$bBack = M_War::setHoldCityBack($marchInfo);
					}

					if (!$bBack) {
						Logger::error(array(__METHOD__, 'Err Hold March Back:', $marchInfo));
					} else {
						$res = M_March::setMarchBack($marchInfo['id']);
					}
				} else {
					Logger::error(array(__METHOD__, 'Err March Flag:', $marchInfo));
					//$res = M_March::setMarchBack($marchInfo['id']);
					$errNo = T_ErrNo::MARCH_CAN_NOT_BACK;
				}
			} else {
				//部队不存在
				$errNo = T_ErrNo::MARCH_NO_EXIST;
			}
		}

		if ($res) {
			$errNo = '';

		}


		$data = array();
		return B_Common::result($errNo, $data);
	}

	/**
	 * 城市基础行军速度加成
	 * @author HeJunyun
	 */
	public function ABaseSpeedAdd() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$data['props'] = $objPlayer->Props()->getEffectVal('ARMY_INCR_SPEED'); //道具加速度
		$data['teach'] = array(
			M_Army::ID_FOOT => $objPlayer->Tech()->calcArmyTechSpeed(M_Army::ID_FOOT), //科技-步兵加速度
			M_Army::ID_GUN => $objPlayer->Tech()->calcArmyTechSpeed(M_Army::ID_GUN), //科技-炮兵加速度
			M_Army::ID_ARMOR => $objPlayer->Tech()->calcArmyTechSpeed(M_Army::ID_ARMOR), //科技-装甲部队加速度
			M_Army::ID_AIR => $objPlayer->Tech()->calcArmyTechSpeed(M_Army::ID_AIR) //科技-航空部队加速度
		);
		$data['skill'] = array();
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 部队出征占领城市
	 * @author duhuihui
	 * @param string $defPosStr 目的地的坐标(zone,x,y)  用,分隔符
	 * @param string $heroIdList 英雄列表 array(id1,id2,id3)  用,分隔符 最小1个,最大5个
	 * @param int $isAuto 是否自动战斗(1是,0否)
	 * @param int $spPercent 减少出征时间(百分比值)
	 */
	public function AOccupiedCity($defPosStr, $heroIdList, $isAuto = 1, $spPercent = 0) {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$defPosArr = !empty($defPosStr) ? explode(',', $defPosStr) : array(); //目的地坐标
		$tmpArr = !empty($heroIdList) ? explode(',', $heroIdList) : array();
		$now = time();
		$attHeroIdArr = array_flip(array_flip($tmpArr)); //英雄列表
		$heroNum = count($attHeroIdArr);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (count($defPosArr) == 3 && $heroNum > 0 && $heroNum <= M_Config::getVal('hero_num_troop')
		) {
			$cityId = $cityInfo['id']; //攻击方
			$defPosNo = M_MapWild::calcWildMapPosNoByXY($defPosArr[0], $defPosArr[1], $defPosArr[2]);
			$mapInfo = M_MapWild::getWildMapInfo($defPosNo); //要占领的城市的地图信息
			$tmp = M_ColonyCity::getInfo($mapInfo['city_id']); //要占领城市信息
			$holdTimeInterval = M_Config::getVal('hold_city_time_interval');
			if (!empty($mapInfo['hold_expire_time'])) {
				if (!empty($mapInfo['hold_expire_time']) &&
					date('Ymd', $mapInfo['hold_expire_time']) != date('Ymd')
				) { //被占领城市占领时间置为0

					$bUp = M_ColonyCity::setInfo($mapInfo['city_id'], array('hold_time' => 0));
					if ($bUp) {
						M_MapWild::setWildMapInfo($defPosNo, array('hold_expire_time' => $now));
					}
					//Logger::debug(array(__METHOD__, $tmp, $mapInfo, date('Ymd', $mapInfo['hold_expire_time']), date('Ymd'), $bUp));
				} else if (!empty($mapInfo['hold_expire_time']) &&
					date('Ymd', $mapInfo['hold_expire_time']) == date('Ymd') &&
					$mapInfo['hold_expire_time'] - strtotime(date('Y-m-d 00:00:00')) < T_App::ONE_HOUR * $holdTimeInterval
				) {
					$bUp = M_ColonyCity::setInfo($mapInfo['city_id'], array('hold_time' => $mapInfo['hold_expire_time'] - strtotime(date('Y-m-d 00:00:00'))));
					if ($bUp) {
						M_MapWild::setWildMapInfo($defPosNo, array('hold_expire_time' => $now + T_App::ONE_HOUR * $holdTimeInterval - $mapInfo['hold_expire_time'] - strtotime(date('Y-m-d 00:00:00'))));
					}

				}
			} else {
				if ($tmp['hold_time'] > T_App::ONE_HOUR * $holdTimeInterval) {
					M_ColonyCity::setInfo($mapInfo['city_id'], array('hold_time' => 0));
				}
			}
			$defCityId = $mapInfo['city_id'];

			if ($defCityId > 0) {
				$objPlayerDef = new O_Player($defCityId);
				$defCityInfo = $objPlayerDef->getCityBase();

				$err = '';
				$vipLv = $cityInfo['vip_level'];
				$needMilPay = M_Vip::getDecrMarchTimeCost($vipLv, $spPercent);
				if ($needMilPay) {
					$objPlayer->City()->mil_pay -= $needMilPay;
				}

				$defColonyInfo = M_ColonyCity::getInfo($mapInfo['city_id']); //要占领城市信息

				//Logger::debug(array(__METHOD__, $mapInfo['city_id'], $defColonyInfo, $mapInfo));

				$holdCityId = !empty($defColonyInfo['atk_city_id']) ? $defColonyInfo['atk_city_id'] : 0;
				$holdCityInfo = M_City::getInfo($holdCityId);

				$objPlayerHold = new O_Player($holdCityId);
				$defCityInfo = $objPlayerHold->getCityBase();

				if ($holdCityId == $cityInfo['id'] || $defCityId == $cityInfo['id']) {
					M_ColonyCity::revise($mapInfo['city_id']);
					$err = T_ErrNo::NO_HOLD_SELF;
				} else if (empty($mapInfo['pos_no']) || $mapInfo['type'] == T_Map::WILD_MAP_CELL_SPACE) {
					$err = T_ErrNo::WILD_POS_IS_SPACE;
				} else if ($objPlayer->Props()->isAvoidHold()) {
					$err = T_ErrNo::AVOID_HOLD_SELF;
				} else if ($objPlayerDef->Props()->isAvoidHold()) {
					$err = T_ErrNo::AVOID_HOLD_ENEMY;
				} else if (!M_Vip::isDecrMarchTime($vipLv, $spPercent)) //VIP减少出征时间
				{
					$err = T_ErrNo::VIP_NOT_LEVEL;
				} else if ($objPlayer->City()->mil_pay < 0) {
					$err = T_ErrNo::NO_ENOUGH_MILIPAY;
				} else if ($cityInfo['newbie'] == M_City::NEWBIE_GUARD_YES) {
					$err = T_ErrNo::USER_ATK_IS_PROTECTED;
				} else if ($defCityInfo['newbie'] == M_City::NEWBIE_GUARD_YES) {
					$err = T_ErrNo::USER_DEF_IS_PROTECTED;
				} else if (!empty($cityInfo['union_id']) &&
					!empty($defCityInfo['union_id']) &&
					$defCityInfo['union_id'] == $cityInfo['union_id']
				) {
					$err = T_ErrNo::UNION_THE_SAME; //攻击方与占领方是同盟
				} else if (!empty($cityInfo['union_id']) &&
					!empty($holdCityInfo['union_id']) &&
					$holdCityInfo['union_id'] == $cityInfo['union_id']
				) {
					$err = T_ErrNo::UNION_THE_SAME; //攻击方与被占领方是同盟
				} else if ($defColonyInfo['hold_time'] > T_App::ONE_HOUR * $holdTimeInterval) { // 此城市今天已被占领的CD时间超过了4个小时
					//Logger::debug(array(__METHOD__, $defColonyInfo, $holdTimeInterval));
					$err = T_ErrNo::MARCH_NO_OUTTIME;
				} else if (M_March_Hold::exist($cityInfo['pos_no'])) {
					$err = T_ErrNo::MARCH_NO;
				} else {
					$atkCityLv = $cityInfo['level'];
					$defCityLv = $defCityInfo['level'];

					$diffLv = $defCityLv - $atkCityLv;
					if ($diffLv < -2) {
						$err = T_ErrNo::USER_ATK_LEVEL_DOWN;
					} else if ($diffLv > 2) {
						$err = T_ErrNo::USER_ATK_LEVEL_OVER;
					}
				}

				if (empty($err)) {
					//获取出征信息
					$tmpMarchData = M_Hero::getArmyMarchInfo($objPlayer, $attHeroIdArr); //array(基础速度,基础消耗油,基础消食物)

					if (!empty($tmpMarchData[0])) {
						list($speed, $costOil, $costFood) = $tmpMarchData;

						$posDistance = M_Formula::calcMarchDistance($cityInfo['pos_no'], $defPosNo); //计算行军的距离
						$marchTime = M_Formula::calcMarchTime($speed, $posDistance, $spPercent); //计算行军时间
						$totalCostOil = M_Formula::calcMarchCost($costOil, $marchTime); //消耗油
						$totalCostFood = M_Formula::calcMarchCost($costFood, $marchTime); //消耗食物

						$objPlayer->City()->mil_order -= T_App::MARCH_CITY_COST_MILORDER;

						$errMarchTime = false;
						$defInfo = array();

						if ($defCityId > 0) {
							$defInfo = array(
								'city_id' => $defCityId,
								'nickname' => $defCityInfo['nickname'],
								'pos_no' => $defPosNo,
								'gender' => $defCityInfo['gender'],
								'face_id' => $defCityInfo['face_id'],
							);
						}
						//写入key
						$objRes = $objPlayer->Res();

						if ($objPlayer->City()->mil_order < 0) {
							$errNo = T_ErrNo::NO_ENOUGH_MILORDER;
						} else if ($objRes->incr('oil', -$totalCostOil) < 0 || $objRes->incr('food', -$totalCostFood) < 0) {
							$errNo = T_ErrNo::CITY_RES_LACK;
						} else if ($errMarchTime) {
							$errNo = T_ErrNo::ERR_MARCH_TIME;
						} else {
							$marchData = array($marchTime, $totalCostOil, $totalCostFood);
							$bCost = true;
							if ($spPercent > 0) {
								$bCost = false;
							}

							if ($needMilPay > 0) {
								$objPlayer->Log()->expense(T_App::MILPAY, $needMilPay, B_Log_Trade::E_ReductionMarchTime, $spPercent);								$bCost = true;
							}

							$ret = $objPlayer->save();
							if ($bCost) {
								$atkInfo = array(
									'city_id' => $cityInfo['id'],
									'nickname' => $cityInfo['nickname'],
									'pos_no' => $cityInfo['pos_no'],
									'gender' => $cityInfo['gender'],
									'face_id' => $cityInfo['face_id'],
								);

								$info = M_March::buildWarMarch($atkInfo, $defInfo, M_March::MARCH_ACTION_CITY, $attHeroIdArr, $marchData, $isAuto);
								if (empty($info['ErrNo'])) {
									$objPlayer = new O_Player($cityInfo['id']);
									$objPlayer->Quest()->check('atk_player', array('num' => 1));
									$objPlayer->save();
									$data = array(
										'MarchId' => $info['MarchId'],
										'Distance' => $posDistance,
										'MarchTime' => $marchTime,
										'TotalCostOil' => $totalCostOil,
										'TotalCostFood' => $totalCostFood,
									);

									$errNo = '';

									//M_March::syncOutForcesById($atkCityId);
									M_March::syncMarch2Front($info['MarchId']);
								} else {
									$errNo = $info['ErrNo'];
								}
							}
						}
					} else {
						Logger::error(array(__METHOD__, M_March::MARCH_ACTION_CITY, $mapInfo['type'], $tmpMarchData));
						$errNo = T_ErrNo::ARMY_MARCH_FAIL;
					}
				} else {
					$errNo = $err;
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 驻守 占领城市
	 * @author duhuihui
	 * @param string $defPosStr 目的地的坐标(zone,x,y)  用,分隔符
	 */
	public function AHoldCity($defPosStr, $heroIdList, $isAuto = 1, $spPercent = 0) {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$defPosArr = !empty($defPosStr) ? explode(',', $defPosStr) : array(); //目的地坐标
		$tmpArr = !empty($heroIdList) ? explode(',', $heroIdList) : array();
		$now = time();
		$attHeroIdArr = array_flip(array_flip($tmpArr)); //英雄列表
		$heroNum = count($attHeroIdArr);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (count($defPosArr) == 3 && $heroNum > 0 && $heroNum <= M_Config::getVal('hero_num_troop')
		) {
			$atkCityId = $cityInfo['id'];
			$defPosNo = M_MapWild::calcWildMapPosNoByXY($defPosArr[0], $defPosArr[1], $defPosArr[2]);
			$mapInfo = M_MapWild::getWildMapInfo($defPosNo);
			$defColonyInfo = M_ColonyCity::getInfo($mapInfo['city_id']); //要占领城市信息
			$defCityId = $mapInfo['city_id'];
			$vipLv = $cityInfo['vip_level'];
			$needMilPay = M_Vip::getDecrMarchTimeCost($vipLv, $spPercent);
			if ($needMilPay) {
				$objPlayer->City()->mil_pay -= $needMilPay;
			}

			$objPlayerDef = new O_Player($defCityId);
			$defCityInfo = $objPlayerDef->getCityBase();

			if ($defCityId > 0) {
				$err = '';
				$isHold = 0;
				$marchList = M_March::getMarchList($cityInfo['id'], M_War::MARCH_OWN_ATK);
				foreach ($marchList as $marchInfo) {
					if ($marchInfo['def_pos'] == $defPosNo) {
						$isHold = 1;
					}
				}
				if (!empty($defColonyInfo['atk_city_id']) &&
					$defColonyInfo['atk_city_id'] != $cityInfo['id']
				) {
					$err = T_ErrNo::HOLD_NO;
				} else if (!empty($isHold)) {
					$err = T_ErrNo::HOLD_NO_MARCHID;
				} else if (empty($defColonyInfo['atk_city_id'])) {
					$err = T_ErrNo::HOLD_NO_OCCUPIED;
				} else if ($objPlayer->Props()->isAvoidHold()) {
					$err = T_ErrNo::AVOID_HOLD_SELF;
				} else if (!M_Vip::isDecrMarchTime($vipLv, $spPercent)) //VIP减少出征时间
				{
					$err = T_ErrNo::VIP_NOT_LEVEL;
				} else if ($objPlayer->City()->mil_pay < 0) {
					$err = T_ErrNo::NO_ENOUGH_MILIPAY;
				} else if (M_March_Hold::exist($cityInfo['pos_no'])) {
					$err = T_ErrNo::MARCH_NO;
				}
				if (empty($err)) { //获取出征信息
					$tmpMarchData = M_Hero::getArmyMarchInfo($objPlayer, $attHeroIdArr); //array (基础速度,基础消耗油,基础消食物)

					if (!empty($tmpMarchData[0])) {
						list($speed, $costOil, $costFood) = $tmpMarchData;

						$posDistance = M_Formula::calcMarchDistance($cityInfo['pos_no'], $defPosNo); //计算行军的距离
						$marchTime = M_Formula::calcMarchTime($speed, $posDistance, $spPercent); //计算行军时间
						$totalCostOil = M_Formula::calcMarchCost($costOil, $marchTime); //消耗油
						$totalCostFood = M_Formula::calcMarchCost($costFood, $marchTime); //消耗食物

						$objPlayer->City()->mil_order -= T_App::MARCH_CITY_COST_MILORDER;
						$errMarchTime = false;
						$defInfo = array();

						if ($mapInfo['city_id'] > 0) {
							$defCityInfo = M_City::getInfo($mapInfo['city_id']);
							$defInfo = array(
								'city_id' => $defCityInfo['id'],
								'nickname' => $defCityInfo['nickname'],
								'pos_no' => $defPosNo,
								'gender' => $defCityInfo['gender'],
								'face_id' => $defCityInfo['face_id'],
							);
						}
						//写入key
						$objRes = $objPlayer->Res();
						if ($objPlayer->City()->mil_order < 0) {
							$errNo = T_ErrNo::NO_ENOUGH_ENERGY;
						} else if ($objRes->incr('oil', -$totalCostOil) < 0 || $objRes->incr('food', -$totalCostFood) < 0) {
							$errNo = T_ErrNo::CITY_RES_LACK;
						} else if ($errMarchTime) {
							$errNo = T_ErrNo::ERR_MARCH_TIME;
						} else {
							$marchData = array($marchTime, $totalCostOil, $totalCostFood);
							$bCost = true;
							if ($spPercent > 0) {
								$bCost = false;
							}
							if ($needMilPay > 0) {
								$objPlayer->Log()->expense(T_App::MILPAY, $needMilPay, B_Log_Trade::E_ReductionMarchTime, $spPercent);
								$bCost = true;
							}

							$ret = $objPlayer->save();
							if ($bCost && $ret) {
								$atkInfo = array(
									'city_id' => $cityInfo['id'],
									'nickname' => $cityInfo['nickname'],
									'pos_no' => $cityInfo['pos_no'],
									'gender' => $cityInfo['gender'],
									'face_id' => $cityInfo['face_id'],
								);

								$info = M_March::buildWarMarch($atkInfo, $defInfo, M_March::MARCH_ACTION_HOLD_CITY, $attHeroIdArr, $marchData, $isAuto);

								if (empty($info['ErrNo'])) {
									$objPlayer->Quest()->check('atk_player', array('num' => 1));
									$objPlayer->save();

									$data = array(
										'MarchId' => $info['MarchId'],
										'Distance' => $posDistance,
										'MarchTime' => $marchTime,
										'TotalCostOil' => $totalCostOil,
										'TotalCostFood' => $totalCostFood,
									);

									$wildInfo = M_ColonyCity::getNoByPosNo($cityInfo['id'], $defPosNo);
									//M_MapWild::setWildMapInfo($defPosNo, array('march_id' => $info['MarchId']));
									$no = 0;
									if (!empty($wildInfo)) {
										$no = $wildInfo['no'];
									}
									if (!empty($no)) {
										$msRow[$no] = array('MarchId' => $info['MarchId'], 'MarchType' => 2);
										M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_CITY_COLONY, $msRow);
									}

									$errNo = '';

									//M_March::syncOutForcesById($atkCityId);
									M_March::syncMarch2Front($info['MarchId']);
								} else {
									$errNo = $info['ErrNo'];
								}
							}
						}
					} else {
						Logger::error(array(__METHOD__, M_March::MARCH_ACTION_CITY, $mapInfo['type'], $tmpMarchData));
						$errNo = T_ErrNo::ARMY_MARCH_FAIL;
					}
				} else {
					$errNo = $err;
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 解救城市
	 * @author duhuihui
	 * @param string $defPosStr 目的地的坐标(zone,x,y)  用,分隔符
	 * @param string $heroIdList 英雄列表 array(id1,id2,id3)  用,分隔符 最小1个,最大5个
	 * @param int $isAuto 是否自动战斗(1是,0否)
	 * @param int $type 0自己解救自己,1同盟解救自己
	 */
	public function ARescueCity($defPosStr, $heroIdList, $isAuto = 1, $spPercent = 0) { //自己解救自己行军时间为1分钟，别人解救自己有行军时间
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$defPosArr = !empty($defPosStr) ? explode(',', $defPosStr) : array(); //目的地坐标
		$tmpArr = !empty($heroIdList) ? explode(',', $heroIdList) : array();
		$now = time();
		$attHeroIdArr = array_flip(array_flip($tmpArr)); //英雄列表
		$heroNum = count($attHeroIdArr);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (count($defPosArr) == 3 && $heroNum > 0 && $heroNum <= M_Config::getVal('hero_num_troop')
		) {

			$defPosNo = M_MapWild::calcWildMapPosNoByXY($defPosArr[0], $defPosArr[1], $defPosArr[2]);
			$mapInfo = M_MapWild::getWildMapInfo($defPosNo);
			$defCityId = $mapInfo['city_id'];

			if ($defCityId > 0) { //存在城市
				$isSelf = false;
				$err = '';
				$defColonyInfo = M_ColonyCity::getInfo($defCityId);

				$objPlayerDef = new O_Player($defCityId);
				$defCityInfopos = $objPlayerDef->getCityBase();
				$needMilPay = M_Vip::getDecrMarchTimeCost($cityInfo['vip_level'], $spPercent);
				if ($needMilPay) {
					$objPlayer->City()->mil_pay -= $needMilPay;
				}

				if ($objPlayer->Props()->isAvoidHold()) {
					$err = T_ErrNo::AVOID_HOLD_SELF;
				} else if ($defCityInfopos['id']) {
					if ($defCityInfopos['id'] == $cityInfo['id']) { //解救自己
						$objPlayerDef = new O_Player($defCityId);
						list($diff, $flag) = $objPlayerDef->CD()->toFront(O_CD::TYPE_RESCUE);
						if ($diff > 0) { //解救CD时间判断
							$err = T_ErrNo::OUT_RESCUE_TIME;
						}
						$isSelf = true;
					} else { //盟友解救
						$occupiedCityInfo = array();
						if ($defColonyInfo['atk_city_id'] > 0) {
							$occupiedCityInfo = M_City::getInfo($defColonyInfo['atk_city_id']);
						}
						if (empty($defCityInfopos['union_id']) || empty($cityInfo['union_id']) || $defCityInfopos['union_id'] != $cityInfo['union_id']) {
							$err = T_ErrNo::NO_RESCUE_UNION;
						} else if ($objPlayer->City()->mil_pay < 0) {
							$err = T_ErrNo::NO_ENOUGH_MILIPAY;
						} else if (!empty($occupiedCityInfo['union_id']) && $occupiedCityInfo['union_id'] == $cityInfo['union_id']) {
							$err = T_ErrNo::NO_RESCUE_OCCUPIED_UNION;
						}
					}
				} else {
					$err = T_ErrNo::CITY_NO_EXIST;
				}

				if (empty($err)) {
					$atkCityId = $cityInfo['id'];
					//获取出征信息
					$tmpMarchData = M_Hero::getArmyMarchInfo($objPlayer, $attHeroIdArr); //array (基础速度,基础消耗油,基础消食物)

					if (!empty($tmpMarchData[0])) {
						list($speed, $costOil, $costFood) = $tmpMarchData;

						$posDistance = M_Formula::calcMarchDistance($cityInfo['pos_no'], $defPosNo); //计算行军的距离
						if ($isSelf) {
							$marchTime = T_App::ONE_MINUTE;
						} else {
							$marchTime = M_Formula::calcMarchTime($speed, $posDistance, $spPercent); //计算行军时间
						}
						$totalCostOil = M_Formula::calcMarchCost($costOil, $marchTime); //消耗油
						$totalCostFood = M_Formula::calcMarchCost($costFood, $marchTime); //消耗食物

						$objPlayer->City()->mil_order -= T_App::MARCH_CITY_COST_MILORDER;
						$errMarchTime = false;

						$defInfo = array(
							'city_id' => 0,
							'nickname' => $cityInfo['nickname'],
							'pos_no' => $defPosNo,
							'gender' => $cityInfo['gender'],
							'face_id' => $cityInfo['face_id'],
						);

						//写入key
						$objRes = $objPlayer->Res();
						if ($objPlayer->City()->mil_order < 0) {
							$errNo = T_ErrNo::NO_ENOUGH_ENERGY;
						} else if ($objRes->incr('oil', -$totalCostOil) < 0 || $objRes->incr('food', -$totalCostFood) < 0) {
							$errNo = T_ErrNo::CITY_RES_LACK;
						} else if ($errMarchTime) {
							$errNo = T_ErrNo::ERR_MARCH_TIME;
						} else {
							$marchData = array($marchTime, $totalCostOil, $totalCostFood);
							$bCost = true;
							if ($spPercent > 0) {
								$bCost = false;
							}
							if ($needMilPay > 0) {
								$objPlayer->Log()->expense(T_App::MILPAY, $needMilPay, B_Log_Trade::E_ReductionMarchTime, $spPercent);

								$bCost = true;
							}

							$ret = $objPlayer->save();
							if ($bCost && $ret) {
								$atkInfo = array(
									'city_id' => $cityInfo['id'],
									'nickname' => $cityInfo['nickname'],
									'pos_no' => $cityInfo['pos_no'],
									'gender' => $cityInfo['gender'],
									'face_id' => $cityInfo['face_id'],
								);

								$info = M_March::buildWarMarch($atkInfo, $defInfo, M_March::MARCH_ACTION_RESCUE_CITY, $attHeroIdArr, $marchData, $isAuto);

								if (empty($info['ErrNo'])) {
									$objPlayer->Quest()->check('atk_player', array('num' => 1));
									$objPlayer->save();
									$data = array(
										'MarchId' => $info['MarchId'],
										'Distance' => $posDistance,
										'MarchTime' => $marchTime,
										'TotalCostOil' => $totalCostOil,
										'TotalCostFood' => $totalCostFood,
									);

									// 									M_MapWild::setWildMapInfo($defPosNo, array('march_id' => $info['MarchId']));
									//自己解救自己时才会更新解救CD时间和解救次数
									if ($isSelf) {
										$rescueInterval = M_Config::getVal('rescue_cd');

										$objPlayer->CD()->set(O_CD::TYPE_RESCUE, 1, $rescueInterval * T_App::ONE_MINUTE);
										$objPlayer->save();
									}

									$errNo = '';

									//更新缓存
									//M_March::syncOutForcesById($atkCityId);
									M_March::syncMarch2Front($info['MarchId']);
								} else {
									$errNo = $info['ErrNo'];
								}
							}

						}
					} else {
						Logger::error(array(__METHOD__, M_March::MARCH_ACTION_CITY, $mapInfo['type'], $tmpMarchData));
						$errNo = T_ErrNo::ARMY_MARCH_FAIL;
					}
				} else {
					$errNo = $err;
				}
			}

		}
		return B_Common::result($errNo, $data);
	}
}