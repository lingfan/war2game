<?php

class M_March {
	/** 进攻 */
	const MARCH_ACTION_ATT = 1;
	/** 侦察 */
	const MARCH_ACTION_SCOUT = 2;
	/** 占领野地 */
	const MARCH_ACTION_HOLD = 3;
	/** 空袭*/
	const MARCH_ACTION_BOMB = 4;
	/** 增援 */
	const MARCH_ACTION_HELP = 5;
	/** 返回 */
	const MARCH_ACTION_BACK = 6;
	/** 占领据点*/
	const MARCH_ACTION_CAMP = 7;
	/** 副本 */
	const MARCH_ACTION_FB = 8;
	/** 占领城市*/
	const MARCH_ACTION_CITY = 9;
	/** 解救城市*/
	const MARCH_ACTION_RESCUE_CITY = 10;
	/** 驻守城市*/
	const MARCH_ACTION_HOLD_CITY = 11;
	/** 突击*/
	const MARCH_ACTION_BOUT = 12;
	/** 爬楼 */
	const MARCH_ACTION_FLOOR = 14;
	/** 多人副本 */
	const MARCH_ACTION_MULTI_FB = 14;

	/** 行军类型 */
	static $marchAction = array(
		self::MARCH_ACTION_ATT         => '进攻',
		self::MARCH_ACTION_SCOUT       => '侦察',
		self::MARCH_ACTION_HOLD        => '占领',
		self::MARCH_ACTION_BOMB        => '空袭',
		self::MARCH_ACTION_HELP        => '增援',
		self::MARCH_ACTION_BACK        => '返回',
		self::MARCH_ACTION_CAMP        => '据点',
		self::MARCH_ACTION_FB          => '副本',
		self::MARCH_ACTION_CITY        => '占领城市',
		self::MARCH_ACTION_RESCUE_CITY => '解救城市',
		self::MARCH_ACTION_HOLD_CITY   => '驻守城市',
		self::MARCH_ACTION_BOUT        => '突围',
		self::MARCH_ACTION_FLOOR       => '爬楼',
	);

	/** 移动中 */
	const MARCH_FLAG_MOVE = 0;
	/** 战斗中 */
	const MARCH_FLAG_BATTLE = 1;
	/** 驻守中 */
	const MARCH_FLAG_HOLD = 2;
	/** 等待(排队)中 */
	const MARCH_FLAG_WAIT = 3;

	static $marchFlag = array(
		self::MARCH_FLAG_MOVE   => '移动',
		self::MARCH_FLAG_BATTLE => '战斗',
		self::MARCH_FLAG_HOLD   => '驻守',
		self::MARCH_FLAG_WAIT   => '等待',
	);

	/** 副本行军ID */
	const FB_MARCH_ID = 1;
	/** 突围行军ID */
	const BOUT_MARCH_ID = 2;
	/** 爬楼行军ID */
	const FLOOR_MARCH_ID = 3;

	/** 固定行军ID */
	static $fixMarchId = array(
		M_War::BATTLE_TYPE_FB    => self::FB_MARCH_ID,
		M_War::BATTLE_TYPE_BOUT  => self::BOUT_MARCH_ID,
		M_War::BATTLE_TYPE_FLOOR => self::FLOOR_MARCH_ID,
	);

	/**
	 * 部队出征同步行军信息
	 * @author hujunyun            建立
	 * @author huwei            修改
	 * @param int $marchId 行军ID
	 */
	static public function syncMarch2Front($marchId) {
		$marchId = intval($marchId);

		if ($marchId > 0) {
			$marchInfo = M_March_Info::get($marchId);
			if (isset($marchInfo['id'])) {
				$sysData = array(
					'Id'              => $marchInfo['id'],
					'AttCityId'       => $marchInfo['atk_city_id'],
					'DefCityId'       => $marchInfo['def_city_id'],
					'AttCityNickName' => $marchInfo['atk_nickname'],
					'DefCityNickName' => $marchInfo['def_nickname'],
					'ActionType'      => $marchInfo['action_type'],
					'HeroList'        => json_decode($marchInfo['hero_list'], true),
					'AttPos'          => M_MapWild::calcWildMapPosXYByNo($marchInfo['atk_pos']),
					'DefPos'          => M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']),
					'ArrivedTime'     => $marchInfo['arrived_time'],
					'RemainingTime'   => max($marchInfo['arrived_time'] - time(), 0),
					'WaitEndTime'     => $marchInfo['wait_end_time'],
					'ResData'         => !empty($marchInfo['award']) ? json_decode($marchInfo['award'], true) : array(),
					'Flag'            => $marchInfo['flag'],
					'CreateAt'        => $marchInfo['create_at'],
					'BattleId'        => $marchInfo['battle_id'],
					'CampStartPos'    => M_MapWild::calcWildMapPosXYByNo($marchInfo['start_pos_ext']),
				);

				M_Sync::addQueue($marchInfo['atk_city_id'], M_Sync::KEY_MARCH_DATA, array($marchId => $sysData));
				if ($marchInfo['def_city_id'] > 0) {
					M_Sync::addQueue($marchInfo['def_city_id'], M_Sync::KEY_MARCH_DATA, array($marchId => $sysData), false);
				}
			}
		}
	}

	/**
	 * 部队出征同步行军信息
	 * @param int $marchId 行军ID
	 */
	static public function changeOccupiedMarchData($marchInfo, $newDefCityId, $newDefCityName) {
		if ($marchInfo['id'] > 0) {
			$sysData = array(
				'Id'              => $marchInfo['id'],
				'AttCityId'       => $marchInfo['atk_city_id'],
				'DefCityId'       => $newDefCityId,
				'AttCityNickName' => $marchInfo['atk_nickname'],
				'DefCityNickName' => $newDefCityName,
				'ActionType'      => $marchInfo['action_type'],
				'HeroList'        => json_decode($marchInfo['hero_list'], true),
				'AttPos'          => M_MapWild::calcWildMapPosXYByNo($marchInfo['atk_pos']),
				'DefPos'          => M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']),
				'ArrivedTime'     => $marchInfo['arrived_time'],
				'RemainingTime'   => max($marchInfo['arrived_time'] - time(), 0),
				'WaitEndTime'     => $marchInfo['wait_end_time'],
				'ResData'         => !empty($marchInfo['award']) ? json_decode($marchInfo['award'], true) : array(),
				'Flag'            => $marchInfo['flag'],
				'CreateAt'        => $marchInfo['create_at'],
				'BattleId'        => $marchInfo['battle_id'],
				'CampStartPos'    => array(),
			);

			$upData = array(
				'id'           => $marchInfo['id'],
				'def_city_id'  => $newDefCityId,
				'def_nickname' => $newDefCityName,
			);
			$ret    = M_March_Info::set($upData);

			M_Sync::addQueue($newDefCityId, M_Sync::KEY_MARCH_DATA, array($marchInfo['id'] => $sysData));
			M_Sync::addQueue($marchInfo['def_city_id'], M_Sync::KEY_MARCH_DATA, array($marchInfo['id'] => M_Sync::DEL));

		}
	}


	/**
	 * 同步副本结束行军记录
	 * @param int $cityId
	 * @param int $type
	 */
	static public function syncMarchEnd($cityId, $type) {
		if (isset(self::$fixMarchId[$type])) {
			M_Sync::addQueue($cityId, M_Sync::KEY_MARCH_END, array(self::$fixMarchId[$type] => $cityId));
		}
	}

	/**
	 * 同步突围开始行军记录
	 * @param array $BD
	 */
	static public function syncMarchStart($BD) {
		$actionType = '';
		$type       = $BD['Type'];
		if ($BD['Type'] == M_War::BATTLE_TYPE_FB) {
			$DefPos     = M_MapWild::calcWildMapPosXYByNo($BD['DefPos']);
			$actionType = M_March::MARCH_ACTION_FB;
		} else if ($BD['Type'] == M_War::BATTLE_TYPE_BOUT) {
			$DefPos = explode('_', $BD['DefPos']);
			array_unshift($DefPos, 0);
			$actionType = M_March::MARCH_ACTION_BOUT;
		} else if ($BD['Type'] == M_War::BATTLE_TYPE_FLOOR) {
			$DefPos = explode('_', $BD['DefPos']);
			array_unshift($DefPos, 0);
			$actionType = M_March::MARCH_ACTION_FLOOR;
		}

		if (!empty($actionType)) {
			$sysData = array(
				'Id'              => self::$fixMarchId[$type],
				'AttCityId'       => $BD[T_Battle::CUR_OP_ATK]['CityId'],
				'DefCityId'       => $BD[T_Battle::CUR_OP_DEF]['CityId'],
				'AttCityNickName' => $BD[T_Battle::CUR_OP_ATK]['Nickname'],
				'DefCityNickName' => $BD[T_Battle::CUR_OP_DEF]['Nickname'],
				'ActionType'      => $actionType,
				'HeroList'        => array_keys($BD[T_Battle::CUR_OP_ATK]['HeroDataList']),
				'AttPos'          => M_MapWild::calcWildMapPosXYByNo($BD['AtkPos']),
				'DefPos'          => $DefPos,
				'ArrivedTime'     => $BD['StartTime'],
				'RemainingTime'   => 0,
				'Award'           => array(),
				'Flag'            => M_March::MARCH_FLAG_BATTLE,
				'BattleId'        => $BD['Id'],
				'CampStartPos'    => array(0, 0, 0),
			);
			M_Sync::addQueue($BD[T_Battle::CUR_OP_ATK]['CityId'], M_Sync::KEY_MARCH_DATA, array(self::$fixMarchId[$type] => $sysData));
		}
	}

	/**
	 * 删除行军记录
	 * @author huwei on 20110615
	 * @param int $marchId 行军ID
	 * @return bool
	 */
	static public function delMarchInfo($marchId) {
		$ret       = false;
		$marchInfo = M_March_Info::get($marchId);
		if (!empty($marchInfo['id'])) {
			//确保 所有的英雄状态为空闲 或者死亡 才能删除队列
			$errIds   = $ids = array();
			$heroList = json_decode($marchInfo['hero_list'], true);
			foreach ($heroList as $k => $heroId) {
				$heroInfo = M_Hero::getHeroInfo($heroId);
				if (T_Hero::FLAG_FREE != $heroInfo['flag'] &&
					T_Hero::FLAG_DIE != $heroInfo['flag']
				) {
					$ids[] = $heroId;
				} else {
					$errIds[] = $heroId;
				}
			}
			$ret = M_Hero::changeHeroFlag($heroInfo['city_id'], $ids, T_Hero::FLAG_FREE, array('march_id' => 0)); //同步军官缓存,设置军官空闲

			if ($ret) {
				M_March_Queue::del($marchId);

				$obj_ml = new M_March_List($marchInfo['atk_pos']);
				$obj_ml->del($marchId);

				M_March::syncDelMarchBack($marchId, $marchInfo['atk_city_id']);

				if (!empty($marchInfo['def_city_id'])) {
					$obj_ml = new M_March_List($marchInfo['def_pos']);
					$obj_ml->del($marchId);

					M_March::syncDelMarchBack($marchId, $marchInfo['def_city_id']);
				}

				M_March_Info::del($marchId);

			} else {
				$msg = array(__METHOD__, "Del MarchId#{$marchId} Fail", $errIds);
				Logger::error($msg);
			}
		}

		return $ret;
	}

	/**
	 * 玩家自己的据点行军列表
	 * @author huwei
	 * @param int $cityId 出发城市ID
	 * @param int $campId 据点ID
	 * @return array
	 */
	static public function getCampMarchList($cityId, $campId) {
		$cityInfo = M_City::getInfo($cityId);
		$obj_ml   = new M_March_List($cityInfo['pos_no']);
		$list     = $obj_ml->get();
		$ret      = array();
		foreach ($list as $marchId) {
			$info = M_March_Info::get($marchId);

			if (!empty($info) &&
				$info['atk_city_id'] == $cityId &&
				!empty($info['start_pos_ext']) &&
				$info['flag'] == M_March::MARCH_FLAG_MOVE &&
				$info['action_type'] == M_March::MARCH_ACTION_CAMP
			) {
				list($tmpZone, $tmpCampId, $tmpLineNo) = M_MapWild::calcWildMapPosXYByNo($info['start_pos_ext']);
				if ($tmpZone == T_App::MAP_CAMPAIGN && $tmpCampId == $campId) {
					$ret[$marchId] = $info;
				}
			}
		}
		return $ret;
	}

	/**
	 * 据点最大派遣数量
	 * @author huwei
	 * @param int $cityId
	 * @param int $posNo
	 * @param int $campId
	 */
	static public function getMarchCampMaxNum($cityId, $posNo, $campId) {
		$obj_ml = new M_March_List($posNo);
		$list   = $obj_ml->get();
		$ret    = array();
		$num    = 0;
		foreach ($list as $marchId) {
			$info = M_March_Info::get($marchId);
			if (!empty($info) &&
				$info['atk_city_id'] == $cityId &&
				$info['action_type'] == M_March::MARCH_ACTION_CAMP
			) {
				list($tmpZone, $tmpCampId, $tmpLineNo) = M_MapWild::calcWildMapPosXYByNo($info['def_pos']);
				if ($tmpCampId == $campId) {
					$num++;
				}
			}
		}
		return $num;
	}

	/**
	 * 玩家的行军列表
	 * @author HeJunyun
	 * @author huwei modify 20120107
	 * @param int $cityId 出发城市ID
	 * @return array
	 */
	static public function getMarchList($cityId, $own = M_War::MARCH_OWN_ALL) {
		$cityInfo = M_City::getInfo($cityId);
		$obj_ml   = new M_March_List($cityInfo['pos_no']);
		$list     = $obj_ml->get();
		$ret      = array();
		foreach ($list as $marchId) {
			$bExist = M_March_Queue::exist($marchId);
			if ($bExist) {
				$info = M_March_Info::get($marchId);
				if (!empty($info)) {
					$isOwn = false;
					if (($own & M_War::MARCH_OWN_ATK) > 0 &&
						$info['atk_city_id'] == $cityId
					) {
						$isOwn = true;
					} else if (($own & M_War::MARCH_OWN_DEF) > 0 &&
						$info['def_city_id'] == $cityId
					) {
						$isOwn = true;
					}

					if ($isOwn) {
						$ret[$marchId] = $info;
					}
				}
			}
		}
		return $ret;
	}

	/**
	 * 获取等待战斗的行军列表
	 * @author huwei
	 * @param int $cityId 城市ID
	 * @param int $own 拥有方
	 * @return $list array
	 */
	static public function getMarchListForWait($cityId, $own = M_War::MARCH_OWN_ALL) {
		$list = M_March::getMarchList($cityId, $own);
		$ret  = false;
		foreach ($list as $marchId => $val) {
			if (M_March::MARCH_FLAG_WAIT == $val['flag']) {
				$ret[$marchId] = $val;
			}
		}
		return $ret;
	}

	/**
	 * 同步出征列表
	 * @author huwei
	 * @param int $cityId 出发城市ID
	 * @return bool
	 */
	static public function syncOutForcesById($cityId) {
		$ret = false;
		if (!empty($defCityId)) {
			$list = B_DB::instance('WarMarch')->getsBy(array('atk_city_id' => $cityId));
			if (!empty($list)) {
				$rc  = new B_Cache_RC(T_Key::WAR_OUT_FORCE, $cityId);
				$ret = $rc->jsonset($list, T_App::ONE_WEEK);
			}
		}
		return $ret;
	}

	static public function setCampBack($marchInfo) {
		//据点排队已满 返回上一级基地 如果返回过一次 则回城
		//相当于 移动操作
		if (!empty($marchInfo['id']) &&
			$marchInfo['action_type'] == M_March::MARCH_ACTION_CAMP &&
			!empty($marchInfo['start_pos_ext']) &&
			$marchInfo['def_pos'] != $marchInfo['start_pos_ext']
		) {
			$arrivedTime = $now + T_App::ONE_MINUTE * M_Campaign::MARCH_TIME;
			Logger::dev("setCAMPMarchBack MarchInfo:cityId#{$marchInfo['atk_city_id']};defpos#" . json_encode($marchInfo['def_pos']) . ";star_pos_ext#{$marchInfo['start_pos_ext']};marchId#{$marchId};arrivedTime#{$arrivedTime}");

			$atkPosArr = $marchInfo['def_pos'];
			$defPosArr = M_MapWild::calcWildMapPosXYByNo($marchInfo['start_pos_ext']);

			$log = "atkPosArr@" . json_encode($atkPosArr) . "defPosArr#" . json_encode($defPosArr);
			Logger::dev("CAMPMarchBack {$log}");
			$campBaseList = M_Base::campaignAll();
			list($defNpcId, $warBgNo) = explode('|', $campBaseList[$defPosArr[1]][$defPosArr[2]]);
			$npcInfo = M_NPC::getInfo($defNpcId);

			$upData   = array(
				'id'           => $marchId,
				'action_type'  => M_March::MARCH_ACTION_CAMP,
				'flag'         => M_March::MARCH_FLAG_MOVE,
				'award'        => json_encode($awardArr),
				'atk_pos'      => $atkPosArr,
				'def_pos'      => $defPosArr,
				'atk_city_id'  => $marchInfo['atk_city_id'],
				'def_city_id'  => $marchInfo['def_city_id'],
				'atk_nickname' => $marchInfo['atk_nickname'],
				'def_nickname' => $npcInfo['nickname'],
				'arrived_time' => $arrivedTime,
				'battle_id'    => 0,
			);
			$campBack = true;

			$ret = M_March_Info::set($upData);
			//删除老的基地的数据
			$obj_ml = new M_March_List($marchInfo['def_pos']);
			$list   = $obj_ml->del($marchId);

			M_March::syncDelMarchBack($marchId, $marchInfo['def_city_id']);

			//更新新的基地的数据
			$obj_ml = new M_March_List($marchInfo['start_pos_ext']);
			$obj_ml->add($marchId);

		}
	}

	/**
	 * 战斗结束，设置奖励，行军返回
	 * @author HeJunyun on 20110711
	 * @param int $marchId 行军ID
	 * @param array $awardArr 奖励数组
	 * @return bool
	 */
	static public function setMarchBack($marchId, $awardArr = array(), $backKeepMarchId = false) {
		$ret = false;
		if (!empty($marchId)) {
			$now       = time();
			$marchInfo = M_March_Info::get($marchId);
			if (!empty($marchInfo['id'])) {
				if ($now < $marchInfo['arrived_time']) { //出征到一半 返回时间
					$t = $now - $marchInfo['create_at'];
				} else {
					list($zone, $campId, $defLineNo) = M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']);
					if ($zone == T_App::MAP_CAMPAIGN) { //战役的返回时间 固定
						$defLineNo = strval($defLineNo);
						$t         = $defLineNo{0} * T_App::ONE_MINUTE * M_Campaign::MARCH_TIME;
					} else {
						$t = $marchInfo['arrived_time'] - $marchInfo['create_at'];
					}
				}
				$arrivedTime = $t + $now;
				$upData      = array(
					'id'           => $marchId,
					'action_type'  => M_March::MARCH_ACTION_BACK,
					'flag'         => M_March::MARCH_FLAG_MOVE,
					'award'        => json_encode($awardArr),
					'arrived_time' => $arrivedTime,
					'battle_id'    => 0,
				);

				$ret = M_March_Info::set($upData);
				if ($ret) {
					$syncData = array(
						'Id'              => $marchInfo['id'],
						'AttCityId'       => $marchInfo['atk_city_id'],
						'DefCityId'       => $marchInfo['def_city_id'],
						'AttCityNickName' => $marchInfo['atk_nickname'],
						'DefCityNickName' => $marchInfo['def_nickname'],
						'ActionType'      => $upData['action_type'],
						'HeroList'        => json_decode($marchInfo['hero_list'], true),
						'AttPos'          => M_MapWild::calcWildMapPosXYByNo($marchInfo['atk_pos']),
						'DefPos'          => M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']),
						'ArrivedTime'     => $upData['arrived_time'],
						'RemainingTime'   => max($upData['arrived_time'] - time(), 0),
						'WaitEndTime'     => 0,
						'ResData'         => !empty($marchInfo['award']) ? json_decode($marchInfo['award'], true) : array(),
						'Flag'            => $upData['flag'],
						'CreateAt'        => $marchInfo['create_at'],
						'BattleId'        => $marchInfo['battle_id'],
					);

					if (!$backKeepMarchId) {
						$ret1 = M_MapWild::setWildMapInfo($marchInfo['def_pos'], array('march_id' => 0));
						//Logger::debug(array(__METHOD__, 'set march id:0', $marchInfo['def_pos']));
					}


					$heroIdList = json_decode($marchInfo['hero_list'], true);
					$tmpIds     = array();
					foreach ($heroIdList as $tmpId) {
						$tmpInfo = M_Hero::getHeroInfo($tmpId);
						if ($tmpInfo['flag'] != T_Hero::FLAG_DIE) {
							$tmpIds[] = $tmpId;
						}
					}

					$troop = M_Hero::changeHeroFlag($marchInfo['atk_city_id'], $tmpIds, T_Hero::FLAG_MOVE);
					M_Sync::addQueue($marchInfo['atk_city_id'], M_Sync::KEY_MARCH_DATA, array($marchId => $syncData));

					$wildInfo = M_ColonyCity::getNoByPosNo($marchInfo['atk_city_id'], $marchInfo['def_pos']);
					$no       = 0;
					if (!empty($wildInfo)) {
						$no = $wildInfo['no'];
					}
					if (!empty($no)) {
						$msRow[$no] = array('MarchId' => $marchInfo['id'], 'MarchType' => 2);
						M_Sync::addQueue($marchInfo['atk_city_id'], M_Sync::KEY_CITY_COLONY, $msRow);
					}
					//删除城市行军记录
					$obj_ml = new M_March_List($marchInfo['def_pos']);
					$obj_ml->del($marchId);

					M_March::syncDelMarchBack($marchId, $marchInfo['def_city_id']);
				}
			}
		}

		return $ret;
	}

	/**
	 * 设置行军为占领状态
	 * @author huwei on 20120317
	 * @param array $marchInfo
	 * @return bool
	 */
	static public function setMarchHold($marchInfo) {
		$ret = false;
		if (!empty($marchInfo)) {
			$upData = array(
				'id'        => $marchInfo['id'],
				'flag'      => M_March::MARCH_FLAG_HOLD,
				'battle_id' => 0,
			);

			$ret = M_March_Info::set($upData);
			if ($ret) {
				//改变进攻方英雄的状态驻守
				$heroIdList = json_decode($marchInfo['hero_list'], true);
				$bAtkHold   = M_Hero::changeHeroFlag($marchInfo['atk_city_id'], $heroIdList, T_Hero::FLAG_HOLD);

				$syncMarchData = array(
					$marchInfo['id'] => array(
						'AttCityId' => $marchInfo['atk_city_id'],
						'DefCityId' => $marchInfo['def_city_id'],
						'AttPos'    => M_MapWild::calcWildMapPosXYByNo($marchInfo['atk_pos']),
						'DefPos'    => M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']),
						'Flag'      => M_March::MARCH_FLAG_HOLD)
				);

				M_Sync::addQueue($marchInfo['atk_city_id'], M_Sync::KEY_MARCH_DATA, $syncMarchData);
				if ($marchInfo['def_city_id'] > 0) {
					M_March::syncDelMarchBack($marchInfo['id'], $marchInfo['def_city_id']);
				}
			} else {
				Logger::error(array(__METHOD__, 'set march hold fail', $upData));
			}
		}

		return $ret;
	}

	/**
	 * 设置部队行军等待
	 * @author huwei on 20120316
	 * @param array $marchInfo
	 * @return bool
	 */
	static public function setMarchWait($marchInfo) {
		$ret = false;
		if (!empty($marchInfo)) {
			$mw = new M_March_Wait($marchInfo['def_pos']);
			$mw->add($marchInfo['id']);

			//加入到等待队列中...
			$upData = array('id' => $marchInfo['id'], 'flag' => M_March::MARCH_FLAG_WAIT);
			$ret    = M_March_Info::set($upData);

			Logger::dev("加入排队防御 #{$marchInfo['id']}DefPos#{$marchInfo['def_pos']}");

			if ($ret) {
				$syncMarchData = array(
					$marchInfo['id'] => array(
						'AttCityId' => $marchInfo['atk_city_id'],
						'DefCityId' => $marchInfo['def_city_id'],
						'AttPos'    => M_MapWild::calcWildMapPosXYByNo($marchInfo['atk_pos']),
						'DefPos'    => M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']),
						'Flag'      => M_March::MARCH_FLAG_WAIT)
				);
				Logger::dev("同步排队数据 #{$marchInfo['id']}#atk_city_id@{$marchInfo['atk_city_id']}#def_city_id@{$marchInfo['def_city_id']}" . json_encode($syncMarchData));
				M_Sync::addQueue($marchInfo['atk_city_id'], M_Sync::KEY_MARCH_DATA, $syncMarchData);

				if ($marchInfo['def_city_id'] > 0) {
					Logger::dev("同步排队数据#{$marchInfo['def_city_id']}#{$marchInfo['id']}");
					M_Sync::addQueue($marchInfo['def_city_id'], M_Sync::KEY_MARCH_DATA, $syncMarchData);
				}
			}
		}

		return $ret;
	}

	/**
	 * 设置部队行军战斗
	 * @author huwei on 20120316
	 * @param array $marchInfo
	 * @return bool
	 */
	static public function setMarchBattle($marchInfo, $battleId, $notSyncDefMarch = true) {
		$ret = false;
		if (!empty($marchInfo) && !empty($battleId)) {
			$upData = array(
				'id'        => $marchInfo['id'],
				'flag'      => M_March::MARCH_FLAG_BATTLE,
				'battle_id' => $battleId,
				'update_at' => time(),
			);

			$ret = M_March_Info::set($upData);
			if ($ret) {
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

				if ($marchInfo['def_city_id'] > 0) {
					//同步目的地城市的敌情数据
					$filter = array(
						M_War::BATTLE_TYPE_CITY,
						M_War::BATTLE_TYPE_NPC,
						M_War::BATTLE_TYPE_OCCUPIED_CITY,
						//M_War::BATTLE_TYPE_RESCUE
					);

					//自己解救 会出现 进攻和防御 坐标相同
					$isSame = $marchInfo['atk_pos'] == $marchInfo['def_pos'] ? true : false;
					if (in_array($marchInfo['action_type'], $filter) && !$isSame && $notSyncDefMarch) { //双方坐标 不相同 则同步行军记录给防御方
						M_Sync::addQueue($marchInfo['def_city_id'], M_Sync::KEY_MARCH_DATA, $syncMarchData);
					}
				}
			}
		}

		return $ret;
	}

	/**
	 * 同步删除行军标记
	 * @author huwei on 20120317
	 * @param int $marchId
	 * @param int $cityId
	 * @return bool
	 */
	static public function syncDelMarchBack($marchId, $cityId) {
		$ret    = false;
		$cityId = intval($cityId);
		if (!empty($marchId) && !empty($cityId)) {
			//行军返回数据
			$syncData = array(
				$marchId => $cityId
			);
			$ret      = M_Sync::addQueue($cityId, M_Sync::KEY_MARCH_END, $syncData);
		}
		return $ret;
	}

	/**
	 * 添加行军记录兵同步行军队列
	 * @author huwei on 20110615
	 * @param array $info 行军数据
	 * @return int
	 */
	static public function addMarchInfo($info) {
		$marchId = B_DB::instance('WarMarch')->insert($info);
		if (!empty($marchId)) {
			$marchInfo = B_DB::instance('WarMarch')->get($marchId);
			if (!empty($marchInfo)) {
				//添加行军记录到城市行军记录
				if (M_March_Info::set($marchInfo)) {
					$defCityInfo = M_City::getInfo($marchInfo['def_city_id']);
					$obj_ml      = new M_March_List($marchInfo['atk_pos']);
					$obj_ml->add($marchId); //添加自己 的行军ID
					if ($marchInfo['atk_pos'] != $marchInfo['def_pos']) {
						$obj_ml = new M_March_List($marchInfo['def_pos']);
						$obj_ml->add($marchId); //添加敌人 的行军ID
					}

					M_March_Queue::add($marchId); //加入到行军队列

				} else {
					Logger::error(array(__METHOD__, "add march fail", func_get_args()));
				}
			}
		}
		return $marchId;
	}

	/** 行军移动处理 */
	static public function run($num) {
		$now      = time();
		$marchIds = M_March_Queue::get($num);
		$total    = count($marchIds);
		echo "Total:{$total}\n";
		if (is_array($marchIds)) {
			$outLog = '';
			foreach ($marchIds as $marchId) {
				$marchInfo = M_March_Info::get($marchId);
				$outLog    = "MarchId#{$marchId}:Flag#{$marchInfo['flag']};";
				if (!empty($marchInfo['arrived_time'])) { //有到达时间
					$diff = $marchInfo['arrived_time'] - $now;
					$outLog .= "时间差#{$diff}";

					//时间差 小于2秒开始运算
					if ($diff < 1 && isset(M_March_Action::$warAction[$marchInfo['action_type']])) { //提前1s到达
						M_March_Queue::run($marchInfo);
					}
				} else {
					Logger::error(array(__METHOD__, 'err march data', $marchId, $marchInfo));
					M_March_Queue::del($marchId);
				}
				$outLog .= "\n";
				//Logger::dev($outLog);
			}
			echo $outLog;
		}

		return 3;
	}

	/**
	 * 敌情警报部队列表
	 * @author HeJunyun
	 * @param int $defCityId 防守方城市ID
	 * @return $list array
	 */
	static public function getEnemyForcesById($cityId) {
		$list = M_March::getMarchList($cityId, M_War::MARCH_OWN_DEF);
		$ret  = array();

		$tmpTypeArr = array(
			M_March::MARCH_ACTION_ATT,
			M_March::MARCH_ACTION_SCOUT,
			M_March::MARCH_ACTION_HOLD,
			M_March::MARCH_ACTION_CITY,
			M_March::MARCH_ACTION_RESCUE_CITY,
			M_March::MARCH_ACTION_BOMB
		);

		$tmpFlagArr = array(
			M_March::MARCH_FLAG_BATTLE,
			M_March::MARCH_FLAG_MOVE,
			M_March::MARCH_FLAG_WAIT,
		);
		foreach ($list as $marchId => $val) {
			if (in_array($val['action_type'], $tmpTypeArr) &&
				in_array($val['flag'], $tmpFlagArr)
			) {
				$isOk = true;
				if ($val['flag'] == M_March::MARCH_FLAG_BATTLE) {
					$bd = M_Battle_Info::get($val['battle_id']);
					if ($bd[T_Battle::CUR_OP_DEF]['CityId'] != $cityId) { //非自己的战斗
						$isOk = false;
					}
				}

				if ($isOk) {
					$ret[$marchId] = $val;
				}

			}
		}
		return $ret;
	}

	/**
	 * 获取行军记录中的 英雄列表
	 * @author HeJunyun
	 * @author huwei modify 20120107
	 * @param int $marchId 行军ID
	 * @param int $cityId 城市ID
	 * @param int $atk 是否攻击方
	 * @param array
	 */
	static public function getHeroListByMarchId($marchId, $cityId, $own = M_War::MARCH_OWN_ALL) {
		$ret = array();
		$row = M_March_Info::get($marchId);
		if (!empty($row['id'])) {
			$isOwn = false;
			if (($own & M_War::MARCH_OWN_ATK) > 0 && $row['atk_city_id'] == $cityId) {
				$isOwn = true;
			} else if (($own & M_War::MARCH_OWN_DEF) > 0 && $row['def_city_id'] == $cityId) {
				$isOwn = true;
			}
			if (isset($row['hero_list']) && $isOwn) {
				$heroList = json_decode($row['hero_list'], true); //获取行军部队信息中的指挥官集合
				foreach ($heroList as $key => $val) {
					$heroInfo = M_Hero::getHeroInfo($val); //根据指挥官ID获取详细信息
					$ret[]    = $heroInfo;
				}
			}
		}

		return $ret;
	}

	/**
	 * 构建一支行军部队
	 * @author huwei+Hejunyun
	 * @param int $attCityId 进攻方城市ID
	 * @param int $defPosInfo 防御方位置信息(如果是npc 则为0)
	 * @param int $type 行动类型
	 * @param array $heroIdList 英雄ID数组 [id1,id2,id3]
	 * @param array $marchData 行军数据 (耗费时间,消耗油,消耗粮食)
	 * @param int $autoFight 是否自动战斗
	 * @return bool
	 */
	static public function buildWarMarch($atkInfo, $defInfo, $type, $heroIdList, $marchData, $autoFight = 1) {
		$errNo = T_ErrNo::ARMY_TROOP_FAIL;
		$id    = 0;
		if (!empty($atkInfo) &&
			!empty($defInfo) &&
			!empty($heroIdList) &&
			count($marchData) == 3
		) {
			$now          = time();
			$arrived_time = $now + $marchData[0];

			if (B_Utils::isDev()) {
				$arrived_time = $now + 60; //@todo 测试 到达时间写死为60秒后
			}

			$info = array(
				'atk_city_id'  => $atkInfo['city_id'],
				'atk_nickname' => $atkInfo['nickname'],
				'def_city_id'  => $defInfo['city_id'],
				'def_nickname' => $defInfo['nickname'],
				'action_type'  => $type,
				'hero_list'    => json_encode($heroIdList),
				'atk_pos'      => $atkInfo['pos_no'],
				'def_pos'      => $defInfo['pos_no'],
				'arrived_time' => $arrived_time,
				'atk_ext'      => json_encode(array($atkInfo['face_id'], $atkInfo['gender'])),
				'def_ext'      => json_encode(array($defInfo['face_id'], $defInfo['gender'])),
				'auto_fight'   => $autoFight,
				'create_at'    => $now,
			);
			$id   = M_March::addMarchInfo($info);
			if ($id) {
				//更新英雄状态为出征
				$bTroop = M_Hero::changeHeroFlag($atkInfo['city_id'], $heroIdList, T_Hero::FLAG_MOVE, array('march_id' => $id));
				if ($bTroop) {
					//前面已做资源检测  如果这里扣除出征消耗资源失败  还是让部队出征成功
					$objPlayer = new O_Player($atkInfo['city_id']);
					$objPlayer->Res()->incr('gold', -$marchData[1]);
					$objPlayer->Res()->incr('food', -$marchData[2]);
					$objPlayer->save();

					$errNo = '';
				} else {
					$errNo = T_ErrNo::HERO_UPDATE_FLAG_FAIL;
				}
			}

			!empty($errNo) && Logger::debug(array(__METHOD__, $errNo, func_get_args()));
		}
		$data = array('ErrNo' => $errNo, 'MarchId' => $id);
		return $data;
	}

	/**
	 * 构建一支进攻据点行军部队
	 * @author huwei
	 * @param int $atkInfo 进攻方城市ID
	 * @param int $defInfo 防御方信息
	 * @param array $heroIdList 英雄ID数组 [id1,id2,id3]
	 * @param int $autoFight 是否自动战斗
	 * @return bool
	 */
	static public function buildCampaignMarch($atkInfo, $defInfo, $heroIdList, $arrivedTime, $autoFight = 1) {
		$ret = false;
		$id  = 0;
		if (!empty($atkInfo) &&
			!empty($defInfo) &&
			!empty($arrivedTime) &&
			!empty($heroIdList)
		) {
			$now  = time();
			$info = array(
				'atk_city_id'  => $atkInfo['city_id'],
				'atk_nickname' => $atkInfo['nickname'],
				'def_city_id'  => $defInfo['city_id'],
				'def_nickname' => $defInfo['nickname'],
				'action_type'  => M_March::MARCH_ACTION_CAMP,
				'hero_list'    => json_encode($heroIdList),
				'atk_pos'      => $atkInfo['pos_no'],
				'def_pos'      => $defInfo['pos_no'],
				'arrived_time' => intval($arrivedTime),
				'atk_ext'      => json_encode(array(intval($atkInfo['face_id']), intval($atkInfo['gender']))),
				'def_ext'      => json_encode(array(intval($defInfo['face_id']), intval($defInfo['gender']))),
				'auto_fight'   => $autoFight,
				'create_at'    => $now,
			);

			$id = M_March::addMarchInfo($info);
			if ($id) {
				//更新英雄状态为出征
				$ret = M_Hero::changeHeroFlag($atkInfo['city_id'], $heroIdList, T_Hero::FLAG_MOVE, array('march_id' => $id));
				if (!$ret) {
					$errNo = T_ErrNo::HERO_UPDATE_FLAG_FAIL;
					Logger::error(array(__METHOD__, "fail M_Hero::changeHeroFlag", array($atkInfo['city_id'], $heroIdList, T_Hero::FLAG_MOVE, $id)));
				}
			}
		}
		return $id;
	}
}

?>