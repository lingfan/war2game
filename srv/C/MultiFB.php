<?php
//
//class C_MultiFB extends C_I {
//	public function AInfo() {
//		$errNo = T_ErrNo::ERR_ACTION;
//		$data = array();
//
//		$cityId = M_Auth::myCid();
//		$objPlayer = new O_Player($cityId);
//		$cityInfo = $objPlayer->getCityBase();
//		if (!empty($cityInfo['id'])) {
//			$info = M_MultiFB::getCityInfo($cityInfo['id']);
//			if ($info['city_id']) {
//				$cost = M_Formula::calcAddupPerCost(M_Config::getVal('multi_fb_buy_cost'), $info['daily_buy_times'] + 1);
//
//				$data = array(
//					'OpenFlag' => 1,
//					'FreeTimes' => $info['daily_free_times'],
//					'BuyTimes' => $info['left_buy_times'],
//					'DailyBuyTimes' => $info['daily_buy_times'],
//					'DailyBuyMilpay' => $cost,
//					'TeamId' => $cityInfo['id'] == 11 ? 3 : $info['team_id'],
//				);
//
//
//				$errNo = '';
//			}
//
//		}
//
//
//		return B_Common::result($errNo, $data);
//	}
//
//	public function ABuyTimes() {
//		$errNo = T_ErrNo::ERR_ACTION;
//		$data = array();
//
//		$cityId = M_Auth::myCid();
//		$objPlayer = new O_Player($cityId);
//		$cityInfo = $objPlayer->getCityBase();
//		if (!empty($cityInfo['id'])) {
//			$cityMultiFBInfo = M_MultiFB::getCityInfo($cityInfo['id']);
//			if ($cityMultiFBInfo['city_id']) {
//				$nextBuyTimes = $cityMultiFBInfo['daily_buy_times'] + 1;
//				$leftBuyTimes = $cityMultiFBInfo['left_buy_times'] + 1;
//				$cost = M_Formula::calcAddupPerCost(M_Config::getVal('multi_fb_buy_cost'), $nextBuyTimes);
//				$err = '';
//				if ($cityInfo['mil_pay'] < $cost) {
//					$err = T_ErrNo::NO_ENOUGH_MILIPAY;
//				} else if (!$objPlayer->City()->decrCurrency(T_App::MILPAY, $cost, B_Log_Trade::E_BuyMultiFBTimes, $nextBuyTimes)) {
//					$err = T_ErrNo::NO_ENOUGH_MILIPAY;
//				}
//
//				if (empty($err)) {
//					$upArr = array(
//						'city_id' => $cityInfo['id'],
//						'daily_buy_times' => $nextBuyTimes,
//						'left_buy_times' => $leftBuyTimes,
//					);
//					$ret = M_MultiFB::setCityInfo($upArr, true);
//					if ($ret) {
//
//						$errNo = '';
//						$cost = M_Formula::calcAddupPerCost(M_Config::getVal('multi_fb_buy_cost'), $nextBuyTimes + 1);
//						$data = array(
//							'BuyTimes' => $leftBuyTimes,
//							'DailyBuyTimes' => $nextBuyTimes,
//							'DailyBuyMilpay' => $cost,
//						);
//					}
//				} else {
//					$errNo = $err;
//				}
//
//			}
//		}
//
//
//		return B_Common::result($errNo, $data);
//	}
//
//	/**
//	 * 队伍列表
//	 * @author huwei
//	 * @param string $fbIdStr
//	 * @param int $page
//	 */
//	public function ATeamList($fbIdStr, $page = 1) {
//
//		$errNo = T_ErrNo::ERR_ACTION;
//		$data = array();
//		$fbIds = explode(',', $fbIdStr);
//		$cityId = M_Auth::myCid();
//		$objPlayer = new O_Player($cityId);
//		$cityInfo = $objPlayer->getCityBase();
//		if (!empty($cityInfo['id']) && count($fbIds) > 0) {
//			$listIds = array();
//			foreach ($fbIds as $fbId) {
//				$tmpIds = M_MultiFB::getTeamList($fbId);
//				$listIds = array_merge($listIds, $tmpIds);
//			}
//
//			$tmpIdArr = M_MultiFB::parsePage($listIds, $page);
//			$list = array();
//			foreach ($tmpIdArr['listIds'] as $id) {
//				$info = M_MultiFB::getInfo($id);
//
//				if ($info['id']) {
//					$num = 0;
//					for ($i = 1; $i < 6; $i++) {
//						$pos = 'pos_' . $i;
//						if (!empty($info[$pos])) {
//							$num++;
//						}
//					}
//					$header = json_decode($info['pos_1'], true);
//					$list[] = array(
//						'TeamId' => $id,
//						'Nickname' => $header['nickname'],
//						'FBId' => $info['multi_fb_id'],
//						'Type' => $info['type'],
//						'Num' => $num,
//						'UnionId' => $info['union_id'],
//						'CreateAt' => $info['create_at'],
//						'StartTime' => $info['start_time'],
//					);
//				}
//			}
//
//			$data['List'] = $list;
//			$data['CurPage'] = $tmpIdArr['curPage'];
//			$data['TotalPage'] = $tmpIdArr['totalPage'];
//
//			$flag = T_App::SUCC;
//			$errNo = '';
//		}
//
//		return B_Common::result($errNo, $data);
//	}
//
//	/**
//	 * 队伍信息
//	 * @author huwei
//	 *
//	 */
//	public function ATeamInfo($teamId) {
//
//		$errNo = T_ErrNo::ERR_ACTION;
//		$data = array();
//
//		$cityId = M_Auth::myCid();
//		$objPlayer = new O_Player($cityId);
//		$cityInfo = $objPlayer->getCityBase();
//
//		if (!empty($cityInfo['id'])) {
//			$info = M_MultiFB::getInfo($teamId);
//			if ($info['id']) {
//				for ($i = 1; $i <= 5; $i++) {
//					$posArr = array();
//					$pos = 'pos_' . $i;
//					if (!empty($info[$pos])) {
//						$posArr = json_decode($info[$pos], true);
//						$heroCityInfo = M_City::getInfo($posArr['city_id']);
//						$heroArr = $posArr['hero'];
//						$hero = array();
//						$heroArmyNumAdd = M_Hero::heroArmyNumAdd($heroCityInfo['id'], $heroCityInfo['union_id']);
//
//						foreach ($heroArr as $heroId) {
//							$heroInfo = M_Hero::getHeroInfo($heroId);
//
//							$hero[] = array(
//								'HeroId' => $heroId,
//								'CityId' => $heroInfo['city_id'],
//								'NickName' => $heroInfo['nickname'],
//								'Gender' => $heroInfo['gender'],
//								'Quality' => $heroInfo['quality'],
//								'Level' => $heroInfo['level'],
//								'FaceId' => $heroInfo['face_id'],
//								'IsLegend' => 1,
//								'AttrLead' => $heroInfo['attr_lead'],
//								'AttrCommand' => $heroInfo['attr_command'],
//								'AttrMilitary' => $heroInfo['attr_military'],
//								'AttrEnergy' => $heroInfo['attr_energy'],
//								'MaxArmyNum' => M_Formula::calcHeroMaxArmyNum($heroInfo['level'], $heroInfo['skill_army_num'], $heroArmyNumAdd),
//								'SkillSlot' => $heroInfo['skill_slot'] ? $heroInfo['skill_slot'] : 0,
//								'SkillSlot1' => $heroInfo['skill_slot_1'] ? $heroInfo['skill_slot_1'] : 0,
//								'SkillSlot2' => $heroInfo['skill_slot_2'] ? $heroInfo['skill_slot_2'] : 0,
//								'ArmyNum' => $heroInfo['army_num'],
//								'ArmyId' => $heroInfo['army_id'],
//								'WeaponId' => $heroInfo['weapon_id'],
//							);
//						}
//						$posArr['hero'] = $hero;
//					}
//					$info[$pos] = $posArr;
//				}
//
//				$data = array(
//					'TeamId' => $info['id'],
//					'FBId' => $info['multi_fb_id'],
//					'Nickname' => $info['pos_1']['nickname'],
//					'Type' => $info['type'],
//					'Pos1' => $info['pos_1'],
//					'Pos2' => $info['pos_2'],
//					'Pos3' => $info['pos_3'],
//					'Pos4' => $info['pos_4'],
//					'Pos5' => $info['pos_5'],
//					'HoldDefLine' => !empty($info['hold_def_line']) ? json_decode($info['hold_def_line'], true) : array(),
//					'UnionId' => $info['union_id'],
//					'CreateAt' => $info['create_at'],
//					'StartTime' => $info['start_time'],
//				);
//
//				$errNo = '';
//			}
//		}
//
//
//		return B_Common::result($errNo, $data);
//	}
//
//	/**
//	 * 建立队伍
//	 * @author huwei
//	 *
//	 */
//	public function ATeamCreate($multiFBId, $heroIds, $isUnion = 0, $isAuto = 0) {
//
//		$errNo = T_ErrNo::ERR_ACTION;
//		$data = array();
//		$cityId = M_Auth::myCid();
//		$objPlayer = new O_Player($cityId);
//		$cityInfo = $objPlayer->getCityBase();
//		if (!empty($cityInfo['id'])) {
//			$baseList = M_MultiFB::getBaseList();
//			if (isset($baseList[$multiFBId])) {
//				$err = '';
//				$cityMultiFBInfo = M_MultiFB::getCityInfo($cityInfo['id']);
//				$heroIds = explode(',', $heroIds);
//
//				//单人副本进度,城市等级,军官数量,威望
//				list($passFBNo, $cityLevel, $heroNum, $renown, $maxPlayerNum) = explode(',', $baseList[$multiFBId]['join_rule']);
//				$type = $baseList[$multiFBId]['type'];
//
//				if ($cityMultiFBInfo['daily_free_times'] + $cityMultiFBInfo['left_buy_times'] < 1) {
//					$err = T_ErrNo::MULTI_FB_NO_TIMES;
//				} else if ($cityMultiFBInfo['team_id']) {
//					$err = T_ErrNo::MULTI_FB_CITY_EXIST;
//				} else if ($cityInfo['last_fb_no'] < $passFBNo) {
//					$err = T_ErrNo::MULTI_FB_NO_FBNO;
//				} else if ($cityInfo['level'] < $cityLevel) {
//					$err = T_ErrNo::MULTI_FB_NO_LEVEL;
//				} else if (count($heroIds) < $heroNum) {
//					$err = T_ErrNo::HERO_NOT_NUM;
//				} else if (!empty($renown) && $cityInfo['renown'] < $renown) {
//					$err = T_ErrNo::NO_ENOUGH_RENOWN;
//				} else if (!M_Hero::checkHeroStatus($cityInfo['id'], $heroIds)) {
//					$err = T_ErrNo::HERO_STATUS_ERR;
//				} else if ($objPlayerDef->Props()->isAvoidWar()) {
//					$err = T_ErrNo::AVOID_WAR_ENEMY;
//				} else if ($cityInfo['newbie'] == M_City::NEWBIE_GUARD_YES) {
//					$err = T_ErrNo::USER_DEF_IS_PROTECTED;
//				} else if (!empty($isUnion) && empty($cityInfo['union_id'])) {
//					$err = T_ErrNo::UNION_NOT_EXIST;
//				}
//
//				if (empty($err)) {
//					$cityTmp = array(
//						'city_id' => $cityInfo['id'],
//						'nickname' => $cityInfo['nickname'],
//						'hero' => $heroIds,
//						'ploy' => array(M_MultiFB::ADDITION_ATK => 0, M_MultiFB::ADDITION_DEF => 0, M_MultiFB::ADDITION_HP => 0, M_MultiFB::ADDITION_CURE => 0),
//						'status' => 1,
//						'point' => 0,
//						'is_auto' => $isAuto,
//					);
//					$info = array(
//						'multi_fb_id' => $multiFBId,
//						'type' => $type,
//						'city_pos' => json_encode(array($cityInfo['id'] => 'pos_1')),
//						'pos_1' => json_encode($cityTmp),
//						'create_at' => time(),
//						'hold_def_line' => '[]',
//						'union_id' => !empty($isUnion) ? $cityInfo['union_id'] : 0,
//						'start_time' => 0,
//					);
//
//					$teamId = M_MultiFB::addInfo($info);
//					if ($teamId) {
//						$upArr = array(
//							'city_id' => $cityInfo['id'],
//							'team_id' => $teamId
//						);
//
//						M_Hero::changeHeroFlag($cityInfo['id'], $heroIds, T_Hero::FLAG_HOLD);
//
//						$ret = M_MultiFB::setCityInfo($upArr, true);
//
//						$errNo = '';
//						$data = array(
//							'TeamId' => $teamId,
//							'FBId' => $multiFBId,
//							'Nickname' => $cityTmp['nickname'],
//							'Pos1' => $cityTmp,
//							'Pos2' => array(),
//							'Pos3' => array(),
//							'Pos4' => array(),
//							'Pos5' => array(),
//							'Type' => $info['type'],
//							'HoldDefLine' => array(),
//							'UnionId' => $info['union_id'],
//							'CreateAt' => $info['create_at'],
//							'StartTime' => $info['start_time'],
//						);
//					}
//				} else {
//					$errNo = $err;
//				}
//			} else {
//				$errNo = T_ErrNo::MULTI_FB_NO_EXIST;
//			}
//		}
//
//
//		return B_Common::result($errNo, $data);
//	}
//
//	/**
//	 * 加入队伍
//	 * @author huwei
//	 *
//	 */
//	public function ATeamJoin($teamId, $heroIds, $isAuto = 0) {
//
//		$errNo = T_ErrNo::ERR_ACTION;
//		$data = array();
//		$cityId = M_Auth::myCid();
//		$objPlayer = new O_Player($cityId);
//		$cityInfo = $objPlayer->getCityBase();
//		if (!empty($cityInfo['id']) && $teamId > 0) {
//			$teamInfo = M_MultiFB::getInfo($teamId);
//			if (!empty($teamInfo['id'])) {
//				$teamInfo['union_id'] = 0;
//				$cityPos = json_decode($teamInfo['city_pos'], true);
//				$cityMultiFBInfo = M_MultiFB::getCityInfo($cityInfo['id']);
//				$cityMultiFBInfo['team_id'] = 0;
//				$baseList = M_MultiFB::getBaseList();
//				//单人副本进度,城市等级,军官数量,威望
//				$multiFBId = $teamInfo['multi_fb_id'];
//				list($passFBNo, $cityLevel, $heroNum, $renown, $maxPlayerNum) = explode(',', $baseList[$multiFBId]['join_rule']);
//
//				$pos = '';
//				for ($i = 2; $i <= 5; $i++) {
//					if (empty($teamInfo['pos_' . $i])) {
//						$pos = 'pos_' . $i;
//						break;
//					}
//				}
//
//				$isExist = isset($cityPos[$cityInfo['id']]) ? $cityPos[$cityInfo['id']] : false;
//
//				$err = '';
//				if ($cityMultiFBInfo['daily_free_times'] + $cityMultiFBInfo['left_buy_times'] < 1) {
//					$err = T_ErrNo::MULTI_FB_NO_TIMES;
//				} else if ($teamInfo['start_time'] != 0) {
//					$err = T_ErrNo::MULTI_FB_HAD_START;
//				} else if ($cityMultiFBInfo['team_id']) {
//					$err = T_ErrNo::MULTI_FB_CITY_EXIST;
//				} else if ($cityInfo['last_fb_no'] < $passFBNo) {
//					$err = T_ErrNo::MULTI_FB_NO_FBNO;
//				} else if ($cityInfo['level'] < $cityLevel) {
//					$err = T_ErrNo::MULTI_FB_NO_LEVEL;
//				} else if (count($heroIds) < $heroNum) {
//					$err = T_ErrNo::HERO_NOT_NUM;
//				} else if (!empty($renown) && $cityInfo['renown'] < $renown) {
//					$err = T_ErrNo::NO_ENOUGH_RENOWN;
//				} else if (!M_Hero::checkHeroStatus($cityInfo['id'], $heroIds)) {
//					$err = T_ErrNo::HERO_STATUS_ERR;
//				} else if ($objPlayerDef->Props()->isAvoidWar()) {
//					$err = T_ErrNo::AVOID_WAR_ENEMY;
//				} else if ($cityInfo['newbie'] == M_City::NEWBIE_GUARD_YES) {
//					$err = T_ErrNo::USER_DEF_IS_PROTECTED;
//				} else if (!empty($teamInfo['union_id']) && $cityInfo['union_id'] != $teamInfo['union_id']) {
//					$err = T_ErrNo::UNION_NOT_SAME;
//				} else if (empty($pos)) {
//					$err = T_ErrNo::MULTI_FB_NOT_POS;
//				} else if ($isExist) {
//					$err = T_ErrNo::MULTI_FB_EXIT_CITY;
//				}
//
//				if (empty($err)) {
//					$cityTmp = array(
//						'city_id' => $cityInfo['id'],
//						'nickname' => $cityInfo['nickname'],
//						'hero' => $heroIds,
//						'ploy' => array(M_MultiFB::ADDITION_ATK => 0, M_MultiFB::ADDITION_DEF => 0, M_MultiFB::ADDITION_HP => 0, M_MultiFB::ADDITION_CURE => 0),
//						'status' => 1,
//						'point' => 0,
//						'is_auto' => $isAuto,
//					);
//
//					$cityPos[$cityInfo['id']] = $pos;
//
//					$info = array(
//						'id' => $teamId,
//						'city_pos' => json_encode($cityPos),
//						$pos => json_encode($cityTmp),
//					);
//
//					$ret = M_MultiFB::setInfo($info, true);
//					if ($ret) {
//						M_Hero::changeHeroFlag($cityInfo['id'], $heroIds, T_Hero::FLAG_HOLD);
//
//						$upArr = array(
//							'city_id' => $cityInfo['id'],
//							'team_id' => $teamId
//						);
//						M_MultiFB::setCityInfo($upArr, true);
//
//						$errNo = '';
//					}
//				} else {
//					$errNo = $err;
//				}
//			} else {
//				$errNo = T_ErrNo::MULTI_FB_NO_EXIST;
//			}
//		}
//
//
//		return B_Common::result($errNo, $data);
//	}
//
//	/**
//	 * 退出队伍
//	 * @author huwei
//	 *
//	 */
//	public function ATeamQuit($teamId) {
//
//		$errNo = T_ErrNo::ERR_ACTION;
//		$data = array();
//
//		$cityId = M_Auth::myCid();
//		$objPlayer = new O_Player($cityId);
//		$cityInfo = $objPlayer->getCityBase();
//		if (!empty($cityInfo['id'])) {
//			$teamInfo = M_MultiFB::getInfo($teamId);
//			if (!empty($teamInfo['id'])) {
//				$cityPos = json_decode($teamInfo['city_pos'], true);
//				$cityMultiFBInfo = M_MultiFB::getCityInfo($cityInfo['id']);
//				$baseList = M_MultiFB::getBaseList();
//				$multiFBId = $teamInfo['multi_fb_id'];
//				//单人副本进度,城市等级,军官数量,威望
//				list($passFBNo, $cityLevel, $heroNum, $renown, $maxPlayerNum) = explode(',', $baseList[$multiFBId]['join_rule']);
//
//				$pos = isset($cityPos[$cityInfo['id']]) ? 'pos_' . $cityPos[$cityInfo['id']] : '';
//				$err = '';
//				if (empty($pos)) {
//					$err = T_ErrNo::MULTI_FB_NO_EXIST;
//				} else if ($teamInfo['start_time'] != 0) {
//					$err = T_ErrNo::MULTI_FB_HAD_START;
//				}
//
//				if (empty($err)) {
//					if ($pos == 'pos_1') { //队长解散队伍
//						for ($i = 1; $i <= 5; $i++) {
//							$tmpPos = 'pos_' . $i;
//							$tmpInfo = !empty($teamInfo[$tmpPos]) ? json_decode($teamInfo[$tmpPos], true) : array();
//							if (!empty($tmpInfo['city_id'])) {
//								M_MultiFB::setCityInfo(array('city_id' => $tmpInfo['city_id'], 'team_id' => 0), true);
//								M_Hero::changeHeroFlag($tmpInfo['city_id'], $tmpInfo['hero'], T_Hero::FLAG_FREE);
//							}
//						}
//						M_MultiFB::delInfo($teamId);
//					} else {
//						$tmpInfo = !empty($teamInfo[$pos]) ? json_decode($teamInfo[$pos], true) : array();
//
//						if (isset($cityPos[$cityInfo['id']])) {
//							unset($cityPos[$cityInfo['id']]);
//						}
//
//						if (!empty($tmpInfo['city_id'])) {
//							M_Hero::changeHeroFlag($tmpInfo['city_id'], $tmpInfo['hero'], T_Hero::FLAG_FREE);
//							M_MultiFB::setCityInfo(array('city_id' => $tmpInfo['city_id'], 'team_id' => 0), true);
//						}
//
//						M_MultiFB::setInfo(array('id' => $teamId, 'city_pos' => json_encode($cityPos), $pos => ''));
//					}
//
//
//					$errNo = '';
//				} else {
//					$errNo = $err;
//				}
//			}
//		}
//
//
//		return B_Common::result($errNo, $data);
//	}
//
//	/**
//	 * 开始战斗
//	 * @author huwei
//	 */
//	public function ATeamBattleStart($teamId) {
//
//		$errNo = T_ErrNo::ERR_ACTION;
//		$data = array();
//		$now = time();
//		$cityId = M_Auth::myCid();
//		$objPlayer = new O_Player($cityId);
//		$cityInfo = $objPlayer->getCityBase();
//		if (!empty($cityInfo['id'])) {
//			$teamInfo = M_MultiFB::getInfo($teamId);
//			if (!empty($teamInfo['pos_1'])) {
//				$cityPos = json_decode($teamInfo['city_pos'], true);
//				$multiFBId = $teamInfo['multi_fb_id'];
//				$baseList = M_MultiFB::getBaseList();
//				$baseMultiInfo = $baseList[$multiFBId];
//				//单人副本进度,城市等级,军官数量,威望
//				list($passFBNo, $cityLevel, $heroNum, $renown, $maxPlayerNum) = explode(',', $baseMultiInfo['join_rule']);
//
//				$err = '';
//				$posArr = json_decode($teamInfo['pos_1'], true);
//
//				if ($posArr['city_id'] != $cityInfo['id']) {
//					$err = T_ErrNo::MULTI_FB_NOT_HEAD;
//				} else if (count($cityPos) < $maxPlayerNum) {
//					$err = T_ErrNo::MULTI_FB_PLAYER_NUM_ERR;
//				} else if ($teamInfo['start_time'] != 0) {
//					$err = T_ErrNo::MULTI_FB_HAD_START;
//				}
//
//				if (empty($err)) {
//					$info = array(
//						'id' => $teamId,
//						'start_time' => $now,
//						'npc_def_line' => json_encode($baseMultiInfo['def_line']),
//					);
//
//					$holdDefLine = array();
//					for ($i = 1; $i <= 5; $i++) {
//						$tmpPos = 'pos_' . $i;
//						$tmpInfo = !empty($teamInfo[$tmpPos]) ? json_decode($teamInfo[$tmpPos], true) : array();
//						if (!empty($tmpInfo['city_id'])) {
//							$cityMultiFBInfo = M_MultiFB::getCityInfo($tmpInfo['city_id']);
//							$upArr = array('city_id' => $tmpInfo['city_id']);
//							if ($cityMultiFBInfo['daily_free_times'] > 0) {
//								$upArr['daily_free_times'] = $cityMultiFBInfo['daily_free_times'] - 1;
//							} else if ($cityMultiFBInfo['left_buy_times'] > 0) {
//								$upArr['left_buy_times'] = $cityMultiFBInfo['left_buy_times'] - 1;
//							}
//							M_MultiFB::setCityInfo($upArr, true);
//
//							$heroIds = explode(',', $tmpInfo['hero']);
//							$tmpCityInfo = M_City::getInfo($tmpInfo['city_id']);
//							$marchArr = array(
//								'atk_city_id' => $tmpInfo['city_id'],
//								'atk_nickname' => $tmpInfo['nickname'],
//								'def_city_id' => 0,
//								'def_nickname' => $baseMultiInfo['name'],
//								'action_type' => M_March::MARCH_ACTION_MULTI_FB,
//								'hero_list' => json_encode($heroIds),
//								'atk_pos' => $tmpCityInfo['pos_no'],
//								'def_pos' => M_MapWild::calcWildMapPosNoByXY(T_App::MAP_MULTI_FB, $multiFBId, 10),
//								'arrived_time' => $now,
//								'atk_ext' => json_encode(array($tmpCityInfo['face_id'], $tmpCityInfo['gender'])),
//								'def_ext' => json_encode(array(0, 0)),
//								'auto_fight' => $tmpInfo['is_auto'],
//								'create_at' => $now,
//								'flag' => M_March::MARCH_FLAG_HOLD,
//							);
//
//							$marchId = M_March::addMarchInfo($marchArr);
//
//							//M_March::syncOutForcesById($tmpInfo['city_id']);
//							M_March::syncMarch2Front($marchId);
//							if ($marchId) {
//								$holdDefLine['10'][$tmpInfo['city_id']] = $marchId;
//							}
//
//							$tmpInfo['march_id'] = $marchId;
//							$info[$tmpPos] = json_encode($tmpInfo);
//						}
//					}
//					$info['hold_def_line'] = json_encode($holdDefLine);
//
//					$ret = M_MultiFB::setInfo($info, true);
//
//					if ($ret) {
//
//						$errNo = '';
//					}
//				} else {
//					$errNo = $err;
//				}
//			}
//		}
//
//
//		return B_Common::result($errNo, $data);
//	}
//
//	/**
//	 * 战场加成属性
//	 * @author huwei
//	 *
//	 */
//	public function ABattleUpAddtion($teamId, $addAttr) {
//
//		$errNo = T_ErrNo::ERR_ACTION;
//		$data = array();
//		$now = time();
//		$cityId = M_Auth::myCid();
//		$objPlayer = new O_Player($cityId);
//		$cityInfo = $objPlayer->getCityBase();
//		if (!empty($cityInfo['id']) && in_array($addAttr, $addition)) {
//			$teamInfo = M_MultiFB::getInfo($teamId);
//			if ($teamInfo['id']) {
//				$cityPos = json_decode($teamInfo['city_pos'], true);
//				$pos = isset($cityPos[$cityInfo['id']]) ? $cityPos[$cityInfo['id']] : '';
//				$posInfo = !empty($teamInfo[$tmpPos]) ? json_decode($teamInfo[$tmpPos], true) : array();
//
//				if ($posInfo) {
//					$tmpLv = isset($posInfo['ploy'][$addAttr]) ? $posInfo['ploy'][$addAttr] : 0;
//					$baseMultiCostArr = M_Config::getVal('multi_fb_addition_cost');
//					$baseMulti = isset($baseMultiCostArr[$addAttr]) ? $baseMultiCostArr[$addAttr] : array();
//
//					if (!empty($baseMulti) && $tmpLv < count($baseMulti)) {
//						$tmpNum = $tmpLv + 1;
//						$posInfo['ploy'][$addAttr] = $tmpNum;
//						//array(1 => 加成,军饷; 2 => 加成,军饷; 3 => 加成,军饷;)
//						list($val, $costPrice) = $baseMulti[$tmpNum];
//						$err = '';
//						$ok = true;
//						if ($costPrice > 0) {
//							$ok = false;
//							if ($cityInfo['mil_pay'] < $costPrice) {
//								$err = T_ErrNo::NO_ENOUGH_MILIPAY;
//							} else {
//								$ok = $objPlayer->City()->decrCurrency(T_App::MILPAY, $costPrice, B_Log_Trade::E_BuyMultiFBAddition, $tmpNum);
//							}
//						}
//
//						if (!empty($err)) {
//							$errNo = $err;
//						} else if ($ok) {
//							$info = array(
//								'id' => $teamId,
//								$pos => json_encode($posInfo),
//							);
//
//							$ret = M_MultiFB::setInfo($info, true);
//							if ($ret) {
//
//								$errNo = '';
//							}
//						}
//					} else {
//						$errNo = T_ErrNo::ERR_ADDITION_LEVEL;
//					}
//				}
//			}
//
//		}
//
//
//		return B_Common::result($errNo, $data);
//	}
//
//	/**
//	 * 战场移动
//	 * @author huwei
//	 *
//	 */
//	public function ABattleMove($teamId = 0, $startPos = 0, $endPos = 0) {
//
//		$errNo = T_ErrNo::ERR_ACTION;
//		$data = array();
//		$now = time();
//		$cityId = M_Auth::myCid();
//		$objPlayer = new O_Player($cityId);
//		$cityInfo = $objPlayer->getCityBase();
//		if (!empty($cityInfo['id'])) {
//			$teamInfo = M_MultiFB::getInfo($teamId);
//			if ($teamInfo['id']) {
//				$cityId = $cityInfo['id'];
//				$startPos = strval($startPos);
//				$endPos = strval($endPos);
//				$cityPos = json_decode($teamInfo['city_pos'], true);
//				$holdDefLine = json_decode($teamInfo['hold_def_line'], true);
//				$pos = isset($cityPos[$cityInfo['id']]) ? $cityPos[$cityInfo['id']] : '';
//				$posInfo = !empty($teamInfo[$pos]) ? json_decode($teamInfo[$pos], true) : array();
//
//				if ($posInfo) {
//					$baseList = M_MultiFB::getBaseList();
//					//单人副本进度,城市等级,军官数量,威望
//					$multiFBId = $teamInfo['multi_fb_id'];
//					$baseDefLine = $baseList[$multiFBId]['def_line'];
//					$marchId = !empty($holdDefLine[$startPos][$cityId]) ? $holdDefLine[$startPos][$cityId] : 0;
//					$marchInfo = array();
//					if ($marchId) {
//						$marchInfo = M_March_Info::get($marchId);
//					}
//					$npcInfo = array();
//					if (!empty($baseDefLine[$endPos][0])) {
//						$npcInfo = M_NPC::getInfo($baseDefLine[$endPos][0]);
//					}
//
//					$diff = $startPos{0} - $endPos{0};
//					$err = '';
//					if (abs($diff) > 2) { //不能夸防线战斗
//						$err = T_ErrNo::MULTI_FB_NOT_DEF_LINE;
//					} else if (empty($marchInfo['id'])) { //不存在行军
//						$err = T_ErrNo::MULTI_FB_NOT_EXIT_MARCH;
//					} else if (empty($npcInfo['id'])) {
//						$err = T_ErrNo::MULTI_FB_NOT_EXIT_NPC;
//					}
//
//					if (empty($err)) {
//						unset($holdDefLine[$startPos][$cityId]);
//						$upInfo = array(
//							'id' => $teamId,
//							'hold_def_line' => json_encode($holdDefLine),
//						);
//						$bUp = M_MultiFB::setInfo($upInfo);
//						!$bUp && Logger::error(array(__METHOD__, 'err multifb set info', $upInfo));
//
//						$diff = max(1, $diff);
//						$arrivedTime = $now + $diff * T_App::ONE_MINUTE * M_MultiFB::MARCH_TIME;
//
//						$upData = array(
//							'id' => $marchId,
//							'def_nickname' => $npcInfo['nickname'],
//							'def_pos' => M_MapWild::calcWildMapPosNoByXY(T_App::MAP_MULTI_FB, $multiFBId, $endPos),
//							'flag' => M_March::MARCH_FLAG_MOVE,
//							'arrived_time' => $arrivedTime,
//							'create_at' => $now,
//							'start_pos_ext' => M_MapWild::calcWildMapPosNoByXY(T_App::MAP_MULTI_FB, $multiFBId, $startPos),
//						);
//						//更新行军记录
//						$ret = M_March_Info::set($upData);
//						if ($ret) { //设置英雄状态 [驻守 => 移动]
//							$bFalg = M_Hero::changeHeroFlag($marchInfo['atk_city_id'], json_decode($marchInfo['hero_list'], true), T_Hero::FLAG_MOVE);
//							if ($bFalg) {
//								$syncMarchData = array(
//									$marchInfo['id'] => array(
//										'AttCityId' => $marchInfo['atk_city_id'],
//										'DefCityId' => 0,
//										'DefCityNickName' => $npcInfo['nickname'],
//										'AttPos' => M_MapWild::calcWildMapPosXYByNo($marchInfo['atk_pos']),
//										'DefPos' => M_MapWild::calcWildMapPosXYByNo($upData['def_pos']),
//										'ArrivedTime' => $arrivedTime,
//										'Flag' => M_March::MARCH_FLAG_MOVE,
//										'CampStartPos' => array(T_App::MAP_MULTI_FB, $multiFBId, $startPos))
//								);
//
//								M_Sync::addQueue($marchInfo['atk_city_id'], M_Sync::KEY_MARCH_DATA, $syncMarchData);
//
//								//更新新的基地的数据
//								$atkLinePos = M_MapWild::calcWildMapPosNoByXY(T_App::MAP_MULTI_FB, $multiFBId, $endPos);
//								$obj_ml = new M_March_List($atkLinePos);
//								$bAdd = $obj_ml->add($marchId);
//								!$bAdd && Logger::error(array(__METHOD__, 'Fail for March_List->add', array($atkLinePos, $marchId)));
//
//
//								$errNo = '';
//							} else {
//								Logger::error(array(__METHOD__, 'Fail for M_Hero::changeHeroFlag', array($marchInfo['atk_city_id'], json_decode($marchInfo['hero_list'], true), T_Hero::FLAG_MOVE)));
//							}
//						} else {
//							Logger::error(array(__METHOD__, 'Fail for M_March_Info::set', $upData));
//						}
//					} else {
//						$errNo = $err;
//					}
//				}
//			}
//		}
//
//
//		return B_Common::result($errNo, $data);
//	}
//}
//
//?>