<?php

/**
 * 战斗接口
 */
class C_Battle extends C_I {
	/**
	 * 战场初始化接口
	 * @author huwei on 20110704
	 * @param int $battleId
	 */
	public function AInit($battleId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$now = time();
		$sync = false;

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (!empty($battleId)) {
			$cityId = $cityInfo['id'];
			$BD = M_Battle_Calc::getVerifyBattleData($battleId, $cityId);
			//M_Battle_Calc::delBattleData($battleId);
			if (empty($BD)) {
				$errNo = '';

				$data = array(
					'WarStatus' => T_Battle::STATUS_END,
					'IsOwnOp' => 0,
					'CurOpLeftTime' => 0,
					'Num' => 1,
					'OpFlag' => 1,
					'IsAI' => 0,
					'OpReport' => '',
					'Err' => '',
					'ReportId' => $BD['ReportId'],
					'BId' => $battleId,
				);
			} else if ($BD['CurStatus'] == T_Battle::STATUS_END) {

				$errNo = '';
				$data = array(
					'WarStatus' => $BD['CurStatus'],
					'IsOwnOp' => 0,
					'CurOpLeftTime' => 0,
					'Num' => $BD['CurOpBoutNum'],
					'OpFlag' => 1,
					'IsAI' => 0,
					'OpReport' => '',
					'Err' => '',
					'ReportId' => $BD['ReportId'],
					'BId' => $battleId,
				);
			} else {

				M_Battle_Handler::checkStatus($BD);

				$startWaitTime = 0;
				//战斗状态
				if ($BD['CurStatus'] == T_Battle::STATUS_WAIT) {
					if ($now > $BD['StartWaitTime']) {
						$BD['CurStatus'] = T_Battle::STATUS_PROC;
						$sync = true;
					}
				}

				//获取当前操作方
				$curOp = $BD['CurOp'];
				$othOp = $curOp ^ 3;
				$isOwnOp = $BD[$curOp]['CityId'] == $cityId; //当前是否拥有操作权限

				$sync && M_Battle_Info::set($BD);

				M_Battle_Calc::setNewOpLogNum($battleId, $cityId);

				$errNo = '';
				$own = $isOwnOp ? $curOp : $othOp;
				$oth = $own ^ 3;

				$bgArr = M_Config::getVal('map_war_bg');
				$data = array(
					'BId' => $BD['Id'],
					'WarName' => $BD['MapName'],
					'WarSize' => $BD['MapSize'],
					//'WarBgNo'		=> $bgArr[$BD['MapBgNo']][1],
					'WarBgNo' => $BD['MapNo'],
					'WarMapNo' => $BD['MapNo'],
					'WarMapCell' => base64_encode(M_MapBattle::chrCellData($BD['MapCell'])),
					'WarMapSecne' => $BD['MapSecne'],
					'Weather' => $BD['Weather'],
					'WarStatus' => $BD['CurStatus'],
					'OwnPos' => M_MapWild::calcWildMapPosXYByNo($BD[$own]['Pos']),
					'OthPos' => M_MapWild::calcWildMapPosXYByNo($BD[$oth]['Pos']),
					'IsFB' => $BD['Type'] == M_War::BATTLE_TYPE_FB ? 1 : 0,
					'IsOwnOp' => $isOwnOp ? 1 : 0,
					'BattleType' => $BD['Type'],
					'CurOpLeftTime' => M_Formula::calcBattleWaitTime($now, $BD['StartWaitTime'], $BD['CurOpEndTime']),
					'CurOpBoutNum' => min($BD['CurOpBoutNum'], T_Battle::OP_BATTLE_BOUT_NUM),
					'OwnHero' => M_Battle_Calc::filterHeroInfo($BD[$own]),
					'OthHero' => M_Battle_Calc::filterHeroInfo($BD[$oth]),
				);
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 回合结束
	 * @author huwei
	 * @param int $battleId
	 */
	public function ABoutEnd($battleId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = $opLogRow = array();
		$now = time();
		$sync = $opFlag = false;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (!empty($battleId)) {
			$cityId = $cityInfo['id'];
			$BD = M_Battle_Calc::getVerifyBattleData($battleId, $cityId);
			if (!empty($BD)) {
				//获取当前操作方
				$curOp = $BD['CurOp'];
				$othOp = $curOp ^ 3;
				$isOwnOp = ($BD['CurStatus'] == T_Battle::STATUS_PROC && $BD[$curOp]['CityId'] == $cityId) ? 1 : 0;

				$err = '';
				if ($BD['CurStatus'] != T_Battle::STATUS_PROC) {
					$err = T_ErrNo::BATTLE_NOT_PROC;
					$err = '非战斗进行状态';
				} else if (!empty($BD[$curOp]['IsAI'])) {
					$err = T_ErrNo::BATTLE_NOT_M_OP;
					$err = '非手动操作状态';
				} else if (!$isOwnOp) {
					$err = T_ErrNo::BATTLE_NOT_CUR_OP;
					$err = '非当前操作方';
				} else if ($BD['CurOpBoutNum'] >= T_Battle::OP_BATTLE_BOUT_NUM) {
					$err = T_ErrNo::BATTLE_MAX_BOUT_NUM;
					$err = '回合数已最大';
				}

				//数据处理  (切换到对方操作)
				//$isOwnOp = ($BD['CurStatus'] == T_Battle::STATUS_PROC && $BD[$othOp]['CityId'] == $cityId) ? 1 : 0;

				if (empty($err)) {
					$BD['CurStatus'] = T_Battle::STATUS_PROC_WAIT;
					$oldBoutNum = $BD['CurOpBoutNum'];

					//回合数|操作方|4
					$opLogRow = array($oldBoutNum,
						$BD[$curOp]['CityId'],
						T_Battle::OP_ACT_END,
						T_Battle::OP_M);

					M_Battle_Calc::addOpLog($battleId, $opLogRow);
					$sync = true;
					$opFlag = true;
				}

				$data = array(
					'WarStatus' => $BD['CurStatus'],
					'IsOwnOp' => $isOwnOp,
					'CurOpLeftTime' => M_Formula::calcBattleWaitTime($now, $BD['StartWaitTime'], $BD['CurOpEndTime']),
					'OpInfo' => $opLogRow,
					'Num' => $BD['CurOpBoutNum'],
					'OpFlag' => $opFlag ? 1 : 0,
					'Err' => $err,
					'ReportId' => $BD['ReportId'],
				);


				$errNo = '';

			} else {
				$errNo = T_ErrNo::BATTLE_DATA_ERR;
			}

			$sync && M_Battle_Info::set($BD);
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 撤退
	 * @author huwei
	 * @param int $battleId
	 */
	public function AEscape($battleId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = $opLogRow = array();
		$sync = $opFlag = false;
		$now = time();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (!empty($battleId)) {
			$cityId = $cityInfo['id'];
			$BD = M_Battle_Calc::getVerifyBattleData($battleId, $cityId);

			if (!empty($BD)) {
				M_Battle_Handler::checkStatus($BD);

				//获取当前操作方
				$curOp = $BD['CurOp'];
				$isOwnOp = ($BD['CurStatus'] == T_Battle::STATUS_PROC && $BD[$curOp]['CityId'] == $cityId) ? 1 : 0;

				$err = '';
				if ($BD['CurStatus'] != T_Battle::STATUS_PROC) {
					$err = T_ErrNo::BATTLE_NOT_PROC;
					//$err = '非战斗进行状态';
				} else if (!empty($BD[$curOp]['IsAI'])) {
					$err = T_ErrNo::BATTLE_NOT_M_OP;
					//$err = '非手动操作状态';
				} else if (!$isOwnOp) {
					$err = T_ErrNo::BATTLE_NOT_CUR_OP;
					//$err = '非当前操作方';
				} else if ($BD['CurOpBoutNum'] > T_Battle::OP_BATTLE_BOUT_NUM) {
					$err = T_ErrNo::BATTLE_MAX_BOUT_NUM;
					//$err = '回合数已最大';
				}

				if (empty($err)) {
					//对方胜利
					$BD['CurWin'] = $curOp ^ 3;
					$BD['CurStatus'] = T_Battle::STATUS_RESULT;
					$opFlag = $sync = true;

					//回合数|操作方|5
					$opLogRow = array($BD['CurOpBoutNum'],
						$BD[$curOp]['CityId'],
						T_Battle::OP_ACT_ESC,
						T_Battle::OP_M);

					M_Battle_Calc::addOpLog($battleId, $opLogRow);

					M_Battle_Calc::calcEscapeLoss($BD);

					$sync && M_Battle_Info::set($BD);

					M_Battle_Handler::checkResult($BD);
				}

				$data = array(
					'WarStatus' => $BD['CurStatus'],
					'IsOwnOp' => $isOwnOp,
					'CurOpLeftTime' => M_Formula::calcBattleWaitTime($now, $BD['StartWaitTime'], $BD['CurOpEndTime']),
					'OpInfo' => $opLogRow,
					'Num' => $BD['CurOpBoutNum'],
					'OpFlag' => $opFlag ? 1 : 0,
					'Err' => $err,
					'ReportId' => $BD['ReportId'],
				);


				$errNo = '';
			} else {
				$errNo = T_ErrNo::BATTLE_DATA_ERR;
			}

		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 等待中
	 * @author huwei
	 * @param int $battleId
	 */
	public function AWait($battleId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$sync = false;
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (!empty($battleId)) {
			$cityId = $cityInfo['id'];
			$BD = M_Battle_Calc::getVerifyBattleData($battleId, $cityId);

			//获取当前操作方
			if (!empty($BD)) {
				M_Battle_Handler::checkStatus($BD);

				//依靠前端来 控制 战斗进程
				//M_Battle_Handler::runData($BD['Id']);

				$curOp = $BD['CurOp'];

				$isOwnOp = ($BD['CurStatus'] == T_Battle::STATUS_PROC && $BD[$curOp]['CityId'] == $cityId) ? 1 : 0;

				$data = array(
					'WarStatus' => $BD['CurStatus'],
					'IsOwnOp' => $isOwnOp,
					'CurOpLeftTime' => M_Formula::calcBattleWaitTime($now, $BD['StartWaitTime'], $BD['CurOpEndTime']),
					'Num' => $BD['CurOpBoutNum'],
					'OpFlag' => 1,
					'IsAI' => $BD[$curOp]['IsAI'],
					'OpReport' => M_Battle_Calc::getOpLog($battleId, $cityId),
					'Err' => '',
					'ReportId' => $BD['ReportId'],
				);

				$errNo = '';
			} else {
				$errNo = T_ErrNo::BATTLE_DATA_ERR;
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 改变AI模式
	 * @param int $battleId
	 * @param int $aiOp 手动 0 自动 1
	 */
	public function AChangeAI($battleId = 0, $aiOp = T_Battle::OP_M) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = $opLogRow = array();
		$sync = $opFlag = false;
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (!empty($battleId) && in_array($aiOp, array(T_Battle::OP_A, T_Battle::OP_M))
		) {
			$cityId = $cityInfo['id'];
			$BD = M_Battle_Calc::getVerifyBattleData($battleId, $cityId);

			if (!empty($BD)) {
				M_Battle_Handler::checkStatus($BD);

				$curOp = $BD['CurOp'];
				$isOwnOp = ($BD['CurStatus'] == T_Battle::STATUS_PROC && $BD[$curOp]['CityId'] == $cityId) ? 1 : 0;
				$curOp = $isOwnOp ? $BD['CurOp'] : $BD['CurOp'] ^ 3;

				$err = '';
				if ($BD['CurStatus'] != T_Battle::STATUS_PROC) {
					$err = T_ErrNo::BATTLE_NOT_PROC;
					//$err = '非战斗进行状态';
				} else if ($BD[$curOp]['IsAI'] == $aiOp) {
					$err = T_ErrNo::BATTLE_NOT_CHANGE_AI;
					//$err = '操作状态无变化';
				} else if ($BD['CurOpBoutNum'] >= T_Battle::OP_BATTLE_BOUT_NUM) {
					$err = T_ErrNo::BATTLE_MAX_BOUT_NUM;
					//$err = '回合数已最大';
				}

				//战斗进行中
				if (empty($err)) {
					$BD[$curOp]['IsAI'] = abs($BD[$curOp]['IsAI'] - 1);

					if ($BD[$curOp]['IsAI'] == T_Battle::OP_A) {
						$heroList = $BD[$curOp]['HeroPosData'];
						$n = 0;
						foreach ($heroList as $kId => $vData) {
							//检测是否被操作过
							($vData[1] > 0) && $n++;
						}
						if (count($heroList) == $n) {
							$BD[$curOp]['PlayFinish'] = 1;
						}
					}

					$opFlag = $sync = true;
					//回合数|操作方|6|当前AI模式
					$opLogRow = array($BD['CurOpBoutNum'],
						$BD[$curOp]['CityId'],
						T_Battle::OP_ACT_AI,
						T_Battle::OP_M,
						$BD[$curOp]['IsAI']);

					M_Battle_Info::set($BD);
				}

				$data = array(
					'WarStatus' => $BD['CurStatus'],
					'IsOwnOp' => $isOwnOp,
					'CurOpLeftTime' => M_Formula::calcBattleWaitTime($now, $BD['StartWaitTime'], $BD['CurOpEndTime']),
					'Num' => $BD['CurOpBoutNum'],
					'OpInfo' => $opLogRow,
					'OpFlag' => $opFlag ? 1 : 0,
					'Err' => $err,
					'ReportId' => $BD['ReportId'],
				);


			} else {
				$errNo = T_ErrNo::BATTLE_DATA_ERR;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 使用计策
	 * @param int $battleId 战斗id
	 * @param int $ployId 计策ID
	 */
	public function AUsePloy($battleId = 0, $ployId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = $opLogRow = array();
		$sync = $opFlag = false;
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (!empty($battleId) ) {
			$cityId = $cityInfo['id'];
			$BD = M_Battle_Calc::getVerifyBattleData($battleId, $cityId);

			if (!empty($BD)) {
				M_Battle_Handler::checkStatus($BD);

				$curOp = $BD['CurOp'];
				$isOwnOp = ($BD['CurStatus'] == T_Battle::STATUS_PROC && $BD[$curOp]['CityId'] == $cityId) ? 1 : 0;

				$err = '';
				if ($BD['CurStatus'] != T_Battle::STATUS_PROC) {
					$err = T_ErrNo::BATTLE_NOT_PROC;
					//$err = '非战斗进行状态';
				} else if (!empty($BD[$curOp]['IsAI'])) {
					$err = T_ErrNo::BATTLE_NOT_M_OP;
					//$err = '非手动操作状态';
				} else if (!$isOwnOp) {
					$err = T_ErrNo::BATTLE_NOT_CUR_OP;
					//$err = '非当前操作方';
				} else if ($BD['CurOpBoutNum'] > T_Battle::OP_BATTLE_BOUT_NUM) {
					$err = T_ErrNo::BATTLE_MAX_BOUT_NUM;
					//$err = '回合数已最大';
				}

				if (empty($err)) {
					//@todo 回合数|操作方|3|坐标(0全部)|计策效果定义|效果值|持续回合数
					$opLogRow = array($BD['CurOpBoutNum'],
						$BD[$curOp]['CityId'],
						T_Battle::OP_ACT_ATK,
						T_Battle::OP_M);
					M_Battle_Calc::addOpLog($battleId, $opLogRow);
					$opFlag = true;
				}

				$data = array(
					'WarStatus' => $BD['CurStatus'],
					'IsOwnOp' => $isOwnOp,
					'CurOpLeftTime' => M_Formula::calcBattleWaitTime($now, $BD['StartWaitTime'], $BD['CurOpEndTime']),
					'Num' => $BD['CurOpBoutNum'],
					'OpInfo' => $opLogRow,
					'OpFlag' => $opFlag ? 1 : 0,
					'Err' => $err,
				);


			} else {
				$errNo = T_ErrNo::BATTLE_DATA_ERR;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 移动
	 * @param int $battleId
	 * @param int $heroId
	 * @param string $posList 坐标列表(x1_y1,x2_y2)
	 */
	public function AMove($battleId = 0, $heroId = 0, $posList = '') {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = $opLogRow = array();
		$sync = $opFlag = false;
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (!empty($battleId) && !empty($heroId) && !empty($posList)) {
			$moveList = explode(',', $posList);
			$moveNum = count($moveList);
			$cityId = $cityInfo['id'];
			$BD = M_Battle_Calc::getVerifyBattleData($battleId, $cityId);


			if (!empty($BD) && $moveNum > 0) {

				list($maxX, $maxY) = $BD['MapSize'];
				$posErr = false;
				foreach ($moveList as $val) {
					list($x, $y) = explode('_', $val);
					if ($x < 0 || $y < 0 || $x >= $maxX || $y >= $maxY) {
						$posErr = true;
						break;
					}
				}

				M_Battle_Handler::checkStatus($BD);

				$errNo = T_ErrNo::BATTLE_DATA_ERR;
				//获取当前操作方
				$curOp = $BD['CurOp'];

				$isOwnOp = ($BD['CurStatus'] == T_Battle::STATUS_PROC && $BD[$curOp]['CityId'] == $cityId) ? 1 : 0; //当前是否拥有操作权限

				$heroPosData = isset($BD[$curOp]['HeroPosData'][$heroId]) ? $BD[$curOp]['HeroPosData'][$heroId] : array('', '', '');
				list($heroPos, $heroOpFlag, $SkillEffect) = $heroPosData;

				$err = '';
				if ($posErr) {
					$err = T_ErrNo::BATTLE_NOT_MOVE;
				} else if ($BD['CurStatus'] != T_Battle::STATUS_PROC) {
					$err = T_ErrNo::BATTLE_NOT_PROC;
					//$err = '非战斗进行状态';
				} else if ($BD['CurOpBoutNum'] > T_Battle::OP_BATTLE_BOUT_NUM) {
					$err = T_ErrNo::BATTLE_MAX_BOUT_NUM;
					//$err = '回合数已最大';
				} else if (!$isOwnOp) {
					$err = T_ErrNo::BATTLE_NOT_CUR_OP;
					//$err = '非当前操作方';
				} else if (!empty($BD[$curOp]['IsAI'])) {
					$err = T_ErrNo::BATTLE_NOT_M_OP;
					//$err = '非手动操作状态';
				} else if (empty($heroPos)) {
					$err = T_ErrNo::BATTLE_DATA_ERR;
					//$err = '战场数据错误';
				} else if (($heroOpFlag & T_Battle::OP_HERO_MOVE_FLAG) > 0) {
					$err = T_ErrNo::BATTLE_NOT_MOVE_LIST;
					//$err = '已移动操作';
				}

				$errNo = $err;
				if (empty($err)) {

					$heroInfo = $BD[$curOp]['HeroDataList'][$heroId];

					//移动范围是否正常
					$wp = new M_Battle_Path($BD['MapSize'], $BD['MapCell']);
					$isMove = $wp->checkMovePath($heroPos, $moveList, $heroInfo['move_type'], $heroInfo['move_range']);

					$moveEPos = $moveList[$moveNum - 1]; //最后坐标

					if (empty($moveEPos)) {
						$err = T_ErrNo::BATTLE_NOT_MOVE_POS;
						//$err = '无可移动坐标';
					} else if (!$isMove) {
						$err = T_ErrNo::BATTLE_NOT_MOVE;
						//$err = '不可移动';
					} else if ($heroInfo['move_range'] < $moveNum) {
						$err = T_ErrNo::BATTLE_NOT_MOVE_RANGE;
						//$err = '不在移动范围内';
					}


					if (empty($err)) {
						//更新英雄坐标
						list($oldPos, $oldOpFlag, $SkillEffect) = $BD[$curOp]['HeroPosData'][$heroId];

						$BD['MapCell'][$moveEPos] = isset($BD['MapCell'][$oldPos]) ? $BD['MapCell'][$oldPos] : array($curOp, $heroId);
						unset($BD['MapCell'][$oldPos]);

						//更新移动队列
						$BD[$curOp]['HeroPosData'][$heroId] = array($moveEPos, $oldOpFlag + T_Battle::OP_HERO_MOVE_FLAG, $SkillEffect);
						$opFlag = $sync = true;

						//回合数|操作方|1|英雄ID|坐标数组
						$opLogRow = array($BD['CurOpBoutNum'],
							$BD[$curOp]['CityId'],
							T_Battle::OP_ACT_MOVE,
							T_Battle::OP_M,
							$heroId,
							$moveList);

						M_Battle_Calc::addOpLog($battleId, $opLogRow);

						$errNo = '';
						//更新缓存
						$sync && M_Battle_Info::set($BD);
					}
				}
				$data = array(
					'WarStatus' => $BD['CurStatus'],
					'IsOwnOp' => $isOwnOp ? 1 : 0,
					'CurOpLeftTime' => M_Formula::calcBattleWaitTime($now, $BD['StartWaitTime'], $BD['CurOpEndTime']),
					'Num' => $BD['CurOpBoutNum'],
					'OpInfo' => $opLogRow,
					'OpFlag' => $opFlag ? 1 : 0,
					'Err' => $err,
				);

			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 攻击
	 * @param int $battleId
	 * @param int $heroId1 攻击方英雄ID
	 * @param int $defHeroId 防御方英雄ID
	 */
	public function AAttack($battleId = 0, $heroId1 = 0, $defHeroId = 0) {

		$errNo = T_ErrNo::BATTLE_DATA_ERR;
		$data = $opInfo = array();
		$sync = $opFlag = false;
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (!empty($battleId) && !empty($heroId1) && !empty($defHeroId)) {
			$cityId = $cityInfo['id'];
			$BD = M_Battle_Calc::getVerifyBattleData($battleId, $cityId);

			if (!empty($BD)) {
				M_Battle_Handler::checkStatus($BD);

				//获取当前操作方
				$curOp = $BD['CurOp'];
				//获取被攻击方（按位异或） 将把 不同的位设为 1
				$defOp = $BD['CurOp'] ^ 3;
				$isOwnOp = ($BD['CurStatus'] == T_Battle::STATUS_PROC && $BD[$curOp]['CityId'] == $cityId) ? 1 : 0; //当前是否拥有操作权限

				$heroId2 = $defHeroId;
				list($defPos, $defOpAct, $defSkillEffect) = isset($BD[$defOp]['HeroPosData'][$heroId2]) ? $BD[$defOp]['HeroPosData'][$heroId2] : array('', '', '');
				$heroInfo1 = isset($BD[$curOp]['HeroDataList'][$heroId1]) ? $BD[$curOp]['HeroDataList'][$heroId1] : array();
				list($curPos, $actOpAct, $atkSkillEffect) = isset($BD[$curOp]['HeroPosData'][$heroId1]) ? $BD[$curOp]['HeroPosData'][$heroId1] : array('', '', '');

				$err = '';
				if ($BD['CurStatus'] != T_Battle::STATUS_PROC) {
					$err = T_ErrNo::BATTLE_NOT_PROC;
					//$err = '非战斗进行状态';
				} else if (!empty($BD[$curOp]['IsAI'])) {
					$err = T_ErrNo::BATTLE_NOT_M_OP;
					//$err = '非手动操作状态';
				} else if (!$isOwnOp) {
					$err = T_ErrNo::BATTLE_NOT_CUR_OP;
					//$err = '非当前操作方';
				} else if ($BD['CurOpBoutNum'] > T_Battle::OP_BATTLE_BOUT_NUM) {
					$err = T_ErrNo::BATTLE_MAX_BOUT_NUM;
					//$err = '回合数已最大';
				} else if (($actOpAct & T_Battle::OP_HERO_ATK_FLAG) > 0) {
					$err = T_ErrNo::BATTLE_NOT_ATK_LIST;
					//$err = '不在可攻击列表';
				} else if (empty($heroInfo1)) {
					$err = T_ErrNo::HERO_NO_EXIST;
				}

				if (empty($err)) {
					//防御方坐标
					$wp = new M_Battle_Path($BD['MapSize'], $BD['MapCell']);
					$rangeList = $wp->getAtkRange($curPos, array($heroInfo1['shot_range_min'], $heroInfo1['shot_range_max']));

					//攻击范围是否正常
					if (M_Battle_AI::checkAtkRange($defPos, $rangeList)) {
						$bAI = new M_Battle_AI($BD);
						$atkData = $bAI->atk($heroId1, $heroId2, $curOp, T_Battle::OP_M);
						$BD = $bAI->getData();
						$sync = $opFlag = true;
						$opInfo = $atkData['opLog'];

						M_Battle_Handler::checkStatus($BD);
						$isOwnOp = ($BD['CurStatus'] == T_Battle::STATUS_PROC) ? 1 : 0; //当前是否拥有操作权限

					} else {
						$err = T_ErrNo::BATTLE_NOT_ATK_RANG;
						//$err = '不在攻击范围';
					}
				}

				$data = array(
					'WarStatus' => $BD['CurStatus'],
					'IsOwnOp' => $isOwnOp ? 1 : 0,
					'CurOpLeftTime' => M_Formula::calcBattleWaitTime($now, $BD['StartWaitTime'], $BD['CurOpEndTime']),
					'Num' => $BD['CurOpBoutNum'],
					'OpInfo' => $opInfo,
					'OpFlag' => $opFlag ? 1 : 0,
					'Err' => $err,
					'ReportId' => $BD['ReportId'],
				);

				//更新缓存
				$sync && M_Battle_Info::set($BD);


				$errNo = '';
			}
		}

		return B_Common::result($errNo, $data);
	}

	public function APlayOver($battleId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$opFlag = false;
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (!empty($battleId)) {
			$cityId = $cityInfo['id'];
			$BD = M_Battle_Calc::getVerifyBattleData($battleId, $cityId);

			//获取当前操作方
			if (!empty($BD)) {
				$curOp = $BD['CurOp'];
				$othOp = $curOp ^ 3;

				$isCur = ($BD[$curOp]['CityId'] == $cityId) ? 1 : 0;

				if (in_array($BD['CurStatus'], array(T_Battle::STATUS_PROC, T_Battle::STATUS_PROC_WAIT))) {
					if ($isCur) {
						$BD[$curOp]['PlayFinish'] = 1;
						$opFlag = true;
					} else if (!$isCur) {
						$boutTime = !empty($BD[T_Battle::CUR_OP_DEF]['CityId']) ? T_Battle::OP_BATTLE_BOUT_PVP_TIME : T_Battle::OP_BATTLE_BOUT_PVE_TIME;
						$aiAutoChangeTime = $boutTime - 2;
						if ($now > $BD['CurOpEndTime'] - $aiAutoChangeTime) {
							$BD[$othOp]['PlayFinish'] = 1;
							$opFlag = true;
						}

					}
				}

				$data = array(
					'WarStatus' => $BD['CurStatus'],
					'IsOwnOp' => $isCur,
					'CurOpLeftTime' => M_Formula::calcBattleWaitTime($now, $BD['StartWaitTime'], $BD['CurOpEndTime']),
					'Num' => $BD['CurOpBoutNum'],
					'OpFlag' => $opFlag ? 1 : 0,
					'OpReport' => '',
					'Err' => '',
					'ReportId' => $BD['ReportId'],
				);

				if ($opFlag) {
					M_Battle_Info::set($BD);
				}


				$errNo = '';
			} else {
				$errNo = T_ErrNo::BATTLE_DATA_ERR;
			}
		}

		return B_Common::result($errNo, $data);
	}
}

?>