<?php

class M_March_Action {
	/** 侦察成功率附加值 */
	const SUCC_RATE_ADD = 5;
	/** 侦察成功率最大值 */
	const SUCC_RATE_MAX = 95;
	/** 侦察成功率最小值 */
	const SUCC_RATE_MIN = 5;
	/** 侦察失败死兵参考值 */
	const DEAD_ARMY_NUM = 100;
	/** 战斗失败军官死亡概率 */
	const FAIL_DEAD_RATE = 15;
	/** 据点战斗失败军官死亡概率 */
	const FAIL_DEAD_RATE_CAMP = 50;
	/** 空袭战斗最多回合 */
	const BOMB_MAX_BOUT = 8;
	/** 进攻方胜利的空袭成功率 */
	const BOMB_SUCC_RATE = 95;
	/** 进攻方失败的空袭成功率 */
	const BOMB_FAIL_RATE = 5;

	/** 军队行动类型对应处理函数  */
	static $warAction = array(
		M_March::MARCH_ACTION_ATT         => 'att',
		M_March::MARCH_ACTION_CAMP        => 'camp',
		M_March::MARCH_ACTION_HOLD        => 'hold',
		M_March::MARCH_ACTION_BOMB        => 'bomb',
		M_March::MARCH_ACTION_SCOUT       => 'scout',
		M_March::MARCH_ACTION_HELP        => 'help',
		M_March::MARCH_ACTION_BACK        => 'back',
		M_March::MARCH_ACTION_CITY        => 'occupiedCity', //占领城市
		M_March::MARCH_ACTION_RESCUE_CITY => 'rescueCity', //解救占领城市
		M_March::MARCH_ACTION_HOLD_CITY   => 'holdCity', ////驻守占领城市
		M_March::MARCH_ACTION_MULTI_FB    => 'mulitFB'
		//驻守
	);

	static $needWait = array(
		M_March::MARCH_ACTION_ATT         => 'att',
		M_March::MARCH_ACTION_CAMP        => 'camp',
		M_March::MARCH_ACTION_HOLD        => 'hold',
		M_March::MARCH_ACTION_CITY        => 'occupiedCity', //占领城市
		M_March::MARCH_ACTION_RESCUE_CITY => 'rescueCity', //解救
		M_March::MARCH_ACTION_MULTI_FB    => 'mulitFB'
	);


	/**
	 * 进攻 到达后处理
	 * @param array $marchInfo
	 */
	static public function att($marchInfo) {
		$result = false;
		//$toBattle = true;
		if (!empty($marchInfo['id'])) {
			$result = self::toCityBattle($marchInfo);
		}

		return $result;
	}

	/**
	 * 占领 到达后处理
	 * @param array $marchInfo
	 */
	static public function hold($marchInfo) {
		$result = false;
		//$toBattle = true;
		if (!empty($marchInfo['id'])) {
			$result = self::toHoldBattle($marchInfo);
		}

		return $result;
	}

	/**
	 * 驻军据点
	 * @param array $marchInfo
	 */
	static public function camp($marchInfo) {
		$result = false;
		if (!empty($marchInfo['id'])) {
			$result = self::toCampBattle($marchInfo);
		}

		return $result;

	}

	/**
	 * 侦察 到达后处理
	 * @param array $marchInfo
	 */
	static public function scout($marchInfo) {
		$flag             = T_App::FAIL;
		$atkReportContent = array();
		$reportContent    = array();
		$isDie            = false;
		$arr_heroId       = json_decode($marchInfo['hero_list'], true);
		$cityHeroInfo     = M_Hero::getHeroInfo($arr_heroId[0]); //城市军官信息
		$weaponInfo       = M_Weapon::baseInfo($cityHeroInfo['weapon_id']); //武器基础信息
		$armyInfo         = M_Army::baseInfo($cityHeroInfo['army_id']); //兵种基础信息
		if (M_War::MARCH_SCOUT == $weaponInfo['march_type']) //属于 侦察系
		{
			$army_num = $cityHeroInfo['army_num']; //侦察兵/机数量

			$atkObjCity   = new O_Player($marchInfo['atk_city_id']);
			$attBuildList = $atkObjCity->getObjBuild()->get();

			$defObjCity   = new O_Player($marchInfo['def_city_id']);
			$defBuildList = $defObjCity->getObjBuild()->get();

			$attRadarLv  = isset($attBuildList[M_Build::ID_RADAR]) ? current($attBuildList[M_Build::ID_RADAR]) : 0;
			$defRadarLv  = isset($defBuildList[M_Build::ID_RADAR]) ? current($defBuildList[M_Build::ID_RADAR]) : 0;
			$radarDiff   = max($attRadarLv - $defRadarLv, 0); //雷达站相差等级
			$tmpFailRate = M_Formula::calcTmpFailRate($army_num, $radarDiff, $defRadarLv); //初步失败率
			if (M_Army::ID_FOOT == $cityHeroInfo['army_id']) //侦察步兵
			{
				$tmpFailRate -= self::SUCC_RATE_ADD; //失败率减5
			}
			$failRate = ($tmpFailRate > self::SUCC_RATE_MAX) ? self::SUCC_RATE_MAX : max($tmpFailRate, self::SUCC_RATE_MIN); //修正后最终失败率
			if (B_Utils::odds($failRate)) //侦察失败
			{
				$objPlayer = new O_Player($marchInfo['atk_city_id']);

				$baseArmy = M_Army::baseInfo($cityHeroInfo['army_id']); //兵种基础信息
				$dieNum   = 0;
				if ($army_num > self::DEAD_ARMY_NUM - 1) {
					$dieNum    = floor($army_num / 2);
					$diePeople = $dieNum * $baseArmy['cost_people'];
					$objPlayer->City()->diedPeopleToFreePeople($diePeople); //死一半兵转成人口
				} else {
					$dieNum    = $army_num;
					$diePeople = $dieNum * $baseArmy['cost_people'];

					$objPlayer->City()->diedPeopleToFreePeople($diePeople); //全军覆没(兵全死转人口，军官概率死亡，返回？)
					if (B_Utils::odds(self::FAIL_DEAD_RATE)) {
						$isDie = true;
						M_Hero::changeHeroFlagKilled($marchInfo['atk_city_id'], $arr_heroId[0], M_Formula::heroRelifeTime($cityHeroInfo)); //军官死亡
					}
				}
				$objPlayer->save();

				$heroId                    = $cityHeroInfo['id'];
				$atkReportContent[$heroId] = array(
					'Nickname' => $cityHeroInfo['nickname'],
					'FaceId'   => $cityHeroInfo['face_id'],
					'Gender'   => $cityHeroInfo['gender'],
					'Quality'  => $cityHeroInfo['quality'],
					'Level'    => $cityHeroInfo['level'],
					'WeaponId' => $cityHeroInfo['weapon_id'],
					'ArmyId'   => $cityHeroInfo['army_id'],
					'IsDie'    => $isDie ? 1 : 0,
					'DieNum'   => $dieNum,
				);
				//报告内容
				$reportContent = array(
					'Atk' => $atkReportContent,
				);
			} else {
				//侦察成功
				$flag       = T_App::SUCC;
				$tmpInfoVal = M_Formula::calcTmpInfoVal($army_num, $radarDiff); //初步情报值
				$infoVal    = max($tmpInfoVal, 1); //侦察情报值
				//军队带着侦察结果返回
				$reportContent = M_War::getScoutDataByInfoval($infoVal, $marchInfo['def_city_id']);
			}
		}

		//添加侦察战报
		list($atkFaceId, $atkGender) = json_decode($marchInfo['atk_ext']);
		list($defFaceId, $defGender) = json_decode($marchInfo['def_ext']);

		$initData = array(
			M_Battle_Calc::REPORT_TYPE_SCOUT,
			$marchInfo['atk_city_id'],
			$marchInfo['def_city_id'],
			array($marchInfo['atk_nickname'], $atkFaceId, $marchInfo['atk_pos'], $atkGender),
			array($marchInfo['def_nickname'], $defFaceId, $marchInfo['def_pos'], $defGender),
			0,
		);

		$tmpReportId = M_WarReport::initWarReport($initData);
		if ($tmpReportId) {
			$reportData = array(
				'id'             => $tmpReportId,
				'content'        => $reportContent,
				'reward'         => array(),
				'is_succ'        => $flag,
				'replay_address' => '',
				'create_at'      => time(),
			);

			//添加侦察战报
			$bAddReort = M_WarReport::addWarReport($initData, $reportData);
		}

		if ($isDie) {
			M_March::delMarchInfo($marchInfo['id']); //删除行军记录
		} else {
			M_March::setMarchBack($marchInfo['id']); //军官返回
		}

		return array($flag, $reportContent);
	}


	/**
	 * 空袭 到达后处理
	 * @param array $marchInfo
	 */
	static public function bomb($marchInfo) {

	}

	/**
	 * 增援 到达后处理
	 * @param array $marchInfo
	 */
	static public function help($marchInfo) {

	}

	/**
	 * 返回 到达后处理
	 * @param array $marchInfo
	 * @return bool
	 */
	static public function back($marchInfo) {
		//@todo   修改战斗结束奖励数据结构
		$awardArr  = json_decode($marchInfo['award'], true);
		$objPlayer = new O_Player($marchInfo['atk_city_id']);
		$objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Prop);
		$objPlayer->save();
		//行军返回后自动补兵
		$ret      = M_March::delMarchInfo($marchInfo['id']); //删除行军记录
		$heroList = json_decode($marchInfo['hero_list'], true);
		M_Hero::fillHeroArmyNumByHeroId($marchInfo['atk_city_id'], $heroList);

		return $ret;
	}

	/**
	 * 占领城市系统
	 * @author duhuihui
	 * @param array $marchInfo
	 * @return bool
	 */
	static public function occupiedCity($marchInfo) //占领城市
	{
		$result = false;
		if (!empty($marchInfo['id'])) {
			$result = self::toOccupiedCityBattle($marchInfo);
		}

		return $result;

	}

	/**
	 * 解救城市系统
	 * @author duhuihui
	 * @param array $marchInfo
	 * @return bool
	 */
	static public function rescueCity($marchInfo) //解救城市
	{
		$result = false;
		if (!empty($marchInfo['id'])) {
			$result = self::toRescueCityBattle($marchInfo);
		}

		return $result;
	}

	/**
	 * 驻守到达城市后
	 * @param array $marchInfo
	 */
	static public function holdCity($marchInfo) {
		$result = false;
		//$toBattle = true;
		if (!empty($marchInfo['id'])) {
			$result = self::toHoldCityBattle($marchInfo);
		}

		return $result;
	}

	/**
	 * 多人副本到达后
	 * @param array $marchInfo
	 */
	static public function multiFB($marchInfo) {
		if (empty($marchInfo['id'])) {
			return false;
		}

		$needBack  = true;
		$rewardArr = array();
		$now       = time();
		$battleId  = '';
		$data      = M_War::buildMultiFBWarBattleData($marchInfo);

		if (!empty($data['atkHero'])) {
			$atkCityId = $data['atkData'][0];
			$defCityId = $data['defData'][0];

			if (!empty($data['defHero'])) {
				$battleId = M_War::insertWarBattle($data); //插入战斗队列
				if (!empty($battleId)) {
					$upData = array(
						'id'        => $marchInfo['id'],
						'flag'      => M_March::MARCH_FLAG_BATTLE,
						'battle_id' => $battleId,
						'update_at' => time(),
					);

					$ret = M_March_Info::set($upData);

					$syncMarchData = array(
						$marchInfo['id'] => array(
							'AttCityId' => $marchInfo['atk_city_id'],
							'DefCityId' => $marchInfo['def_city_id'],
							'AttPos'    => M_MapWild::calcWildMapPosXYByNo($marchInfo['atk_pos']),
							'DefPos'    => M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']),
							'BattleId'  => $battleId,
							'Flag'      => M_March::MARCH_FLAG_BATTLE)
					);

					M_Sync::addQueue($marchInfo['atk_city_id'], M_Sync::KEY_MARCH_DATA, $syncMarchData);

					$needBack = false;
				}
			} else {
				self::_buildEmptyDefReport($data);
				$needBack = self::_multiFBBattle($atkCityId, $defCityId, $marchInfo);
			}
		}

		//是否遣返行军
		if ($needBack) {
			M_March::setMarchBack($marchInfo['id']);
		}

		return $battleId;
	}

	/**
	 * 多人副本战斗
	 * @author huwei
	 * @param int $atkCityId
	 * @param int $defCityId
	 * @param array $marchInfo
	 * @return bool
	 */
	static private function _multiFBBattle($atkCityId, $defCityId, $marchInfo) {
		$needBack = true;
		$defPosNo = $marchInfo['def_pos'];
		$result   = array();
		list($type, $multiFBId, $defLineNo) = M_MapWild::calcWildMapPosXYByNo($defPosNo);

		$baseMultiFB = M_MultiFB::getBaseList();
		if (!isset($baseMultiFB[$multiFBId]['def_line'][$defLineNo])) {
			return $needBack;
		}

		list($defNpcId, $warMapNo, $point) = $baseMultiFB[$multiFBId]['def_line'][$defLineNo];

		$atkMultiFBInfo = M_MultiFB::getCityInfo($atkCityId);
		if (!$atkMultiFBInfo['team_id']) {
			return $needBack;
		}

		$teamInfo = M_MultiFB::getInfo($atkMultiFBInfo['team_id']);
		if (!$teamInfo['id']) {
			return $needBack;
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
			$needBack = false;
		}
		return $needBack;
	}


	/**
	 * 构建占领城市战斗数据
	 * @author duhuihui
	 * @param array $marchInfo
	 */
	static public function toOccupiedCityBattle($marchInfo) {
		if (empty($marchInfo['id'])) {
			return false;
		}

		$needBack  = true;
		$rewardArr = array();
		$now       = time();
		$battleId  = '';

		$data = M_War::buildOccupiedWarBattleData($marchInfo);

		if (!empty($data)) {
			$atkCityId   = $data['atkData'][0];
			$defCityId   = $data['defData'][0];
			$atkCityInfo = M_City::getInfo($atkCityId);
			$defCityInfo = M_City::getInfo($defCityId);
			if (!empty($atkCityInfo) &&
				!empty($defCityInfo) &&
				(empty($defCityInfo['union_id']) || empty($atkCityInfo['union_id']) || $defCityInfo['union_id'] != $atkCityInfo['union_id'])
			) { //如果联盟 为自己联盟
				if (!empty($data['defHero'])) {
					$battleId = M_War::insertWarBattle($data); //插入战斗队列

					//Logger::debug(array(__METHOD__, $marchInfo, $battleId));

					if (!empty($battleId)) {
						$defColonyInfo = M_ColonyCity::getInfo($marchInfo['def_city_id']); //被占领城市信息
						if (!empty($defColonyInfo['atk_march_id'])) {
							$info   = M_March_Info::get($defColonyInfo['atk_march_id']);
							$upData = array(
								'id'        => $info['id'],
								'flag'      => M_March::MARCH_FLAG_BATTLE,
								'battle_id' => $battleId,
								'update_at' => time(),
							);

							$ret = M_March_Info::set($upData);

							$syncMarchData = array(
								$info['id'] => array(
									'AttCityId' => $info['atk_city_id'],
									'DefCityId' => $info['def_city_id'],
									'AttPos'    => M_MapWild::calcWildMapPosXYByNo($info['atk_pos']),
									'DefPos'    => M_MapWild::calcWildMapPosXYByNo($info['def_pos']),
									'BattleId'  => $battleId,
									'Flag'      => M_March::MARCH_FLAG_BATTLE)
							);

							M_Sync::addQueue($info['atk_city_id'], M_Sync::KEY_MARCH_DATA, $syncMarchData);
						}
						$needBack = false;
					}
				} else {
					self::_buildEmptyDefReport($data);
					$needBack = self::_occupiedCityBattle($atkCityId, $defCityId, $marchInfo);
				}
			}
		}

		//是否遣返行军
		if ($needBack) {
			M_March::setMarchBack($marchInfo['id']);
		}

		return $battleId;
	}

	/**
	 * 构建解救城市战斗数据
	 * @author duhuihui
	 * @param array $marchInfo
	 */
	static public function toRescueCityBattle($marchInfo) { //自己解救的行军时间固定1分钟，别的玩家解救的话有行军时间
		if (empty($marchInfo['id'])) {
			return false;
		}

		$needBack  = true;
		$rewardArr = array();
		$battleId  = '';
		$data      = M_War::buildRescueWarBattleData($marchInfo); //构建战斗数据
		if (!empty($data)) {
			$atkCityId   = $data['atkData'][0];
			$defCityId   = $data['defData'][0];
			$atkCityInfo = M_City::getInfo($atkCityId); //$atkCityInfo['pos_no']
			$mapRow      = M_MapWild::getWildMapInfo($marchInfo['def_pos']);
			$defCityInfo = M_City::getInfo($mapRow['city_id']); //被解救的城市

			if (!empty($atkCityInfo) &&
				!empty($defCityInfo) &&
				$defCityInfo['union_id'] == $atkCityInfo['union_id']
			) {
				//插入战斗队列
				if (!empty($data['defHero'])) {
					$wildInfo = M_ColonyCity::getNoByPosNo($defCityId, $marchInfo['def_pos']);
					if (!empty($wildInfo)) {
						$no         = $wildInfo['no'];
						$msRow[$no] = array(
							'MarchType' => 0,
						);

						M_Sync::addQueue($defCityId, M_Sync::KEY_CITY_COLONY, $msRow); //同步属地数据
					}
					$battleId = M_War::insertWarBattle($data);
					if (!empty($battleId)) {
						$needBack = false;
					}
				} else {
					self::_buildEmptyDefReport($data);
					$needBack = self::_rescueCityBattle($atkCityId, $defCityId, $marchInfo);
					//	$needBack && M_March_Hold::del($defCityInfo['pos_no']);
				}
			}
		}

		//是否遣返行军
		if ($needBack) {
			M_March::setMarchBack($marchInfo['id']);
		}

		return $battleId;
	}

	/**
	 * 构建驻守城市战斗数据
	 * @author duhuihui
	 * @param array $marchInfo
	 */
	static public function toHoldCityBattle($marchInfo) {
		if (empty($marchInfo['id'])) {
			return false;
		}

		$needBack      = true;
		$ret           = false;
		$rewardArr     = array();
		$now           = time();
		$atkCityId     = $marchInfo['atk_city_id'];
		$defCityId     = $marchInfo['def_city_id'];
		$mapRow        = M_MapWild::getWildMapInfo($marchInfo['def_pos']);
		$defColonyInfo = M_ColonyCity::getInfo($mapRow['city_id']);
		if (isset($defColonyInfo['atk_city_id']) &&
			$defColonyInfo['atk_city_id'] == $atkCityId
		) {
			$ret      = self::_holdCityBattle($atkCityId, $defCityId, $marchInfo);
			$needBack = false;
		} else if (empty($defColonyInfo['atk_city_id']) || $defColonyInfo['atk_city_id'] != $atkCityId) {
			$needBack = true;
		}
		//是否遣返行军
		if ($needBack) {
			M_March::setMarchBack($marchInfo['id']);
		}

		return $ret;
	}


	/**
	 * 排队等待系统
	 * @author huwei
	 * @param array $marchInfo
	 * @return bool
	 */
	static public function toWait($marchInfo) {
		$isBack = false;
		//先进入 排队系统
		if (M_March::MARCH_FLAG_MOVE == $marchInfo['flag']) {
			//防守方敌情列表
			$mw       = new M_March_Wait($marchInfo['def_pos']);
			$marchIds = $mw->get();

			$waitNum = count($marchIds);
			$maxNum  = ($marchInfo['action_type'] == M_March::MARCH_ACTION_CAMP) ? M_Campaign::MAX_QUEUE_NUM : M_War::MAX_CITY_WAIT_NUM_MARCH;

			//Logger::debug(array(__METHOD__,"defpos#{$marchInfo['def_pos']};pvpWaitNum#{$waitNum}; max#{$maxNum}", $marchInfo));

			if ($waitNum >= $maxNum) {
				//等待队列已满遣返行军
				$bUp  = M_March::setMarchBack($marchInfo['id']);
				$info = M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']);

				//发送消息邮件
				if ($marchInfo['action_type'] == M_March::MARCH_ACTION_CAMP) {
					$campBaseList = M_Base::campaignAll();
					$campBaseInfo = $campBaseList[$info[1]];

					$content = array(T_Lang::C_CAMPAIGN_WAIT_QUEUE, $campBaseInfo['title'], $marchInfo['def_nickname']);
				} else {
					$content = array(T_Lang::C_BATTLE_WAIT_QUEUE, $marchInfo['def_nickname'], array(T_Lang::$Map[$info[0]]), $info[1] . ',' . $info[2]);
				}

				if ($bUp) {
					$isBack = true;
					M_Message::sendSysMessage($marchInfo['atk_city_id'], json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content));
				}
			} else {
				//把当前行军加入到等待队列中...
				$bUpMarch = M_March::setMarchWait($marchInfo);
			}

			M_War::setNextBattle($marchInfo['def_pos']);
		}

		return $isBack;
	}


	/**
	 * 构建城市战斗数据
	 * @author duhuihui
	 * @param array $marchInfo
	 * @return bool
	 */
	static public function toCityBattle($marchInfo) {
		$needBack  = true;
		$rewardArr = array();
		$now       = time();
		//触发战斗
		//构建战斗数据
		$data     = M_War::buildNormalWarBattleData($marchInfo);
		$battleId = '';
		if (!empty($data)) {
			//插入战斗队列
			$atkCityId = $data['atkData'][0];
			$defCityId = $data['defData'][0];
			if (!empty($data['defHero'])) {
				$battleId = M_War::insertWarBattle($data);
				if (!empty($battleId)) {
					$needBack = false;
				}
			} else {
				//无防守英雄 直接结束战斗
				$needCalcRes = false;
				if ($data['battleType'] == M_War::BATTLE_TYPE_CITY &&
					$defCityId > 0
				) {
					//攻击城市才有资源掠夺
					$needCalcRes = true;
				}
				$rewardArr = self::_buildEmptyDefReport($data, $needCalcRes);
			}
		}

		//如果无法生成战斗ID 遣返行军
		if ($needBack) {
			M_March::setMarchBack($marchInfo['id'], $rewardArr);
		}

		return $battleId;
	}//

	/**
	 * 构建野地占领战斗数据
	 * @param array $marchInfo
	 */
	static public function toHoldBattle($marchInfo) {
		$needBack = true;
		$battleId = 0;
		$now      = time();
		//触发战斗
		//构建战斗数据
		$data = M_War::buildNormalWarBattleData($marchInfo);

		if (!empty($data)) {
			//插入战斗队列
			if (!empty($data['defHero'])) {
				$battleId = M_War::insertWarBattle($data);
				if (!empty($battleId)) {
					$needBack = false;
				}
			} else { //无防守英雄 直接结束战斗
				self::_buildEmptyDefReport($data);
				$needBack = self::_wildBattle($data['atkData'][0], $data['defData'][0], $marchInfo);
			}
		}

		//如果无法生成战斗ID 遣返行军
		if ($needBack) {
			M_March::setMarchBack($marchInfo['id']);
		}

		return $battleId;
	}

	/**
	 * 构建据点战斗数据
	 * @param array $marchInfo
	 */
	static public function toCampBattle($marchInfo) {
		if (empty($marchInfo['id'])) {
			return false;
		}

		$needBack = true;
		$battleId = '';
		list($type, $campId, $defLineNo) = M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']);

		$campBaseList = M_Base::campaignAll();

		if ($type == T_App::MAP_CAMPAIGN &&
			!empty($defLineNo) &&
			!empty($campId) &&
			isset($campBaseList[$campId])
		) {
			$campInfo = M_Campaign::getInfo($campId);
			//据点编号
			$defLineNo      = strval($defLineNo);
			$defLineNoField = 'no_' . $defLineNo;

			$campBaseInfo = $campBaseList[$campId];

			$sysStartTime = strtotime($campBaseInfo['open_start_time']);
			$sysEndTime   = strtotime($campBaseInfo['open_end_time']);

			$curWeek = date('w');
			$curTime = time();
			if ((M_Campaign::$campOpenWeek[$curWeek] & $campBaseInfo['open_week']) == 0 ||
				$curTime < $sysStartTime ||
				$curTime > $sysEndTime
			) { //未开放遣返部队
				$needBack = true;
			} else {
				if (isset($campInfo[$defLineNoField])) {
					list($defUnionId, $marchIds) = json_decode($campInfo[$defLineNoField], true);
					$atkCityInfo = M_City::getInfo($marchInfo['atk_city_id']);
					if (!empty($defUnionId) && $defUnionId == $atkCityInfo['union_id']) { //如果联盟 为自己联盟
						$needHold = false;
						if (empty($marchIds[0])) {
							$needHold    = true;
							$marchIds[0] = $marchInfo['id'];
						} else if (empty($marchIds[1])) {
							$needHold    = true;
							$marchIds[1] = $marchInfo['id'];
						} else if (empty($marchIds[2])) {
							$needHold    = true;
							$marchIds[2] = $marchInfo['id'];
						}
						Logger::dev("是否驻军" . json_encode($marchIds) . ':======:' . json_encode($needHold));
						if ($needHold) { //则驻军
							$upInfo = array(
								$defLineNoField => json_encode(array($defUnionId, $marchIds)),
							);

							Logger::dev("开始驻军#{$campId}:" . json_encode($upInfo));
							$ret = M_Campaign::setInfo($campId, $upInfo);
							if ($ret) {
								Logger::dev("setMarchHold:{$marchInfo['id']}" . json_encode($marchIds));
								M_March::setMarchHold($marchInfo);
								$needBack = false;
							}
						} else { //满了  撤回
							list($npcId, $warBgNo) = explode('|', $campBaseInfo[$defLineNoField]);
							$npcInfo = M_NPC::getInfo($npcId);
							$content = array(T_Lang::C_CAMP_HOLD_FULL, $campBaseInfo['title'], $npcInfo['nickname']);
							M_Message::sendSysMessage($marchInfo['atk_city_id'], json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content));
							$needBack = true;
						}
					} else { //不属于自己同盟的据点基地
						$data = M_War::buildCampWarBattleData($marchInfo);

						if (!empty($data)) {
							//插入战斗队列
							if (!empty($data['defHero'])) {
								$battleId = M_War::insertWarBattle($data);
								if (!empty($battleId)) {
									$needBack = false;
								}
							} else { //无防守英雄 直接结束战斗
								self::_buildEmptyDefReport($data);
								$needBack = self::_campBattle($data['atkData'][0], $data['defData'][0], $marchInfo);
							}
						}
					}
				}
			}
		}

		//是否遣返行军
		if ($needBack) {
			M_March::setMarchBack($marchInfo['id']);
		}

		return $battleId;
	}

	/**
	 * 地图野地战斗
	 * @author huwei
	 * @param int $atkCityId
	 * @param int $defCityId
	 * @param array $marchInfo
	 * @return bool
	 */
	static private function _wildBattle($atkCityId, $defCityId, $marchInfo) {
		$needBack = true;
		$mapRow   = M_MapWild::getWildMapInfo($marchInfo['def_pos']);
		$npcInfo  = M_NPC::getInfo($mapRow['npc_id']);
		list($z, $x, $y) = M_MapWild::calcWildMapPosXYByNo($mapRow['pos_no']);

		if ($defCityId > 0) {
			$objPlayerDef = new O_Player($defCityId);
			//删除敌情列表中的行军Id
			$obj_ml = new M_March_List($mapRow['pos_no']);
			$obj_ml->del($marchInfo['id']);

			M_March::syncDelMarchBack($marchInfo['id'], $defCityId);

			if (!empty($mapRow['march_id'])) {
				//撤回已占领的军队
				M_March::setMarchBack($marchInfo['id']);
			}

			//属地删除
			$delUp = $objPlayerDef->ColonyNpc()->del($marchInfo['def_pos']);
			if ($delUp) {
				//发送消息邮件
				$content = array(T_Lang::C_WILD_NPC_LOSE, $npcInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y, date('Y'), date('m'), date('d'), date('h'), date('i'), $marchInfo['atk_nickname']);
				M_Message::sendSysMessage($mapRow['city_id'], json_encode(array(T_Lang::T_WILD_NPC_TIP)), json_encode($content));
			}

			//占领成功 如果有防御方 则需要删除这个 防御方 敌情信息
			//获取防御方的敌情信息
			Logger::dev("OldDefCityId#" . $defCityId . "DefPos#{$marchInfo['def_pos']}");
			$marchArr = M_March::getMarchList($defCityId, M_War::MARCH_OWN_DEF);
			Logger::dev("MarchList#" . json_encode($marchArr));
			foreach ($marchArr as $marchId => $val) {
				if ($val['def_pos'] == $marchInfo['def_pos']) {
					//更新其他进攻方的行军数据中的 防御城市ID
					$marchData = array('id' => $marchId, 'def_city_id' => $atkCityId);
					$ret       = M_March_Info::set($marchData);

					//删除当前防守方的敌情
					Logger::dev("2delSyncMarchData#{$marchId}#{$defCityId}");
					M_March::syncDelMarchBack($marchId, $defCityId);
				}
			}
		}

		//属地添加
		$addFlag = $objPlayerDef->ColonyNpc()->add($marchInfo);
		if ($addFlag) {
			//发送消息邮件
			$content = array(T_Lang::C_WILD_NPC_HOLD_SUCC, $npcInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
			M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_NPC_TIP)), json_encode($content));

			$needBack = false;

			$holdArr = array(
				M_NPC::CITY_NPC_FOOT  => 'hold_npc_type_1',
				M_NPC::CITY_NPC_GUN   => 'hold_npc_type_2',
				M_NPC::CITY_NPC_ARMOR => 'hold_npc_type_3',
				M_NPC::CITY_NPC_AIR   => 'hold_npc_type_4',
				M_NPC::RES_NPC_FOOD   => '',
				M_NPC::RES_NPC_GOLD   => '',
				M_NPC::RES_NPC_OIL    => '',
			);

			$npcType    = isset($npcInfo['type']) ? $npcInfo['type'] : 0;
			$dailyAward = M_Config::getVal('active_award');
			list($IsOpen, $activeField) = M_Task::getHoldNpcActiveStaus($atkCityId, $dailyAward);
			if ($IsOpen == 1 || $IsOpen == 2) {
				M_Task::active($atkCityId, $holdArr[$npcInfo['type']]); //更新学院活动的完成状态
			} elseif ($IsOpen == 3 || $IsOpen == 4) {
				if ($mapRow['city_id'] > 0) {
					M_Task::active($atkCityId, $holdArr[$npcInfo['type']]); //更新学院活动的完成状态
				}
			}
		} else {
			//发送消息邮件
			$content = array(T_Lang::C_WILD_NPC_HOLD_FULL, $npcInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
			M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_NPC_TIP)), json_encode($content));
		}

		M_MapWild::syncWildMapBlockCache($marchInfo['def_pos']);
		return $needBack;
	}

	/**
	 * 更新据点战斗所属联盟
	 * @author huwei
	 * @param int $atkCityId
	 * @param int $defCityId
	 * @param array $marchInfo
	 * @return bool
	 */
	static private function _campBattle($atkCityId, $defCityId, $marchInfo) {
		//无防御联盟的情况下 为空
		$needBack = true;
		list($type, $campId, $defLineNo) = M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']);
		$campInfo = M_Campaign::getInfo($campId);

		//据点编号
		$defLineNo      = strval($defLineNo);
		$defLineNoField = 'no_' . $defLineNo;
		list($defUnionId, $marchIds) = json_decode($campInfo[$defLineNoField], true);

		$cityInfo = M_City::getInfo($atkCityId);
		$marchIds = array($marchInfo['id'], 0, 0);
		$upInfo   = array(
			$defLineNoField => json_encode(array($cityInfo['union_id'], $marchIds))
		);
		$bUp      = M_Campaign::setInfo($campId, $upInfo);
		if ($bUp) {
			//设置行军状态为驻守
			M_March::setMarchHold($marchInfo);
			$needBack = false;
		}

		return $needBack;
	}

	/**
	 * 更新占领城市
	 * @author duhuihui
	 * @param int $atkCityId
	 * @param int $defCityId
	 * @param array $marchInfo
	 * @return bool
	 */
	static private function _occupiedCityBattle($atkCityId, $defCityId, $marchInfo) {
		$now      = time();
		$needBack = true;
		$mapRow   = M_MapWild::getWildMapInfo($marchInfo['def_pos']); //目的地地图
		$cityInfo = M_City::getInfo($mapRow['city_id']);
		list($z, $x, $y) = M_MapWild::calcWildMapPosXYByNo($mapRow['pos_no']);
		$atkmapRow = M_MapWild::getWildMapInfo($marchInfo['atk_pos']); //目的地地图
		list($z1, $x1, $y1) = M_MapWild::calcWildMapPosXYByNo($atkmapRow['pos_no']);
		$defColonyInfo = M_ColonyCity::getInfo($mapRow['city_id']); //属地信息

		if ($defColonyInfo['atk_city_id'] > 0) //之前被占领
		{ //删除敌情列表中的行军Id
			if (!empty($defColonyInfo['atk_march_id'])) { //撤回已占领的军队
				M_March::setMarchBack($defColonyInfo['atk_march_id']); //撤的是第三方的行军
			}
			//属地删除
			$delUp = M_ColonyCity::del($defColonyInfo['atk_city_id'], $marchInfo['def_pos']); //删除第三方的城市属地
			if ($delUp) { //发送消息邮件
				$content = array(T_Lang::C_WILD_CITY_LOSE, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y, date('Y'), date('m'), date('d'), date('h'), date('i'), $marchInfo['atk_nickname']);
				M_Message::sendSysMessage($defColonyInfo['atk_city_id'], json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
			}
			$upArr = array('atk_city_id' => $atkCityId, 'atk_march_id' => $marchInfo['id']);
			M_ColonyCity::setInfo($mapRow['city_id'], $upArr);
			//占领成功 如果有防御方 则需要删除这个 防御方 敌情信息
			//获取防御方的敌情信息
			$marchArr = M_March::getMarchList($defColonyInfo['atk_city_id'], M_War::MARCH_OWN_DEF);
			Logger::dev("OldDefCityId#" . $mapRow['city_id'] . "DefPos#{$marchInfo['def_pos']};MarchList#" . json_encode($marchArr));
			foreach ($marchArr as $marchId => $val) {
				if ($val['def_pos'] == $marchInfo['def_pos']) {
					//更新其他进攻方的行军数据中的 防御城市ID
					$marchData = array('id' => $marchId, 'def_city_id' => 0);
					$ret       = M_March_Info::set($marchData);

					//删除当前防守方的敌情
					Logger::dev("2delSyncMarchData#{$marchId}#{$defColonyInfo['atk_city_id']}");
					M_March::syncDelMarchBack($marchId, $defColonyInfo['atk_city_id']);
				}
			}
		}
		$addFlag = M_ColonyCity::add($marchInfo);
		if ($addFlag) //占领成功
		{
			$recordActive = M_Config::getVal('record_active');
			$now          = time(); //获取战绩值的当前日期
			$startDay     = $recordActive['start']; //获取战绩值起始时间
			$endDay       = $recordActive['end']; //获取战绩值截止时间
			$d2           = strtotime($startDay);
			$d3           = strtotime($endDay);
			if ($d2 < $now && $now < $d3) //要在活动期间并且今天未被占领
			{
				$cityIdArr = array();
				$rc        = new B_Cache_RC(T_Key::OCCUPIED_LIST, date('Ymd') . $marchInfo['atk_city_id']);
				$cityIdArr = $rc->smembers();
				if (empty($cityIdArr) || (!empty($cityIdArr) && !(in_array($mapRow['city_id'], $cityIdArr)))) {
					$recordValue = 0;
					if ($cityInfo['level'] == 3) {
						$recordValue = $recordActive['list']['record_value_3'];
					} else if ($cityInfo['level'] == 4) {
						$recordValue = $recordActive['list']['record_value_4'];
					} else if ($cityInfo['level'] == 5) {
						$recordValue = $recordActive['list']['record_value_5'];
					}
					$rc = new B_Cache_RC(T_Key::RANKINGS_RECORD);
					$rc->zincrby($recordValue, $marchInfo['atk_city_id']);
				}
			}

			$rc = new B_Cache_RC(T_Key::OCCUPIED_LIST, date('Ymd') . $marchInfo['atk_city_id']);
			$rc->sadd($mapRow['city_id']);
			$atkcityInfo         = M_City::getInfo($marchInfo['atk_city_id']); //攻击方城市信息
			$cityColonyUnionInfo = M_Union::getInfo($cityInfo['union_id']);
			$cityColonyInfo      = M_ColonyCity::getInfo($mapRow['city_id']);

			$objPlayer   = new O_Player($mapRow['city_id']);
			$cdRescueArr = $objPlayer->CD()->toFront(O_CD::TYPE_RESCUE);
			$rescueTimes = explode(',', M_Config::getVal('rescue_cd_times'));
			$diff        = $cdRescueArr[0];
			$rescueCD    = array(0, $cityColonyInfo['rescue_num'], $rescueTimes[1], $rescueTimes[2]);
			if ($diff > 0) {
				$rescueCD[0] = $diff;
			}

			$atkcityColonyInfo = array(
				'hold_flag'    => 1,
				'nickName'     => $atkcityInfo['nickname'],
				'level'        => $atkcityInfo['level'],
				'unionName'    => $cityColonyUnionInfo['name'],
				'posNo'        => M_MapWild::calcWildMapPosXYByNo($atkcityInfo['pos_no']),
				'rescueCd'     => $rescueCD,
				'SelfRsMhTime' => T_App::ONE_MINUTE
			);
			M_Sync::addQueue($mapRow['city_id'], M_Sync::KEY_CITY_OCCUPIED, $atkcityColonyInfo);
			//发送消息邮件
			$content = array(T_Lang::C_WILD_CITY_OCCUPIED_SUCC, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
			M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
			$content = array(T_Lang::C_WILD_CITY_OCCUPIED, date('Y'), date('m'), date('d'), date('h'), date('i'), $marchInfo['atk_nickname'], array(T_Lang::$Map[$z1]), $x1 . ',' . $y1);
			M_Message::sendSysMessage($mapRow['city_id'], json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
			$needBack = false;

			$dailyAward = M_Config::getVal('active_award');
			list($IsOpen, $activeField) = M_Task::getHoldNpcActiveStaus($atkCityId, $dailyAward);
			$atkCityInfo = M_City::getInfo($atkCityId);
			if ($IsOpen == 5 || $IsOpen == 6) //第3阶段的活动
			{
				$dailyAward = M_Config::getVal('active_award');
				$rc         = new B_Cache_RC(T_Key::CITY_OCCOUPIED_TIMES, $atkCityId);
				$rc->hincrby($mapRow['city_id'], 1);
				$numArr = $rc->hgetall();
				if ($dailyAward['list3']['num'] <= $numArr[$mapRow['city_id']]) {
					M_Task::active($atkCityId, 'award'); //更新学院活动的完成状态
				}
			} elseif ($IsOpen == 7 || $IsOpen == 8) {
				if ($cityInfo['level'] == $atkCityInfo['level']) {
					$rc = new B_Cache_RC(T_Key::CITY_OCCOUPIED_TIMES, $atkCityId);
					$rc->hincrby($mapRow['city_id'], 1);
					$numArr = $rc->hgetall();
					$numArr = $rc->hgetall();
					if ($dailyAward['list4']['num'] <= $numArr[$mapRow['city_id']]) {
						M_Task::active($atkCityId, 'award'); //更新学院活动的完成状态
					}
				}
			} elseif ($IsOpen == 9 || $IsOpen == 10) {
				if ($cityInfo['level'] >= $atkCityInfo['level']) {
					$rc = new B_Cache_RC(T_Key::CITY_OCCOUPIED_TIMES, $atkCityId);
					$rc->hincrby($mapRow['city_id'], 1);
					$numArr = $rc->hgetall();
					if ($dailyAward['list5']['num'] <= $numArr[$mapRow['city_id']]) {
						M_Task::active($atkCityId, 'award'); //更新学院活动的完成状态
					}
				}
			}
		} else { //发送消息邮件
			$content = array(T_Lang::C_WILD_CITY_OCCUPIED_FULL, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
			M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
		}
		return $needBack;
	}

	/**
	 *更新解救城市所属联盟
	 * @author duhuihui
	 * @param int $atkCityId
	 * @param int $defCityId
	 * @param array $marchInfo
	 * @return bool
	 */
	static private function _rescueCityBattle($atkCityId, $defCityId, $marchInfo) {
		$now = time();
		//时间间隔
		$needBack = true;
		$mapRow   = M_MapWild::getWildMapInfo($marchInfo['def_pos']); //目的地地图
		list($z, $x, $y) = M_MapWild::calcWildMapPosXYByNo($mapRow['pos_no']);
		$atkmapRow = M_MapWild::getWildMapInfo($marchInfo['atk_pos']); //目的地地图
		list($z1, $x1, $y1) = M_MapWild::calcWildMapPosXYByNo($atkmapRow['pos_no']);
		$defColonyInfo = M_ColonyCity::getInfo($mapRow['city_id']);
		$cityInfo      = M_City::getInfo($mapRow['city_id']);

		if ($defColonyInfo['atk_city_id'] > 0) //之前被占领
		{ //删除敌情列表中的行军Id

			if (!empty($defColonyInfo['atk_march_id'])) { //撤回已占领的军队
				M_March::setMarchBack($defColonyInfo['atk_march_id']); //撤的是第三方的行军
			}
			//属地删除
			$delUp = M_ColonyCity::del($defColonyInfo['atk_city_id'], $marchInfo['def_pos']); //删除第三方的城市属地
			if ($delUp) {
				//发送消息邮件
				$content = array(T_Lang::C_WILD_CITY_RESCUE_LOSE, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y, date('Y'), date('m'), date('d'), date('h'), date('i'), $marchInfo['atk_nickname']);
				M_Message::sendSysMessage($defColonyInfo['atk_city_id'], json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
			}
			//占领成功 如果有防御方 则需要删除这个 防御方 敌情信息
			//获取防御方的敌情信息
			Logger::dev("OldDefCityId#" . $mapRow['city_id'] . "DefPos#{$marchInfo['def_pos']}");
			$marchArr = M_March::getMarchList($defColonyInfo['atk_city_id'], M_War::MARCH_OWN_DEF);
			Logger::dev("MarchList#" . json_encode($marchArr));
			foreach ($marchArr as $marchId => $val) {
				if ($val['def_pos'] == $marchInfo['def_pos']) {
					//更新其他进攻方的行军数据中的 防御城市ID
					$marchData = array('id' => $marchId, 'def_city_id' => 0);
					$ret       = M_March_Info::set($marchData);

					//删除当前防守方的敌情
					Logger::dev("2delSyncMarchData#{$marchId}#{$defColonyInfo['atk_city_id']}");
					M_March::syncDelMarchBack($marchId, $defColonyInfo['atk_city_id']);
				}
			}
			list($z, $x, $y) = M_MapWild::calcWildMapPosXYByNo($mapRow['pos_no']);
			$addFlag = false;
			//更新数据
			$updInfo    = array('atk_city_id' => 0, 'atk_march_id' => 0);
			$updateFlag = M_ColonyCity::setInfo($defCityId, $updInfo);
			$ret        = M_MapWild::setWildMapInfo($marchInfo['def_pos'], array('march_id' => 0));

			if ($updateFlag && $ret) {
				$defCityInfo         = M_City::getInfo($defCityId);
				$cityColonyUnionInfo = M_Union::getInfo($defCityInfo['union_id']);
				$atkcityInfo         = M_City::getInfo($atkCityId);
				list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($atkcityInfo['pos_no']);
				$cityColonyInfo = M_ColonyCity::getInfo($defCityId);

				$objPlayerDef = new O_Player($defCityId);
				$cdRescueArr  = $objPlayerDef->CD()->toFront(O_CD::TYPE_RESCUE);
				$rescueTimes  = explode(',', M_Config::getVal('rescue_cd_times'));
				$diff         = $cdRescueArr[0];
				$rescueCD     = array(0, $cityColonyInfo['rescue_num'], $rescueTimes[1], $rescueTimes[2]);
				if ($diff > 0) {
					$rescueCD[0] = $diff;
				}

				$atkcityColonyInfo = array(
					'hold_flag'    => 0,
					'nickName'     => '',
					'level'        => 0,
					'unionName'    => $cityColonyUnionInfo['name'],
					'posNo'        => array(),
					'rescueCd'     => $rescueCD,
					'SelfRsMhTime' => T_App::ONE_MINUTE
				);
				M_Sync::addQueue($mapRow['city_id'], M_Sync::KEY_CITY_OCCUPIED, $atkcityColonyInfo);
				M_MapWild::syncWildMapBlockCache($marchInfo['def_pos']); //刷新旧的地图数据
				//发送消息邮件
				$content = array(T_Lang::C_WILD_CITY_RESCUE_SUCC, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
				M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
				if ($atkCityId != $mapRow['city_id']) {
					$content = array(T_Lang::C_WILD_CITY_RESCUED_SUCC, $marchInfo['atk_nickname'], array(T_Lang::$Map[$z1]), $x1 . ',' . $y1);
					M_Message::sendSysMessage($mapRow['city_id'], json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
					//解救盟友的城市


					$recordActive = M_Config::getVal('record_active');
					$now          = time(); //获取战绩值的当前日期
					$startDay     = $recordActive['start']; //获取战绩值起始时间
					$endDay       = $recordActive['end']; //获取战绩值截止时间
					$d2           = strtotime($startDay);
					$d3           = strtotime($endDay);
					if ($d2 < $now && $now < $d3) //要在活动期间并且今天未被占领
					{
						$cityIdArr = array();
						$rc        = new B_Cache_RC(T_Key::OCCUPIED_LIST, date('Ymd') . $marchInfo['atk_city_id']);
						$cityIdArr = $rc->smembers();
						if (empty($cityIdArr) || (!empty($cityIdArr) && !(in_array($mapRow['city_id'], $cityIdArr)))) {
							$recordValue = 0;
							$recordValue = $recordActive['list']['record_value_rescue'];
							$rc          = new B_Cache_RC(T_Key::RANKINGS_RECORD);
							$rc->zincrby($recordValue, $marchInfo['atk_city_id']);
						}
					}

					$rc = new B_Cache_RC(T_Key::OCCUPIED_LIST, date('Ymd') . $marchInfo['atk_city_id']);
					$rc->sadd($mapRow['city_id']);


				}

			} else {
				//发送消息邮件
				$content = array(T_Lang::C_WILD_CITY_RESCUE_FAIL, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
				M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
				if ($atkCityId != $mapRow['city_id']) {
					$content = array(T_Lang::C_WILD_CITY_RESCUED_FAIL, $marchInfo['atk_nickname'], array(T_Lang::$Map[$z1]), $x1 . ',' . $y1);
					M_Message::sendSysMessage($mapRow['city_id'], json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
				}
			}
			M_MapWild::syncWildMapBlockCache($marchInfo['def_pos']);
		}
		return $needBack;
	}

	/**
	 * 更新驻守城市所属联盟
	 * @author duhuihui
	 * @param int $atkCityId
	 * @param int $defCityId
	 * @param array $marchInfo
	 * @return bool
	 */
	static private function _holdCityBattle($atkCityId, $defCityId, $marchInfo) {
		$now      = time();
		$needBack = true;
		$mapRow   = M_MapWild::getWildMapInfo($marchInfo['def_pos']); //目的地地图
		$cityInfo = M_City::getInfo($mapRow['city_id']);
		list($z, $x, $y) = M_MapWild::calcWildMapPosXYByNo($mapRow['pos_no']);
		$updateFlag = false;
		if ($marchInfo['id'] > 0) {
			$updateFlag = M_ColonyCity::setInfo($defCityId, array('atk_march_id' => $marchInfo['id']));
			if ($updateFlag) {
				$ret               = M_MapWild::setWildMapInfo($marchInfo['def_pos'], array('march_id' => $marchInfo['id']));
				$atkcityColonyInfo = array(
					'hold_flag' => 2,
				);
				M_Sync::addQueue($mapRow['city_id'], M_Sync::KEY_CITY_OCCUPIED, $atkcityColonyInfo);
				$wildInfo = M_ColonyCity::getNoByPosNo($atkCityId, $marchInfo['def_pos']);
				if (!empty($wildInfo)) {
					$marchId  = $level = $zone = $posx = $poxy = 0;
					$nickname = '';
					$no       = $wildInfo['no'];
					$noInfo   = $wildInfo['val'];
					if (!empty($noInfo[1])) {
						list($zone, $posx, $poxy) = M_MapWild::calcWildMapPosXYByNo($noInfo[1]);
						$mapInfo  = M_MapWild::getWildMapInfo($noInfo[1]);
						$marchId  = $mapInfo['march_id'];
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
						'MarchId'       => $marchInfo['id'], //行军中
						'MarchType'     => 1,
						'TaxExprieTime' => $noInfo[2],
						'ExprieTime'    => $noInfo[3],
						'IntervalTime'  => $noInfo[2] - $now,
					);

					M_Sync::addQueue($atkCityId, M_Sync::KEY_CITY_COLONY, $msRow); //同步属地数据
				}

				M_MapWild::syncWildMapBlockCache($marchInfo['def_pos']);
				//发送消息邮件
				$content = array(T_Lang::C_WILD_CITY_HOLD_SUCC, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
				M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));

				$needBack = false;
			} else { //发送消息邮件
				$content = array(T_Lang::C_WILD_CITY_HOLD_FAIL, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
				M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
			}

			$ret1 = M_March::setMarchHold($marchInfo);
			M_MapWild::syncWildMapBlockCache($marchInfo['def_pos']);
		}
		return $ret1;
	}

	/**
	 * 防御方为空的战斗报告
	 * @author huwei
	 * @param array $data
	 * @param bool $needCalcRes
	 * @return array 奖励数据
	 */
	static private function _buildEmptyDefReport($data, $needCalcRes = false) {
		$atkCarryNum      = 0;
		$now              = time();
		$atkHero          = $data['atkHero'];
		$atkReportContent = array();
		if (is_array($atkHero)) {
			foreach ($atkHero as $heroId => $heroInfo) {
				$atkCarryNum += $heroInfo['carry'] * $heroInfo['left_num'];
				$atkReportContent[$heroId] = array(
					'Nickname' => $heroInfo['nickname'],
					'FaceId'   => $heroInfo['face_id'],
					'Gender'   => $heroInfo['gender'],
					'Quality'  => $heroInfo['quality'],
					'Level'    => $heroInfo['level'],
					'WeaponId' => $heroInfo['weapon_id'],
					'ArmyId'   => $heroInfo['army_id'],
					'IsDie'    => 0,
					'DieNum'   => 0,
					'Exp'      => 0,
					'ArmyNum'  => $heroInfo['army_num'],
				);
			}
		}

		list($atkFaceId, $atkGender) = array($data['atkData'][6], $data['atkData'][7]);
		list($defFaceId, $defGender) = array($data['defData'][6], $data['defData'][7]);
		//报告内容
		$reportContent = array(
			'Atk'        => $atkReportContent,
			'Def'        => array(),
			'AtkArmyExp' => array(),
			'DefArmyExp' => array(),
			'Credit'     => 0,
		);

		$initData = array(
			M_Battle_Calc::REPORT_TYPE_ATK,
			$data['atkData'][0],
			$data['defData'][0],
			array($data['atkData'][5], $data['atkData'][6], $data['atkData'][2], $data['atkData'][7]),
			array($data['defData'][5], $data['defData'][6], $data['defData'][2], $data['defData'][7]),
			$data['battleType'],
		);

		$rewardArr = array();
		if ($needCalcRes &&
			$data['defData'][0] > 0
		) {
			//攻击城市才有资源掠夺
			$rewardResArr = M_War::getAtkPlunderRes($data['atkData'][0], $data['defData'][0], $atkCarryNum);
			//扣除玩家资源
			$objPlayerDef = new O_Player($data['defData'][0]);
			$newRewardArr = array();
			foreach ($rewardResArr as $resKey => $resNum) {
				$newRewardArr[] = array($resKey, $resNum, 0);
				$objPlayerDef->Res()->incr($resKey, -$resNum);
			}
			$objPlayerDef->save();

			$bDecrRes     = true;
			$objPlayerAtk = new O_Player($data['atkData'][0]);
			$objPlayerAtk->City()->filterAward($newRewardArr);
			$rewardArr = $newRewardArr;

			$isOpen      = 0;
			$atkCityInfo = $objPlayerAtk->getCityBase();
			$defCityInfo = $objPlayerDef->getCityBase();
			$atkRenown   = !empty($atkCityInfo['renown']) ? $atkCityInfo['renown'] : 0;
			$defRenown   = !empty($defCityInfo['renown']) ? $defCityInfo['renown'] : 0;
			$isOpen      = M_War::openDecrArmyNum($atkRenown, $defRenown);
			if ($isOpen == 1) {
				M_War::failToDecrArmyNum($data['atkData'][0], $data['defData'][0]);
			}

			$objPlayerAtk->Liveness()->check(M_Liveness::GET_POINT_ATK_CITY);
			$objPlayerAtk->save();
		}

		$tmpReportId = M_WarReport::initWarReport($initData);
		if ($tmpReportId) {
			$reportData = array(
				'id'             => $tmpReportId,
				'content'        => $reportContent,
				'reward'         => $rewardArr,
				'is_succ'        => T_App::SUCC,
				'replay_address' => '',
				'create_at'      => $now,
			);
			$reportId   = M_WarReport::addWarReport($initData, $reportData);
		}

		return $rewardArr;
	}

}

?>