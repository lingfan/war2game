<?php

class M_Battle_Handler {
	const QUEUE_CALC_AI = 'QUEUE_CALC_AI';

	/**
	 * 初始化战斗信息
	 * @author huwei on 20110715
	 * @param int $BID
	 * @return array
	 */
	static public function initData($info) {
		$BID = isset($info['id']) ? $info['id'] : 0;
		$now = time();
		$BD  = M_Battle_Info::get($BID);
		//Logger::debug(array(__METHOD__, $BID, $BD['Id'], $info['id']));
		if (empty($BD['Id']) && !empty($info)) {
			$mapData       = $info['map_data'];
			$atkHeroIdList = $mapData['atkHeroPos'];

			if (!empty($info['def_city_id'])) {
				$startTime = $now + T_Battle::OP_BATTLE_INIT_WAIT_PVP_TIME;
				$boutTime  = T_Battle::OP_BATTLE_BOUT_PVP_TIME;
			} else {
				$startTime = $now + T_Battle::OP_BATTLE_INIT_WAIT_PVE_TIME;
				$boutTime  = T_Battle::OP_BATTLE_BOUT_PVE_TIME;
			}

			$defHeroIdList = $mapData['defHeroPos'];

			$curOpEndTime = $startTime + $boutTime;

			$atkHeroPosData = array();
			foreach ($mapData['atkHeroPos'] as $atkId => $atkPos) {
				$atkHeroPosData[$atkId] = array($atkPos, T_Battle::OP_HERO_INIT_FLAG, array());
			}

			$defHeroPosData = array();
			foreach ($mapData['defHeroPos'] as $defId => $defPos) { //位置,战场状态,技能效果array(效果定义=>array(回合数,效果值))
				$defHeroPosData[$defId] = array($defPos, T_Battle::OP_HERO_INIT_FLAG, array());
			}

			$BD = array(
				'Id'                 => (int)$info['id'],
				'StartTime'          => (int)$info['create_at'], //战斗开始时间
				'Type'               => (int)$info['type'], //战斗类型
				'Weather'            => (int)$info['weather'], //天气类型
				'Terrian'            => (int)$info['terrian'], //地形类型
				'AtkMarchId'         => isset($info['atk_march_id']) ? (int)$info['atk_march_id'] : 0, //行军ID
				'DefMarchId'         => isset($info['def_march_id']) ? (int)$info['def_march_id'] : 0, //行军ID
				'StartWaitTime'      => (int)$startTime, //战斗开始时间
				'CurStatus'          => (int)$info['status'], //战斗状态
				'CurOp'              => (int)$info['cur_op'], //当前操作方
				'CurOpEndTime'       => (int)$curOpEndTime, //当前操作结束时间
				'CurOpBoutNum'       => (int)$info['cur_op_bout_num'], //当前操作剩余回合数
				'CurWin'             => 0, //获胜方
				'DefNpcId'           => (int)$info['def_npc_id'], //NPC id
				'AtkPos'             => $info['atk_pos'], //攻击方坐标(洲编号坐标X坐标Y)
				'DefPos'             => $info['def_pos'], //防御方坐标(洲编号坐标X坐标Y)
				'MapName'            => $mapData['mapName'], //战场名称
				'MapSize'            => $mapData['mapSize'], //战斗地图最大行列array(X,Y)
				'MapBgNo'            => (int)$mapData['mapBgNo'], //战场背景编号
				'MapNo'              => (int)$mapData['mapNo'], //战场地图编号
				'MapCell'            => $mapData['mapCell'], //战斗地图数据 array(坐标'x_y'=>标志物array(cell_id))
				'MapSecne'           => $mapData['mapSecne'], //战斗地图装饰物数据
				'CalcResult'         => 0, //结果计算
				'ReportId'           => 0, //战报ID
				'InAIQueueTime'      => 0, //加入AI队列时间

				'ArmyData'           => $info['army_data'],
				'WeaponData'         => $info['weapon_data'],

				T_Battle::CUR_OP_ATK => array(
					'CityId'          => (int)$info['atk_city_id'],
					'Nickname'        => $info['atk_nickname'],
					'FaceId'          => $info['atk_face_id'],
					'Gender'          => (int)$info['atk_gender'],
					'Pos'             => $info['atk_pos'],
					'IsAI'            => (int)$info['atk_is_ai'], //攻击方是否是AI操作
					'CalcAI'          => 0, //AI是否计算
					'HeroPosData'     => $atkHeroPosData, //攻击方英雄坐标 [英雄ID=>坐标x_y]
					'ViewRange'       => array(), //攻击方视野范围
					'HeroDataList'    => $info['atk_hero_data'], //攻击方英雄数据
					'ChangeBoutTime'  => 0, //AI切换回合时间
					'SkillEffect'     => array(), //技能效果
					'PlayFinish'      => 0, //动画播放完成
					'InitHeroPosData' => $atkHeroPosData,
				),

				T_Battle::CUR_OP_DEF => array(
					'CityId'          => (int)$info['def_city_id'],
					'Nickname'        => $info['def_nickname'],
					'FaceId'          => $info['def_face_id'],
					'Gender'          => (int)$info['def_gender'],
					'Pos'             => $info['def_pos'],
					'IsAI'            => (int)$info['def_is_ai'], //防守方是否是AI操作
					'CalcAI'          => 0, //AI是否计算
					'HeroPosData'     => $defHeroPosData, //防守方英雄坐标 [英雄ID=>坐标x_y]
					'ViewRange'       => array(), //防守方视野范围坐标
					'HeroDataList'    => $info['def_hero_data'], //防守方英雄数据
					'ChangeBoutTime'  => 0, //AI切换回合时间
					'SkillEffect'     => array(), //技能效果array(['效果值1','回合数s,e','英雄ID1,英雄ID2,'],..)
					'PlayFinish'      => 0, //动画播放完成
					'InitHeroPosData' => $defHeroPosData,
				),

				'LastOpTime'         => $now,
			);

			$tmpLogArr                                    = $BD;
			$logArr[T_Battle::CUR_OP_ATK]['HeroDataList'] = $tmpLogArr[T_Battle::CUR_OP_ATK]['HeroDataList'];
			$logArr[T_Battle::CUR_OP_ATK]['HeroPosData']  = $tmpLogArr[T_Battle::CUR_OP_ATK]['HeroPosData'];
			$logArr['atk']                                = $logArr[T_Battle::CUR_OP_ATK];
			unset($tmpLogArr[T_Battle::CUR_OP_ATK]);
			$logArr[T_Battle::CUR_OP_DEF]['HeroDataList'] = $tmpLogArr[T_Battle::CUR_OP_DEF]['HeroDataList'];
			$logArr[T_Battle::CUR_OP_DEF]['HeroPosData']  = $tmpLogArr[T_Battle::CUR_OP_DEF]['HeroPosData'];
			$logArr['def']                                = $logArr[T_Battle::CUR_OP_DEF];
			unset($tmpLogArr[T_Battle::CUR_OP_DEF]);
			$logArr['MapCell'] = $tmpLogArr['MapCell'];
			$logArr['base']    = $tmpLogArr;
			unset($tmpLogArr);
			$log = "初始化缓存数据\n" . json_encode($logArr);
			Logger::battle($log, $BD['Id'], 0);

			//根据战斗类型添加到对应的等待战斗队列
			$WBPQ = new M_Battle_QueueHandler();
			$WBPQ->set($BD['Id']);

			$ret = M_Battle_Info::set($BD);

			$atkPosNo = M_MapWild::calcWildMapPosXYByNo($BD['AtkPos']);
			$defPosNo = ($BD['Type'] == M_War::BATTLE_TYPE_FB) ? M_Formula::calcParseFBNo($BD['DefPos']) : M_MapWild::calcWildMapPosXYByNo($BD['DefPos']);

			//改变进攻方英雄的状态出战
			$tmpHeroIdList = array_keys($atkHeroIdList);
			$bAtkTroop     = M_Hero::changeHeroFlag($info['atk_city_id'], $tmpHeroIdList, T_Hero::FLAG_WAR);

			//防御方城市ID 不为空
			if ($info['def_city_id'] > 0) {
				//改变防守方英雄的状态出战
				$tmpHeroIdList = array_keys($defHeroIdList);
				$bDefTroop     = M_Hero::changeHeroFlag($info['def_city_id'], $tmpHeroIdList, T_Hero::FLAG_WAR);
			}

			if ($BD['AtkMarchId'] > 0) {
				//设置发生在当前坐标的战斗ID
				$mw = new M_March_Wait($BD['DefPos']);
				$mw->setBattleId($BD['Id']);

				$marchInfo = array(
					'id'          => $BD['AtkMarchId'],
					'atk_city_id' => $info['atk_city_id'],
					'def_city_id' => $info['def_city_id'],
					'atk_pos'     => $BD['AtkPos'],
					'def_pos'     => $BD['DefPos'],
					'action_type' => $BD['Type'],
				);
				//Logger::dev("Set March Battle#".json_encode($marchInfo));
				$bUpFlag = M_March::setMarchBattle($marchInfo, $BID);
				//Logger::debug(array(__METHOD__, $bUpFlag, $BID));
				//Logger::debug($marchInfo);
				if ($BD['DefMarchId'] > 0) {
					$defMarchInfo = M_March_Info::get($BD['DefMarchId']);
					if ($defMarchInfo['flag'] == M_March::MARCH_FLAG_HOLD) {
						$bUpFlag = M_March::setMarchBattle($defMarchInfo, $BID, false);
					}
				}
			}

			//战斗开始同步数据
			M_March::syncMarchStart($BD);

			//添加正在城市战斗数据
			M_Battle_List::addBattleIdByCity($info['atk_city_id'], $BID);
			if (!empty($info['def_city_id'])) {
				//如果攻击玩家
				//添加正在城市战斗数据
				M_Battle_List::addBattleIdByCity($info['def_city_id'], $BID);
			}
		}

		return $BD;
	}

	/**
	 * 自动操作战斗
	 * @author huwei
	 * @param int $BID 战斗ID
	 * @return bool
	 */
	static public function runData($BID) {
		$now = time();
		$BD  = M_Battle_Info::get($BID);

		if (!empty($BD['Id'])) {
			$t         = $BD['CurOpEndTime'] - time();
			$curOp     = $BD['CurOp'];
			$othOp     = $curOp ^ 3;
			$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId'];
			$defCityId = $BD[T_Battle::CUR_OP_DEF]['CityId'];

			//当前玩家是否在线
			$curIsOL = M_Battle_Calc::isViewOL($atkCityId, $BD['Id']) ? 1 : 0;
			//对方玩家是否在线
			$othIsOL = M_Battle_Calc::isViewOL($defCityId, $BD['Id']) ? 1 : 0;

			$log1 = "在线#{$curIsOL}-{$othIsOL}";
			$log2 = "播放#{$BD[T_Battle::CUR_OP_ATK]['PlayFinish']}-{$BD[T_Battle::CUR_OP_DEF]['PlayFinish']}";
			$log  = "{$BID}>>>状态:" . $BD['CurStatus'] . ", AI:" . $BD[$curOp]['IsAI'] . ", 操作方:" . $curOp . ", 回合数:" . $BD['CurOpBoutNum'] . ", 剩余时间:" . $t . ", {$log1},{$log2}";
			Logger::battle($log, $BID, $BD['CurOpBoutNum']);

			if ($BD['CurStatus'] == T_Battle::STATUS_PROC && $BD[$curOp]['CalcAI'] == 1) {
				$BD['CurStatus'] = T_Battle::STATUS_PROC_WAIT;
				Logger::battle("切换状态到等待[{$BD['CurStatus']}]", $BID, $BD['CurOpBoutNum']);
			}

			$sync = M_Battle_Handler::checkStatus($BD);

			$joinQueue = false;

			$boutTime = !empty($defCityId) ? T_Battle::OP_BATTLE_BOUT_PVP_TIME : T_Battle::OP_BATTLE_BOUT_PVE_TIME;

			switch ($BD['CurStatus']) {
				case T_Battle::STATUS_PROC:
					$calcAI = true;
					//如果ai方是 玩家 等待5秒 计算ai
					if (!empty($BD[$curOp]['CityId'])) {
						$calcAI = ($BD['CurOpEndTime'] - $now) < ($boutTime - 5);
					}

					if (isset($BD['Id']) &&
						$BD[$curOp]['IsAI'] &&
						$BD[$curOp]['CalcAI'] == 0 &&
						$now < ($BD['CurOpEndTime'] - 10) && //最后10s不进行AI运算
						$calcAI
					) {
						if ($BD['InAIQueueTime'] == 0 ||
							$now > ($BD['InAIQueueTime'] + $boutTime)
						) {
							$BD['InAIQueueTime'] = $now;
							$joinQueue           = true;
						}
					}
					Logger::battle("CalcAI#{$BD[$curOp]['CalcAI']}-InAIQueueTime#{$BD['InAIQueueTime']}", $BID, $BD['CurOpBoutNum']);

					break;
				case T_Battle::STATUS_RESULT:
					if ($BD['InAIQueueTime'] == 0 ||
						$now > ($BD['InAIQueueTime'] + $boutTime)
					) {
						$BD['InAIQueueTime'] = $now;
						$joinQueue           = true;
						Logger::battle("RESULT:InAIQueueTime#{$BD['InAIQueueTime']}", $BID, $BD['CurOpBoutNum']);
					}
					break;
			}

			if ($joinQueue) {
				$WBAIQ = new M_Battle_QueueAI();
				$ret   = $WBAIQ->aiSet($BD['Id']);
				$ret   = M_Battle_Info::set($BD);
			}
		}
		return $BD;
	}

	/**
	 * 检查战斗运行的状态
	 * @author huwei
	 * @param array $BD 战斗数据[引用变量]
	 */
	static public function checkStatus(&$BD) {
		//胜利条件 无英雄数据
		$sync     = false;
		$isChange = false;

		if (!empty($BD) && isset($BD['Id'])) {
			$curOpBoutNum = $BD['CurOpBoutNum'];
			$now          = time();
			$curOp        = $BD['CurOp'];
			$othOp        = $curOp ^ 3;

			$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId'];
			$defCityId = $BD[T_Battle::CUR_OP_DEF]['CityId'];

			$boutTime = !empty($defCityId) ? T_Battle::OP_BATTLE_BOUT_PVP_TIME : T_Battle::OP_BATTLE_BOUT_PVE_TIME;

			//当前玩家是否在线,npc为不在线
			$curIsOL = M_Battle_Calc::isViewOL($BD[$curOp]['CityId'], $BD['Id']);
			//对方玩家是否在线
			$othIsOL = M_Battle_Calc::isViewOL($BD[$othOp]['CityId'], $BD['Id']);

			//切换战场等待操作
			if ($BD['CurStatus'] == T_Battle::STATUS_WAIT && $now >= $BD['StartWaitTime']) {
				$BD['CurStatus'] = T_Battle::STATUS_PROC;
				$sync            = true;
			} else if ($BD['CurStatus'] == T_Battle::STATUS_PROC_WAIT) {
				$runChange     = false;
				$allPlayFinish = $BD[T_Battle::CUR_OP_ATK]['PlayFinish'] == 1 && $BD[T_Battle::CUR_OP_DEF]['PlayFinish'] == 1;

				//等待时间超过5s
				if ($now > $BD['CurOpEndTime'] + 5) {
					$runChange = true;
				} else if ($BD[$curOp]['IsAI'] && $BD[$othOp]['IsAI']) { //全AI模式
					if (!$curIsOL && !$othIsOL) { //双方同时不在线 则按自动切换时间
						//第10秒进行 ai运算
						$aiAutoChangeTime = $boutTime - 10;
						if ($now > $BD['CurOpEndTime'] - $aiAutoChangeTime) {
							$runChange = true;
						}
					} else { //双方或一方 有人在线
						if ($allPlayFinish) {
							//都已播放完成
							$runChange = true;
						} else if ($BD[$curOp]['PlayFinish'] == 1 && !$othIsOL) {
							//如果当前操作方播放完成 、对方不在线
							$runChange = true;
						} else if ($BD[$othOp]['PlayFinish'] == 1 && !$curIsOL) {
							//如果对方播放完成 、当前操作方不在线
							$runChange = true;
						}
					}
				} else { //某一方不为AI 或 都不为 AI模式
					if (!$curIsOL && !$othIsOL) { //双方同时不在线 则按正常回合结束时间
						if ($now > $BD['CurOpEndTime']) {
							$runChange = true;
						}
					} else { //双方或一方 有人在线
						if ($allPlayFinish) {
							//都已播放完成
							$runChange = true;
						} else if ($BD[$curOp]['PlayFinish'] == 1 && !$othIsOL) {
							//如果当前操作方播放完成 、对方不在线
							$runChange = true;
						} else if ($BD[$othOp]['PlayFinish'] == 1 && !$curIsOL) {
							//如果对方播放完成 、当前操作方不在线
							$runChange = true;
						}
					}
				}

				if ($runChange) {
					//如果对方在线 则等待动画播放完成
					Logger::battle("CurStatus:{$BD['CurStatus']}ATK:{$BD[T_Battle::CUR_OP_ATK]['PlayFinish']}DEF:{$BD[T_Battle::CUR_OP_DEF]['PlayFinish']}", $BD['Id'], $BD['CurOpBoutNum']);

					$BD['CurStatus'] = T_Battle::STATUS_PROC;

					//切换回合操作
					if ($BD['CurOpBoutNum'] < T_Battle::OP_BATTLE_BOUT_NUM) {
						//切换到对方 更新对方的数据
						//1^3=2  & 2^3=1
						$defOp    = $curOp ^ 3;
						$heroList = $BD[$curOp]['HeroPosData'];
						$isOp     = false;
						foreach ($heroList as $kId => $vData) {
							//检测是否被操作过
							list($pos, $act) = $vData;
							if ($act != 0) {
								$isOp = true;
								break;
							}
						}

						if (!$BD[$curOp]['IsAI'] && ($now > $BD['CurOpEndTime']) && !$isOp) { //当前操作方手动模式  回合内无操作 设置当前操作方 为ai模式
							$BD[$curOp]['IsAI'] = 1;
						}

						$BD['CurOp']        = $defOp;
						$BD['CurOpEndTime'] = $now + $boutTime;
						$BD['CurOpBoutNum'] += 1;
						//切换AI操作标记
						$BD[$defOp]['CalcAI'] = 0;

						//更新战斗英雄列表
						$BD[$defOp]['HeroPosData'] = M_Battle_Calc::updateHeroFlag($BD[$defOp]['HeroPosData']);

						$BD[$curOp]['PlayFinish'] = 0;
						$BD[$defOp]['PlayFinish'] = 0;

						//回合数|操作方|4
						$opLogRow = array($curOpBoutNum, $BD[$curOp]['CityId'], T_Battle::OP_ACT_END, T_Battle::OP_A);
						M_Battle_Calc::addOpLog($BD['Id'], $opLogRow);
						$log = "改变操作方#{$BD['CurOp']}当前操作方OP:" . json_encode($opLogRow);
						Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);
					}
					$sync = true;
				}

			} else if ($BD['CurStatus'] == T_Battle::STATUS_PROC) { //战斗进行中
				//判定胜负操作
				if (count($BD[T_Battle::CUR_OP_ATK]['HeroPosData']) == 0) {
					$BD['CurWin']    = T_Battle::CUR_OP_DEF;
					$BD['CurStatus'] = T_Battle::STATUS_RESULT;
					$sync            = true;
				} else if (count($BD[T_Battle::CUR_OP_DEF]['HeroPosData']) == 0) {
					$BD['CurWin']    = T_Battle::CUR_OP_ATK;
					$BD['CurStatus'] = T_Battle::STATUS_RESULT;
					$sync            = true;
				} else if (!$curIsOL && !$BD[$curOp]['IsAI'] && $now > $BD['CurOpEndTime'] - 3) {
					//如果当前方不在线 并且非AI模式
					$BD['CurStatus'] = T_Battle::STATUS_PROC_WAIT;
					$sync            = true;
				} else if ($now > $BD['CurOpEndTime'] + 10) { //如果单回合时间超过30s
					$BD['CurStatus'] = T_Battle::STATUS_PROC_WAIT;
					$sync            = true;
				} else if ($BD['CurOpBoutNum'] >= T_Battle::OP_BATTLE_BOUT_NUM) {
					//@todo 战斗回合数已满 则防御获胜  (竞技中辉有平局出现)
					$BD['CurWin']    = T_Battle::CUR_OP_DEF;
					$BD['CurStatus'] = T_Battle::STATUS_RESULT;
					$sync            = true;
				}

				$log = "改变状态#{$BD['CurStatus']}获胜方#{$BD['CurWin']}";
				$sync && Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);
			}
		}

		if ($sync) {
			$ret = M_Battle_Info::set($BD);
			Logger::battle("更新状态切换缓存位置 ", $BD['Id'], $BD['CurOpBoutNum']);
		}

		return $sync;
	}

	/**
	 * 如果当前操作方是AI模式, 自动计算AI
	 * 倒计时 第5s 开始计算AI
	 * @author huwei
	 * @param array $BD 战斗数据[引用变量]
	 */
	static public function checkProcessAuto(&$BD) {
		$ret = false;
		//如果当前操作方是AI模式
		$curOp = $BD['CurOp'];
		$now   = time();
		//倒计时 第5s 开始计算AI
		$calcAI = true;
		//如果ai方是 玩家 等待5秒 计算ai
		if (!empty($BD[$curOp]['CityId'])) {
			$boutTime   = !empty($BD[T_Battle::CUR_OP_DEF]['CityId']) ? T_Battle::OP_BATTLE_BOUT_PVP_TIME : T_Battle::OP_BATTLE_BOUT_PVE_TIME;
			$aiCalcTime = $boutTime - 5;
			$calcAI     = ($BD['CurOpEndTime'] - $now) < $aiCalcTime;
		}

		if (isset($BD['Id']) &&
			$BD[$curOp]['IsAI'] &&
			$BD[$curOp]['CalcAI'] == 0 &&
			$calcAI
		) {
			$ret = self::_calcAI($BD);
		}

		return $ret;
	}

	/**
	 * 如果当前操作方是AI模式, 手动计算AI
	 * 直接运算
	 * @author huwei
	 * @param array $BD 战斗数据[引用变量]
	 */
	static public function checkProcessManu(&$BD) {
		$ret = false;
		//如果当前操作方是AI模式
		$curOp = $BD['CurOp'];
		if (isset($BD['Id']) &&
			$BD[$curOp]['IsAI'] &&
			$BD[$curOp]['CalcAI'] == 0
		) {
			$ret = self::_calcAI($BD);
		}
		return $ret;
	}

	/**
	 * 操作战斗结果
	 * @author huwei
	 * @param array $BD 战斗数据[引用变量]
	 * @return bool
	 */
	static public function checkResult(&$BD) {
		$id = false;
		if (isset($BD['Id']) &&
			$BD['CurStatus'] == T_Battle::STATUS_RESULT &&
			empty($BD['CalcResult'])
		) {
			$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId'];
			$defCityId = $BD[T_Battle::CUR_OP_DEF]['CityId'];

			//被攻击方是否为玩家
			$defIsPlayer = !empty($defCityId) ? true : false;
			$x           = $defIsPlayer ? 'Y' : 'N';
			//获胜方
			$log = "战斗结束>>获胜方:" . json_encode($BD['CurWin']) . "被攻击方是否为玩家:" . $x;
			Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);

			$isAtkWin = false;
			if ($BD['CurWin'] == T_Battle::CUR_OP_ATK) {
				$isAtkWin = true;
			}

			//清理数据
			self::_cleanInBattleEnd($BD);

			//更新英雄状态经验威望
			$expArr = self::upExpInBattleEnd($BD);

			//获取奖励
			list($rewardArr, $noGetAward) = M_Battle_Handler::rewardInBattleEnd($BD, $expArr['defDiePct']);
			//Logger::debug(array(__METHOD__, $rewardArr, $noGetAward));

			//生成战斗报告(如果阵亡改变英雄状态为死亡)
			$reportData    = M_Battle_Handler::reportInBattleEnd($BD, $rewardArr, $expArr, $noGetAward);
			$atkAllDie     = $reportData['atkAllDie'];
			$warReportData = $reportData['warReportData'];

			$atkNeedFillArmy = $defNeedFillArmy = false;
			$atkNeedBack     = true;
			$backKeepMarchId = false;


			if ($BD['Type'] == M_War::BATTLE_TYPE_NPC) {
				list($atkNeedBack, $atkNeedFillArmy, $backKeepMarchId) = self::_npc($BD);
			} else if ($BD['Type'] == M_War::BATTLE_TYPE_OCCUPIED_CITY) {
				list($atkNeedBack, $atkNeedFillArmy) = self::_occupied($BD);
				empty($BD['DefMarchId']) && $defNeedFillArmy = true;
			} else if ($BD['Type'] == M_War::BATTLE_TYPE_RESCUE) {
				list($atkNeedBack, $atkNeedFillArmy) = self::_rescue($BD);
			} else if ($BD['Type'] == M_War::BATTLE_TYPE_FB) {
				list($atkNeedBack, $atkNeedFillArmy) = self::singleFB($BD);
				$isAtkWin && self::_updateFBRank($BD, $warReportData, $expArr);
			} else if ($BD['Type'] == M_War::BATTLE_TYPE_CITY) {
				$defNeedFillArmy = true;
			} else if ($BD['Type'] == M_War::BATTLE_TYPE_CAMP) {
				list($atkNeedBack, $atkNeedFillArmy) = self::_camp($BD);
			} else if ($BD['Type'] == M_War::BATTLE_TYPE_BOUT) {
				list($atkNeedBack, $atkNeedFillArmy) = self::breakout($BD);
			} else if ($BD['Type'] == M_War::BATTLE_TYPE_FLOOR) {
				list($atkNeedBack, $atkNeedFillArmy) = self::floor($BD);
			}

			//设置部队返回
			if ($atkAllDie) { //如果攻击方全部阵亡 无返回
				//删除行军记录
				M_March::delMarchInfo($BD['AtkMarchId']);

				//死亡的英雄不需要补兵
				$atkNeedBack = false;
			}

			M_March::syncMarchEnd($atkCityId, $BD['Type']);

			if ($atkNeedBack) {
				M_March::setMarchBack($BD['AtkMarchId'], $rewardArr, $backKeepMarchId);
				//Logger::debug(array(__METHOD__, $rewardArr));

				$rewardStr = json_encode($rewardArr);
				$log       = "设置行军#{$BD['AtkMarchId']}返回奖励数据:($rewardStr)";
				Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);
			}

			//副本战斗 结束 自动补兵
			//获取进攻方英雄ID列表
			$heroIds = array_keys($BD[T_Battle::CUR_OP_ATK]['HeroDataList']);
			$atkNeedFillArmy && M_Hero::fillHeroArmyNumByHeroId($atkCityId, $heroIds);

			//获取防守方英雄ID列表
			$heroIds = array_keys($BD[T_Battle::CUR_OP_DEF]['HeroDataList']);
			//非副本战斗 结束 防守方自动补兵
			$defNeedFillArmy && M_Hero::fillHeroArmyNumByHeroId($defCityId, $heroIds);

			//保持战斗报告
			$BD['CalcResult'] = 1;
			$initData         = array(M_Battle_Calc::REPORT_TYPE_ATK, $atkCityId, $defCityId);
			$BD['ReportId']   = M_WarReport::addWarReport($initData, $warReportData);

			$BD['CurStatus'] = T_Battle::STATUS_END;
			M_Battle_Info::set($BD);

			$log = "战报ID#{$BD['ReportId']}--更新战斗状态#{$BD['CurStatus']}";
			Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);

			if (!empty($BD['AtkMarchId'])) {
				//清除发生在当前坐标的战斗ID
				$mw = new M_March_Wait($BD['DefPos']);
				$mw->delBattleId();

				//每场战斗结束后此防守方的下一个敌人进入
				//Logger::dev("2setNextBattle DefPos#{$BD['DefPos']}");
				M_War::setNextBattle($BD['DefPos']);
			}
		}

		//M_Battle_Info::del($BD['Id']);
		Logger::battle("战斗结束", $BD['Id'], $BD['CurOpBoutNum']);

		return true;
	}


	/**
	 * 操作AI计算
	 * @author huwei
	 * @param array $BD 战斗数据[引用变量]
	 * @return array
	 */
	static private function _calcAI(&$BD) {
		//开始AI操作
		$curOp          = $BD['CurOp'];
		$othOp          = $curOp ^ 3;
		$heroList       = $BD[$curOp]['HeroPosData'];
		$changeBoutTime = 40;
		foreach ($heroList as $kId => $vData) {
			//检测是否被AI操作过
			list($pos, $act) = $vData;
			if ($act == 0) {
				//执行AI操作
				$bAI = new M_Battle_AI($BD);
				$bAI->run($curOp, $kId);
				$BD = $bAI->getData();
				//如果英雄阵亡 则不需要更新数据
				if (isset($BD[$curOp]['HeroPosData'][$kId])) {
					//获取英雄移动后的新坐标
					$newPos                          = $BD[$curOp]['HeroPosData'][$kId][0];
					$SkillEffect                     = $BD[$curOp]['HeroPosData'][$kId][2];
					$newAct                          = $act + T_Battle::OP_HERO_AI_FLAG + T_Battle::OP_HERO_MOVE_FLAG + T_Battle::OP_HERO_ATK_FLAG;
					$BD[$curOp]['HeroPosData'][$kId] = array($newPos, $newAct, $SkillEffect);
					$changeBoutTime -= 3;
				}
			}
		}
		$BD[$curOp]['ChangeBoutTime'] = $changeBoutTime;
		$BD[$curOp]['CalcAI']         = 1;
		$BD['CurStatus']              = T_Battle::STATUS_PROC_WAIT;

		//当前玩家是否在线
		$curIsOL = M_Battle_Calc::isViewOL($BD[$curOp]['CityId'], $BD['Id']);
		//对方玩家是否在线
		$othIsOL = M_Battle_Calc::isViewOL($BD[$othOp]['CityId'], $BD['Id']);

		//当前为AI操作
		if (!$curIsOL) //当前玩家不在线 则无需看动画
		{
			$BD[$curOp]['PlayFinish'] = 1;
		}

		if (!$othIsOL) //对方玩家不在线 则无需看动画
		{
			$BD[$othOp]['PlayFinish'] = 1;
		}
		//ai运算完成
		$BD['InAIQueueTime'] = 0;

		$ret = M_Battle_Info::set($BD);

		return $ret;
	}

	/**
	 * 计算更新战斗经验数据
	 * @author huwei on 20110930
	 * @param array $BD 战斗数据
	 * @return void
	 */
	static public function upExpInBattleEnd($BD) {
		$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId'];
		$defCityId = $BD[T_Battle::CUR_OP_DEF]['CityId'];

		//是否攻击方获胜
		$isAtkWin = ($BD['CurWin'] == T_Battle::CUR_OP_ATK) ? true : false;

		//被攻击方是否为玩家
		$defIsPlayer = intval($defCityId) > 0 ? true : false;
		$expArr      = M_Battle_Calc::calcExp($BD);
		Logger::battle("更新英雄状态和经验值:" . json_encode($expArr), $BD['Id'], $BD['CurOpBoutNum']);

		//更新攻击方英雄状态和经验值
		//@todo 竞技时考虑平局
		$battleResultStatus = '';
		if ($defIsPlayer) {
			$battleResultStatus = ($BD['CurWin'] == T_Battle::CUR_OP_ATK) ? M_Battle_Calc::BATTLE_WIN : M_Battle_Calc::BATTLE_FAIL;
		}

		M_Hero::setHeroExpAndFlag($atkCityId, $expArr['atkHero'], $battleResultStatus);

		//更新攻击方兵种经验值
		$atkCityObj = new O_Player($atkCityId);

		foreach ($expArr['atkArmy'] as $armyId => $addExp) {
			$atkCityObj->Army()->addExp($armyId, $addExp);
			$msRow[$armyId] = $atkCityObj->Army()->getById($armyId);
			M_Sync::addQueue($atkCityId, M_Sync::KEY_ARMY, $msRow); //同步兵种数据
		}

		if ($defIsPlayer) {
			//删除防御方战斗进行中的队列数据
			$ab2 = M_Battle_List::delBattleIdByCity($defCityId, $BD['Id']);

			//被防御为玩家的英雄状态和经验值
			//@todo 竞技时考虑平局
			$battleResultStatus = ($BD['CurWin'] == T_Battle::CUR_OP_DEF) ? M_Battle_Calc::BATTLE_WIN : M_Battle_Calc::BATTLE_FAIL;
			M_Hero::setHeroExpAndFlag($defCityId, $expArr['defHero'], $battleResultStatus);
			//被防御方为玩家的兵种经验值

			$defCityObj = new O_Player($defCityId);
			foreach ($expArr['atkArmy'] as $armyId => $addExp) {
				$defCityObj->Army()->addExp($armyId, $addExp);
				$msRow[$armyId] = $defCityObj->Army()->getById($armyId);
				M_Sync::addQueue($defCityId, M_Sync::KEY_ARMY, $msRow); //同步兵种数据
			}

		}

		if ($defIsPlayer) { //防御方玩家
			if ($isAtkWin) {
				$atkRate = 1;
				$defRate = 0.5;
			} else {
				$atkRate = 0.5;
				$defRate = 1;
			}

			$addItemArr   = array('renown' => $expArr['atkRenownValue'] * $atkRate);
			$objPlayerAtk = new O_Player($atkCityId);
			$objPlayerAtk->City()->addPoint($addItemArr); //攻打玩家获取威望
			$addItemArr   = array('renown' => $expArr['defRenownValue'] * $defRate);
			$objPlayerDef = new O_Player($defCityId);
			$objPlayerDef->City()->addPoint($addItemArr); //被攻打玩家获取威望

			$log = "攻玩家#{$atkCityId}防玩家#{$defCityId};攻获得威望:{$expArr['atkRenownValue']}防获得威望:{$expArr['defRenownValue']}";
			Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);
		} else { //防御方Npc
			if ($isAtkWin) {
				$addItemArr   = array('warexp' => $expArr['atkWarexpValue']);
				$objPlayerAtk = new O_Player($atkCityId);
				$objPlayerAtk->City()->addPoint($addItemArr); //攻打NPC获取军功
				$log = "玩家#{$atkCityId}攻打NPC获得军功:{$expArr['atkWarexpValue']}";
				Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);
			}
		}
		return $expArr;
	}

	/**
	 * 清除战斗数据
	 * @author huwei on 20110930
	 * @param array $BD 战斗数据
	 * @return void
	 */
	static private function _cleanInBattleEnd($BD) {
		$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId'];
		$defCityId = $BD[T_Battle::CUR_OP_DEF]['CityId'];

		//是否攻击方获胜
		$isAtkWin = ($BD['CurWin'] == T_Battle::CUR_OP_ATK) ? true : false;

		//被攻击方是否为玩家
		$defIsPlayer = intval($defCityId) > 0 ? true : false;

		//删除战斗进行中的队列数据
		$ab1 = M_Battle_List::delBattleIdByCity($atkCityId, $BD['Id']);

		M_Battle_Calc::delViewOl($atkCityId, $BD['Id']);
		if ($defIsPlayer) {
			M_Battle_Calc::delViewOl($defCityId, $BD['Id']);
		}
		//删除守护进程战斗进行中的队列数据
		$WBPQ = new M_Battle_QueueHandler();
		$WBPQ->del($BD['Id']);
		$log = "删除战斗进行中的队列数据atk:" . print_r($ab1, 1);
		Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);
	}

	/**
	 * 获取战斗战报数据
	 * @author huwei on 20110930
	 * @param array $BD 战斗数据
	 * @param array $rewardArr 奖励数据  数组格式
	 * @param array $expArr 经验数据  数组格式
	 * @return array            战报数据 数组格式
	 */
	static public function reportInBattleEnd($BD, $rewardArr, $expArr, $noGetAward = array()) {
		$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId'];
		$defCityId = $BD[T_Battle::CUR_OP_DEF]['CityId'];

		//是否攻击方获胜
		$isAtkWin = ($BD['CurWin'] == T_Battle::CUR_OP_ATK) ? true : false;

		//被攻击方是否为玩家
		$defIsPlayer = intval($defCityId) > 0 ? true : false;

		//统计攻击方战报内容
		$atkHero          = $BD[T_Battle::CUR_OP_ATK]['HeroDataList'];
		$atkReportContent = array();
		$heroDieNum       = 0;

		$armyBaseArr = M_Base::armyAll();
		$diedPeople  = 0;
		foreach ($atkHero as $heroId => $heroInfo) {
			$isDie = false;
			if (isset($expArr['atkHero'][$heroId]['flag']) && $expArr['atkHero'][$heroId]['flag'] == T_Hero::FLAG_DIE) {
				$isDie = true;
				$heroDieNum++;
			}

			$dieNum                    = $expArr['atkHero'][$heroId]['die_num'];
			$recoverNum                = $expArr['atkHero'][$heroId]['army_relife_num'];
			$atkReportContent[$heroId] = array(
				'Nickname'  => $heroInfo['nickname'],
				'FaceId'    => $heroInfo['face_id'],
				'Gender'    => $heroInfo['gender'],
				'Quality'   => $heroInfo['quality'],
				'Level'     => $heroInfo['level'],
				'WeaponId'  => $heroInfo['weapon_id'],
				'ArmyId'    => $heroInfo['army_id'],
				'IsDie'     => $isDie ? 1 : 0,
				'DieNum'    => $dieNum + $recoverNum,
				'RelifeNum' => $recoverNum,
				'Exp'       => $expArr['atkHero'][$heroId]['exp'],
				'ArmyNum'   => $heroInfo['army_num'],
			);

			$armyId = $heroInfo['army_id'];
			if ($dieNum > 0 && $armyId > 0) {
				$diedPeople += intval($armyBaseArr[$armyId]['cost_people'] * intval($dieNum));
			}
		}

		if ($diedPeople > 0) { //释放 阵亡兵 占用的人口
			$objPlayerAtk = new O_Player($atkCityId);
			$objPlayerAtk->City()->diedPeopleToFreePeople($diedPeople);
			$objPlayerAtk->save();
		}

		//攻击方英雄是否全部阵亡
		$atkAllDie = ($heroDieNum == count($atkHero)) ? true : false;

		//统计防守方战报内容
		$defHero          = $BD[T_Battle::CUR_OP_DEF]['HeroDataList'];
		$defReportContent = array();
		$diedPeople       = 0;
		foreach ($defHero as $heroId => $heroInfo) {
			$isDie = false;
			if ($defIsPlayer) {
				//玩家为防守方
				if (isset($expArr['defHero'][$heroId]['flag']) && $expArr['defHero'][$heroId]['flag'] == T_Hero::FLAG_DIE) {
					$isDie = true;
				}
			}

			$dieNum                    = $expArr['defHero'][$heroId]['die_num'];
			$recoverNum                = $expArr['defHero'][$heroId]['army_relife_num'];
			$defReportContent[$heroId] = array(
				'Nickname'  => $heroInfo['nickname'],
				'FaceId'    => $heroInfo['face_id'],
				'Gender'    => $heroInfo['gender'],
				'Quality'   => $heroInfo['quality'],
				'Level'     => $heroInfo['level'],
				'WeaponId'  => $heroInfo['weapon_id'],
				'ArmyId'    => $heroInfo['army_id'],
				'IsDie'     => $isDie ? 1 : 0,
				'DieNum'    => $dieNum + $recoverNum,
				'RelifeNum' => $recoverNum,
				'Exp'       => $expArr['defHero'][$heroId]['exp'],
				'ArmyNum'   => $heroInfo['army_num'],
			);

			$armyId = $heroInfo['army_id'];
			if ($dieNum > 0 && $armyId > 0) {
				$diedPeople += intval($armyBaseArr[$armyId]['cost_people'] * intval($dieNum));
			}
		}

		if ($defCityId > 0 && $diedPeople > 0) {
			//释放阵亡士占用兵人口
			$objPlayerDef = new O_Player($defCityId);
			$objPlayerDef->City()->diedPeopleToFreePeople($diedPeople);
			$objPlayerDef->save();
		}

		$fbname = '';
		if ($BD['Type'] == M_War::BATTLE_TYPE_FB) {
			list($defPosZ, $defPosX, $defPosY) = M_Formula::calcParseFBNo($BD['DefPos']);
			$fbinfo = M_SoloFB::getDetail($defPosZ, $defPosX);
			$fbname = $fbinfo['name'];
		}

		$AtkCredit = $DefCredit = 0;
		if ($isAtkWin) {
			//如果进攻方获胜
			if ($defIsPlayer) {
				$AtkCredit = $expArr['atkRenownValue'];
				$DefCredit = $expArr['defRenownValue'];
			} else {
				$AtkCredit = $expArr['atkWarexpValue'];
			}
		}

		//报告内容
		//@todo 修改$noGetAward 数据结构
		$reportContentArr = array(
			'Atk'        => $atkReportContent,
			'Def'        => $defReportContent,
			'AtkArmyExp' => $expArr['atkArmy'],
			'DefArmyExp' => $defIsPlayer ? $expArr['defArmy'] : array(),
			'AtkCredit'  => $AtkCredit, //打NPC为功勋 打玩家为威望
			'DefCredit'  => $DefCredit, //防御方为玩家 获胜
			'AtkDiePct'  => $expArr['atkDiePct'] * 100,
			'DefDiePct'  => $expArr['defDiePct'] * 100,
			'NoGetAward' => M_Award::toText($noGetAward),
		);
		$isSucc           = $isAtkWin ? T_App::SUCC : T_App::FAIL;
		//保存战斗日志

		$reportLogContentArr = $reportContentArr;

		$reportLogContentArr['Reward']     = M_Award::toText($rewardArr);
		$reportLogContentArr['FBName']     = $fbname;
		$reportLogContentArr['IsSucc']     = $isSucc;
		$reportLogContentArr['Type']       = M_Battle_Calc::REPORT_TYPE_ATK; //战报类型
		$reportLogContentArr['BattleType'] = $BD['Type']; //战斗类型
		//Logger::debug(array(__METHOD__, $reportLogContentArr));
		$replayAddress = M_Battle_Calc::makeOpLogFile($BD, $reportLogContentArr);
		unset($reportLogContentArr);

		$warReportData = array(
			'id'             => $BD['Id'],
			'content'        => $reportContentArr,
			'reward'         => $rewardArr,
			'is_succ'        => $isSucc,
			'replay_address' => $replayAddress,
			'create_at'      => time(),
		);

		$reportData['atkAllDie']     = $atkAllDie;
		$reportData['warReportData'] = $warReportData;

		$log = "战斗报告数据:" . json_encode($reportData);
		Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);
		return $reportData;
	}

	static public function _rewardWinNpc() {

	}

	/**
	 * 获取战斗胜利后的奖励
	 * @author huwei on 20110930
	 * @param array $BD 战斗数据
	 * @param int $defDiePct 防守方伤亡比率
	 * @return array        奖励数据 数组格式
	 */
	static public function rewardInBattleEnd($BD, $defDiePct = 0) {
		$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId'];
		$defCityId = $BD[T_Battle::CUR_OP_DEF]['CityId'];

		$objPlayerAtk = new O_Player($atkCityId);
		$atkCityInfo  = $objPlayerAtk->getCityBase();

		//是否攻击方获胜
		$isAtkWin = ($BD['CurWin'] == T_Battle::CUR_OP_ATK) ? true : false;

		//被攻击方是否为玩家
		$defIsPlayer = false;
		if (intval($defCityId) > 0) {
			$defIsPlayer  = true;
			$objPlayerDef = new O_Player($defCityId);
			$defCityInfo  = $objPlayerDef->getCityBase();
		}

		$rewardArr = $noGetAward = array();

		if ($isAtkWin) {
			if (in_array($BD['Type'], array(M_War::BATTLE_TYPE_FB, M_War::BATTLE_TYPE_BOUT, M_War::BATTLE_TYPE_FLOOR))) {
				//副本战斗 和 突围  不为空的 防御NPC ID
				//获取NPC部队数据
				$npcInfo = M_NPC::getInfo($BD['DefNpcId']);
				$awardId = $npcInfo['award_id'];

				$vipLv     = $atkCityInfo['vip_level'];
				$rewardArr = M_Award::rateResult($awardId);
				//防沉迷相关
				$objPlayerAtk->City()->filterAward($rewardArr);

				$noGetAward  = array();
				$propsNumArr = $objPlayerAtk->Pack()->hasNum();
				foreach ($rewardArr as $k => $tmpAward) {
					list($tType, $tNum, $id) = $tmpAward;
					$clean = false;
					if ($tType == 'equip' && M_Equip::isEquipNumFull($atkCityId, $vipLv)) {
						$clean = true;
					} else if ($tType == 'props') {
						$propsInfo = M_Props::baseInfo($id);
						if ($propsInfo['type'] == M_Props::TYPE_DRAW &&
							$propsNumArr['draw']['full']
						) {
							$clean = true;
						} else if (in_array($propsInfo['type'], array(M_Props::TYPE_INNER, M_Props::TYPE_HERO, M_Props::TYPE_TREA, M_Props::TYPE_WAR)) &&
							$propsNumArr['normal']['full']
						) {
							$clean = true;
						} else if ($propsInfo['type'] == M_Props::TYPE_STUFF &&
							$propsNumArr['stuff']['full']
						) {
							$clean = true;
						}
					}
					if ($clean) {
						$noGetAward[] = $tmpAward;
						unset($rewardArr[$k]);
					}
				}


				$log = "副本奖励数据:" . json_encode($rewardArr);
				Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);
				//直接更新奖励
				$objPlayerAtk->City()->toAward($rewardArr, __METHOD__);

			} else if (!empty($BD['AtkMarchId'])) { //普通战斗  [玩家|NPC]获取奖励不同
				if ($defIsPlayer &&
					empty($BD['DefNpcId']) &&
					$BD['Type'] == M_War::BATTLE_TYPE_CITY
				) {
					//防御方是玩家
					$atkCarryNum = 0;
					$atkHero     = $BD[T_Battle::CUR_OP_ATK]['HeroDataList'];
					if (is_array($atkHero)) {
						foreach ($atkHero as $heroId => $heroInfo) {
							//计算运载量
							$atkCarryNum += $heroInfo['carry'] * $heroInfo['left_num'];
						}
					}

					$rewardArr = M_War::getAtkPlunderRes($atkCityId, $defCityId, $atkCarryNum);

					//扣除玩家资源
					$newRewardArr = array();
					foreach ($rewardArr as $resKey => $resNum) {
						$newRewardArr[] = array($resKey, $resNum, 0);
						$objPlayerDef->Res()->incr($resKey, -$resNum);
					}

					$bDecrRes = true;

					$objPlayerAtk->City()->filterAward($newRewardArr);
					$rewardArr = $newRewardArr;

					$isOpen = 0;

					$atkRenown = !empty($atkCityInfo['renown']) ? $atkCityInfo['renown'] : 0;
					$defRenown = !empty($defCityInfo['renown']) ? $defCityInfo['renown'] : 0;
					$isOpen    = M_War::openDecrArmyNum($atkRenown, $defRenown);
					if ($isOpen == 1) {
						M_War::failToDecrArmyNum($atkCityId, $defCityId);
					}

					$objPlayerAtk->Liveness()->check(M_Liveness::GET_POINT_ATK_CITY);

					$errMsg = $bDecrRes ? '成功' : '失败';
					$log    = "玩家{$atkCityId}运载量#{$atkCarryNum}攻打玩家#{$defCityId}扣除资源{$errMsg}:" . json_encode($rewardArr);
					Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);
				} else if (!empty($BD['DefNpcId'])) {
					//防御方是npc
					$npcInfo      = M_NPC::getInfo($BD['DefNpcId']);
					$awardId      = $npcInfo['award_id'];
					$defDiePctNum = $defDiePct * 100;
					if ($npcInfo['type'] == M_NPC::TMP_NPC) {
						$objPlayerAtk->Liveness()->check(M_Liveness::GET_POINT_TEMPNPC, $npcInfo['level']);

						$tmpNpcConf = M_NPC::getRandTempNpcConf();
						if (isset($tmpNpcConf[$npcInfo['id']][7])) {
							$tmpNpcAward = $tmpNpcConf[$npcInfo['id']][7];
							$awardId     = 0;
							foreach ($tmpNpcAward as $pctKey => $pctVal) {
								if ($defDiePctNum >= $pctKey) {
									$awardId = $pctVal;
								}
							}
						}

						//Logger::debug(array(__METHOD__, $defDiePctNum, $awardId, $tmpNpcConf[$npcInfo['id']][7]));
					} else if ($npcInfo['type'] == M_NPC::FASCIST_NPC) {
						$objPlayerAtk->Liveness()->check(M_Liveness::GET_POINT_TEMPNPC, $npcInfo['level']);
						$FascistNpcConf = M_NPC::getFixedTempNpcConf();
						if (isset($FascistNpcConf[$npcInfo['id']]['npc_awardArr'])) {
							$fascistNpcAward = $FascistNpcConf[$npcInfo['id']]['npc_awardArr'];
							$awardId         = 0;
							foreach ($fascistNpcAward as $pctKey => $pctVal) {
								if ($defDiePctNum >= $pctKey) {
									$awardId = $pctVal;
								}
							}
						}
						//Logger::debug(array(__METHOD__, $defDiePctNum, $awardId, $tmpNpcConf[$npcInfo['id']][7]));
					}

					$rewardArr = M_Award::rateResult($awardId);
					//防沉迷相关
					$objPlayerAtk->City()->filterAward($rewardArr);

					$log = "玩家{$atkCityId}攻打NPC#{$BD['DefNpcId']}获得奖励:" . json_encode($rewardArr);
					Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);
				}
			}
		} else {
			if (!empty($BD['DefNpcId'])) {
				$npcInfo = M_NPC::getInfo($BD['DefNpcId']);
				if ($npcInfo['type'] == M_NPC::TMP_NPC) { //临时NPC 失败也能获得奖励
					$defDiePctNum = $defDiePct * 100;

					$awardId    = $npcInfo['award_id'];
					$tmpNpcConf = M_NPC::getRandTempNpcConf();
					if (isset($tmpNpcConf[$npcInfo['id']][7])) {
						$tmpNpcAward = $tmpNpcConf[$npcInfo['id']][7];
						$awardId     = 0;

						foreach ($tmpNpcAward as $pctKey => $pctVal) {
							if ($defDiePctNum >= $pctKey) {
								$awardId = $pctVal;
							}
						}
					}

					//Logger::debug(array(__METHOD__, $defDiePctNum, $awardId, $tmpNpcConf[$npcInfo['id']][7]));

					$rewardArr = M_Award::rateResult($awardId);
				}
			}
		}

		$objPlayerAtk->save();
		if ($defIsPlayer) {
			$objPlayerDef->save();
		}
		return array($rewardArr, $noGetAward);
	}

	static private function _occupied($BD) //占领城市
	{
		$atkNeedBack     = true;
		$atkNeedFillArmy = false;
		$now             = time();
		$atkCityId       = $BD[T_Battle::CUR_OP_ATK]['CityId']; //攻击方城市
		$defCityId       = $BD[T_Battle::CUR_OP_DEF]['CityId']; //防守方城市
		$marchInfo       = M_March_Info::get($BD['AtkMarchId']);
		$mapRow          = M_MapWild::getWildMapInfo($BD['DefPos']); //目的地地图
		$atkmapRow       = M_MapWild::getWildMapInfo($BD['AtkPos']); //目的地地图
		$atkCityInfo     = M_City::getInfo($atkCityId);
		if (!empty($mapRow['city_id'])) {
			$cityInfo = M_City::getInfo($mapRow['city_id']);

			if (!empty($cityInfo['id'])) {
				list($z, $x, $y) = M_MapWild::calcWildMapPosXYByNo($mapRow['pos_no']);
				list($z1, $x1, $y1) = M_MapWild::calcWildMapPosXYByNo($atkmapRow['pos_no']);
				if ($BD['CurWin'] == T_Battle::CUR_OP_ATK) { //攻击方获胜
					$defColonyInfo = M_ColonyCity::getInfo($mapRow['city_id']);
					if ($defColonyInfo['atk_city_id'] > 0) { //之前被占领
						if (!empty($defColonyInfo['atk_march_id'])) { //撤回已占领的军队
							M_March::setMarchBack($defColonyInfo['atk_march_id']); //撤的是第三方的行军
						}
						//属地删除
						$defCityInfo = M_City::getInfo($defColonyInfo['atk_city_id']);
						$delUp       = M_ColonyCity::del($defColonyInfo['atk_city_id'], $marchInfo['def_pos']); //删除第三方的城市属地

						if ($delUp) { //发送消息邮件
							$content = array(T_Lang::C_WILD_CITY_LOSE, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y, date('Y'), date('m'), date('d'), date('h'), date('i'), $marchInfo['atk_nickname']);
							M_Message::sendSysMessage($defColonyInfo['atk_city_id'], json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
						}

						M_ColonyCity::setInfo($mapRow['city_id'], array('atk_city_id' => $atkCityId, 'atk_march_id' => $marchInfo['id']));
						//占领成功 如果有防御方 则需要删除这个 防御方 敌情信息
						//获取防御方的敌情信息
						$marchArr = M_March::getMarchList($defColonyInfo['atk_city_id'], M_War::MARCH_OWN_DEF);
						Logger::dev("OldDefCityId#" . $mapRow['city_id'] . "DefPos#{$marchInfo['def_pos']};MarchList#" . json_encode($marchArr));

						foreach ($marchArr as $marchId => $val) {
							if ($val['def_pos'] == $marchInfo['def_pos']) {
								//更新其他进攻方的行军数据中的 防御城市ID
								$ret = M_March_Info::set(array('id' => $marchId, 'def_city_id' => 0));

								//删除当前防守方的敌情
								Logger::dev("2delSyncMarchData#{$marchId}#{$defColonyInfo['atk_city_id']}");
								M_March::syncDelMarchBack($marchId, $defColonyInfo['atk_city_id']);
							}
						}
					}

					if (!empty($BD[T_Battle::CUR_OP_ATK]['HeroPosData'])) {
						//属地添加
						$atkHeroIdList = array_keys($BD[T_Battle::CUR_OP_ATK]['HeroPosData']);

						$marchInfo = array(
							'id'          => $BD['AtkMarchId'],
							'atk_pos'     => $BD['AtkPos'],
							'def_pos'     => $BD['DefPos'],
							'hero_list'   => json_encode($atkHeroIdList),
							'atk_city_id' => $atkCityId,
							'def_city_id' => $defCityId,
						);
						$bAdd      = M_ColonyCity::add($marchInfo);
						if ($bAdd) {
							$recordActive = M_Config::getVal('record_active');
							$d2           = strtotime($recordActive['start']); //获取战绩值起始时间
							$d3           = strtotime($recordActive['end']); //获取战绩值截止时间
							if ($d2 < $now && $now < $d3) //要在活动期间并且今天未被占领
							{
								$cityIdArr      = array();
								$rcOccupiedList = new B_Cache_RC(T_Key::OCCUPIED_LIST, date('Ymd') . $marchInfo['atk_city_id']);
								$cityIdArr      = $rcOccupiedList->smembers();
								if (empty($cityIdArr) || (!empty($cityIdArr) && !(in_array($mapRow['city_id'], $cityIdArr)))) {
									$tmpStr      = "record_value_{$cityInfo['level']}";
									$recordValue = isset($recordActive['list'][$tmpStr]) ? $recordActive['list'][$tmpStr] : 0;

									if ($recordValue > 0) {
										$rc = new B_Cache_RC(T_Key::RANKINGS_RECORD);
										$rc->zincrby($recordValue, $marchInfo['atk_city_id']);
									}
								}
								$rcOccupiedList->sadd($mapRow['city_id']);
							}

							$atkcityInfo         = M_City::getInfo($marchInfo['atk_city_id']); //攻击方城市信息
							$cityColonyUnionInfo = M_Union::getInfo($cityInfo['union_id']);
							$cityColonyInfo      = M_ColonyCity::getInfo($mapRow['city_id']);

							$objPlayer   = new O_Player($mapRow['city_id']);
							$cdRescueArr = $objPlayer->CD()->toFront(O_CD::TYPE_RESCUE);
							$rescueTimes = explode(',', M_Config::getVal('rescue_cd_times'));
							$diff        = $cdRescueArr[0];
							$tmpRescue   = array(0, $cityColonyInfo['rescue_num'], $rescueTimes[1], $rescueTimes[2]);
							if ($diff > 0) {
								$tmpRescue[0] = $diff;
							}

							$atkcityColonyInfo = array(
								'hold_flag'    => 1,
								'nickName'     => $atkcityInfo['nickname'],
								'level'        => $atkcityInfo['level'],
								'unionName'    => $cityColonyUnionInfo['name'],
								'posNo'        => M_MapWild::calcWildMapPosXYByNo($atkcityInfo['pos_no']),
								'rescueCd'     => $tmpRescue,
								'SelfRsMhTime' => T_App::ONE_MINUTE
							);

							M_Sync::addQueue($mapRow['city_id'], M_Sync::KEY_CITY_OCCUPIED, $atkcityColonyInfo);
							$content = array(T_Lang::C_WILD_NPC_HOLD_SUCC, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
							M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
							$content = array(T_Lang::C_WILD_CITY_OCCUPIED, date('Y'), date('m'), date('d'), date('h'), date('i'), $atkCityInfo['nickname'], array(T_Lang::$Map[$z1]), $x1 . ',' . $y1);
							M_Message::sendSysMessage($mapRow['city_id'], json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
							$atkNeedBack = false;

							$dailyAward = M_Config::getVal('active_award');
							list($IsOpen, $activeField) = M_Task::getHoldNpcActiveStaus($atkCityId, $dailyAward);
							$atkCityInfo = M_City::getInfo($atkCityId);
							if ($IsOpen == 5 || $IsOpen == 6) //第3阶段的活动
							{
								$rc = new B_Cache_RC(T_Key::CITY_OCCOUPIED_TIMES, $atkCityId);
								$rc->hincrby($mapRow['city_id'], 1);
								$numArr = $rc->hgetall();
								if ($dailyAward['list3']['num'] <= $numArr[$mapRow['city_id']]) {
									M_Task::active($atkCityId, 'award'); //更新学院活动的完成状态
								}
							} else if ($IsOpen == 7 || $IsOpen == 8) {
								if ($cityInfo['level'] == $atkCityInfo['level']) {
									$rc = new B_Cache_RC(T_Key::CITY_OCCOUPIED_TIMES, $atkCityId);
									$rc->hincrby($mapRow['city_id'], 1);
									$numArr = $rc->hgetall();
									if ($dailyAward['list4']['num'] <= $numArr[$mapRow['city_id']]) {
										M_Task::active($atkCityId, 'award'); //更新学院活动的完成状态
									}
								}
							} else if ($IsOpen == 9 || $IsOpen == 10) {
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

						$atkNeedFillArmy = true;
					}
				} else { //进攻方 输了占领战斗
					$defColonyInfo = M_ColonyCity::getInfo($mapRow['city_id']);
					if (!empty($defColonyInfo['atk_march_id'])) {
						$info = M_March_Info::get($defColonyInfo['atk_march_id']);
						$bUp  = M_March::setMarchHold($info); //进攻方输了 占领方继续驻守
						//Logger::debug(array(__METHOD__, $info, $bUp));
					}
					$content = array(T_Lang::C_WILD_CITY_OCCUPIED_FAIL, $BD[T_Battle::CUR_OP_DEF]['Nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
					M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
				}
			} else {
				Logger::error(array(__METHOD__, 'err npc info', $mapRow['city_id']));
			}
		} else {
			Logger::error(array(__METHOD__, 'err npc id at wild map', $BD['DefPos']));
		}

		return array($atkNeedBack, $atkNeedFillArmy);
	}

	static private function _rescue($BD) //解救城市
	{
		$now             = time();
		$atkNeedBack     = true;
		$atkNeedFillArmy = false;
		$atkCityId       = $BD[T_Battle::CUR_OP_ATK]['CityId']; //攻击方城市
		$defCityId       = $BD[T_Battle::CUR_OP_DEF]['CityId']; //防守方城市
		$defMarchInfo    = M_March_Info::get($BD['DefMarchId']); //防御方行军ID
		$atkCityInfo     = M_City::getInfo($atkCityId);
		$mapRow          = M_MapWild::getWildMapInfo($BD['DefPos']); //目的地地图
		$atkmapRow       = M_MapWild::getWildMapInfo($BD['AtkPos']); //目的地地图

		//Logger::debug(array(__METHOD__, $BD['DefMarchId'], $defMarchInfo));

		if (!empty($mapRow['city_id'])) {
			$cityInfo = M_City::getInfo($mapRow['city_id']);

			if (!empty($cityInfo['id'])) {
				list($z, $x, $y) = M_MapWild::calcWildMapPosXYByNo($mapRow['pos_no']);
				list($z1, $x1, $y1) = M_MapWild::calcWildMapPosXYByNo($atkmapRow['pos_no']);
				if ($BD['CurWin'] == T_Battle::CUR_OP_ATK) //攻击方获胜
				{
					$defHold = M_ColonyCity::getInfo($mapRow['city_id']); //地图上占领方信息
					if ($defHold['atk_city_id'] > 0) //占领方城市ID
					{
						$delUp = M_ColonyCity::del($defHold['atk_city_id'], $defMarchInfo['def_pos']); //删除占领方城市属地信息
						if ($delUp) {
							$content = array(T_Lang::C_WILD_CITY_RESCUE_LOSE, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y, date('Y'), date('m'), date('d'), date('h'), date('i'), $BD[T_Battle::CUR_OP_ATK]['Nickname']);
							M_Message::sendSysMessage($defHold['atk_city_id'], json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content)); //发送消息邮件

							if (!empty($defHold['atk_march_id'])) {
								M_March::setMarchBack($defHold['atk_march_id']); //撤回已占领方的军队
							}

							//占领成功 如果有防御方 则需要删除这个 防御方 敌情信息
							//获取防御方的敌情信息
							//删除敌情列表中的行军Id
							$marchArr = M_March::getMarchList($defHold['atk_city_id'], M_War::MARCH_OWN_DEF);
							Logger::dev("OldDefCityId#" . $mapRow['city_id'] . "DefPos#{$defMarchInfo['def_pos']};MarchList#" . json_encode($marchArr));
							foreach ($marchArr as $marchId => $val) {
								if ($val['def_pos'] == $defMarchInfo['def_pos']) {
									//更新其他进攻方的行军数据中的 防御城市ID
									$marchData = array('id' => $marchId, 'def_city_id' => 0);
									$ret       = M_March_Info::set($marchData);

									//删除当前防守方的敌情
									Logger::dev("2delSyncMarchData#{$marchId}#{$defHold['atk_city_id']}");
									M_March::syncDelMarchBack($marchId, $defHold['atk_city_id']);
								}
							}

							$addFlag = false;

							if (!empty($BD[T_Battle::CUR_OP_ATK]['HeroPosData'])) {
								$updInfo    = array('atk_city_id' => 0, 'atk_march_id' => 0);
								$updateFlag = M_ColonyCity::setInfo($defCityId, $updInfo); //更新被占领方属地数据

								$atkHeroIdList = array_keys($BD[T_Battle::CUR_OP_ATK]['HeroPosData']);

								$ret = M_MapWild::setWildMapInfo($BD['DefPos'], array('march_id' => 0));
								//Logger::debug(array(__METHOD__, 'march_id:0', $ret, $BD['DefPos']));
								if ($updateFlag) {
									$cityColonyUnionInfo = M_Union::getInfo($cityInfo['union_id']);
									$atkcityInfo         = M_City::getInfo($atkCityId);
									list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($atkcityInfo['pos_no']);
									$cityColonyInfo = M_ColonyCity::getInfo($defCityId);


									$objPlayerDef = new O_Player($defCityId);
									$cdRescueArr  = $objPlayerDef->CD()->toFront(O_CD::TYPE_RESCUE);
									$rescueTimes  = explode(',', M_Config::getVal('rescue_cd_times'));
									$diff         = $cdRescueArr[0];
									$tmpRescue    = array(0, $cityColonyInfo['rescue_num'], $rescueTimes[1], $rescueTimes[2]);
									if ($diff > 0) {
										$tmpRescue[0] = $diff;
									}
									$atkcityColonyInfo = array(
										'hold_flag'    => 0,
										'nickName'     => '',
										'level'        => 0,
										'unionName'    => $cityColonyUnionInfo['name'],
										'posNo'        => array(),
										'rescueCd'     => $tmpRescue,
										'SelfRsMhTime' => T_App::ONE_MINUTE
									);
									M_Sync::addQueue($mapRow['city_id'], M_Sync::KEY_CITY_OCCUPIED, $atkcityColonyInfo);

									M_MapWild::syncWildMapBlockCache($BD['DefPos']);

									$content = array(T_Lang::C_WILD_CITY_RESCUE_SUCC, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
									M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
									if ($atkCityId != $mapRow['city_id']) {
										$rc1 = new B_Cache_RC(T_Key::OCCUPIED_LIST, date('Ymd') . $atkCityId);

										$recordActive = M_Config::getVal('record_active');
										if ($now > strtotime($recordActive['start']) &&
											$now < strtotime($recordActive['end'])
										) //要在活动期间并且今天未被占领
										{
											$cityIdArr = $rc1->smembers();
											if (empty($cityIdArr) ||
												(!empty($cityIdArr) && !in_array($mapRow['city_id'], $cityIdArr))
											) {
												$recordValue = isset($recordActive['list']['record_value_rescue']) ? $recordActive['list']['record_value_rescue'] : 0;
												if ($recordValue > 0) {
													$rc = new B_Cache_RC(T_Key::RANKINGS_RECORD);
													$rc->zincrby($recordValue, $atkCityId);
												}
											}
										}

										$rc1->sadd($mapRow['city_id']);

										$content = array(T_Lang::C_WILD_CITY_RESCUED_SUCC, $atkCityInfo['nickname'], array(T_Lang::$Map[$z1]), $x1 . ',' . $y1);
										M_Message::sendSysMessage($mapRow['city_id'], json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
									}
								} else { //发送消息邮件
									$content = array(T_Lang::C_WILD_CITY_RESCUE_FAIL, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
									M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
									if ($atkCityId != $mapRow['city_id']) {
										$content = array(T_Lang::C_WILD_CITY_RESCUED_FAIL, $atkCityInfo['nickname'], array(T_Lang::$Map[$z1]), $x1 . ',' . $y1);
										M_Message::sendSysMessage($mapRow['city_id'], json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
									}
								}

								$atkNeedFillArmy = true;
							}
						}
					}
				} else { //进攻方 输了解救战斗
					$content = array(T_Lang::C_WILD_CITY_RESCUE_FAIL, $cityInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
					M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_CITY_TIP)), json_encode($content));
					//Logger::debug(array(__METHOD__, $BD['DefMarchId'], $defMarchInfo));
					M_March::setMarchHold($defMarchInfo);
				}
			} else {
				Logger::error(array(__METHOD__, 'err city info', $mapRow['city_id']));
			}
		} else {
			Logger::error(array(__METHOD__, 'err city id at wild map', $BD['DefPos']));
		}

		return array($atkNeedBack, $atkNeedFillArmy);
	}

	static private function _camp($BD) {
		//Logger::dev("#{$BD['Id']},{$BD['Type']}计算据点");
		$atkNeedBack = true;
		//据点战斗 不需要自动补兵
		$atkNeedFillArmy = false;
		list($type, $campId, $defLineNo) = M_MapWild::calcWildMapPosXYByNo($BD['DefPos']);
		$campInfo = M_Campaign::getInfo($campId);
		//据点编号
		$defLineNo      = strval($defLineNo);
		$defLineNoField = 'no_' . $defLineNo;
		list($defUnionId, $marchIds) = json_decode($campInfo[$defLineNoField], true);
		//Logger::dev("战斗后=2=marchIds:".json_encode($marchIds));

		$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId'];
		$defCityId = $BD[T_Battle::CUR_OP_DEF]['CityId'];

		//Logger::dev("defCityId:{$defCityId};defLineNoField#{$defLineNoField}".json_encode($marchIds));
		$tmpMarchId = $marchIds[0];

		if ($BD['CurWin'] == T_Battle::CUR_OP_ATK) {
			$atkNeedBack = false;

			//进攻方 赢了据点战斗
			//Logger::dev("进攻方 赢了据点战斗, 防御部队#".json_encode($marchIds));

			if ($marchIds[0] > 0) { //撤回主防的行军
				M_March::setMarchBack($marchIds[0]);
				//Logger::debug(array(__METHOD__,"setMarchBack#{$atkCityId}#{$defCityId}#{$marchIds[0]}"));
			}

			if (empty($marchIds[1])) { //据点无协防 设置获胜方行军ID 为主防
				$cityInfo    = M_City::getInfo($atkCityId);
				$marchIds[0] = $BD['AtkMarchId'];
				$defUnionId  = $cityInfo['union_id'];
				$hasDef      = false;
			} else { //据点还有战斗部队
				$marchIds[0] = $marchIds[1];
				$marchIds[1] = $marchIds[2];
				$marchIds[2] = 0;
				//把部队设置为排队的头  重新下一场战斗
				$mw = new M_March_Wait($BD['DefPos']);
				$mw->add($BD['AtkMarchId'], true);
				$hasDef = true;
			}

			$upInfo = array(
				$defLineNoField => json_encode(array($defUnionId, $marchIds))
			);

			$bUp = M_Campaign::setInfo($campId, $upInfo);

			if (!$hasDef && $bUp) { //没有防御部队
				$marchInfo = M_March_Info::get($BD['AtkMarchId']);
				//设置行军状态为驻守
				M_March::setMarchHold($marchInfo);
			}
		} else { //进攻方 输了据点战斗
			//Logger::dev("进攻方 输了据点战斗, 防御部队#".json_encode($marchIds));
			if ($marchIds[0] > 0) { //设置防守方 行军状态为驻守
				$marchInfo = M_March_Info::get($marchIds[0]);
				M_March::setMarchHold($marchInfo);
			}
		}

		//Logger::dev("据点战斗结束, 防御部队#".json_encode($marchIds));

		if ($tmpMarchId > 0) { //清除防守据点中的战斗信息
			$defLinePos = M_MapWild::calcWildMapPosNoByXY(T_App::MAP_CAMPAIGN, $campId, $defLineNo);
			//Logger::dev("清除防守据点中的战斗信息defLinePos#{$defLinePos};marchId#{$tmpMarchId}");
			$obj_ml = new M_March_List($defLinePos);
			$obj_ml->del($tmpMarchId);

		}

		return array($atkNeedBack, $atkNeedFillArmy);
	}

	static private function _npc($BD) {
		$atkNeedBack     = true;
		$atkNeedFillArmy = false;
		$atkCityId       = $BD[T_Battle::CUR_OP_ATK]['CityId'];
		$defCityId       = $BD[T_Battle::CUR_OP_DEF]['CityId'];
		$mapRow          = M_MapWild::getWildMapInfo($BD['DefPos']);
		$backKeepMarchId = false;

		if (!empty($mapRow['npc_id'])) {
			$npcInfo = M_NPC::getInfo($mapRow['npc_id']);
			//Logger::debug(array(__METHOD__, $npcInfo));
			if (!empty($npcInfo['id'])) {
				list($z, $x, $y) = M_MapWild::calcWildMapPosXYByNo($mapRow['pos_no']);

				$holdArr = array(
					M_NPC::CITY_NPC_FOOT  => 'hold_npc_type_1',
					M_NPC::CITY_NPC_GUN   => 'hold_npc_type_2',
					M_NPC::CITY_NPC_ARMOR => 'hold_npc_type_3',
					M_NPC::CITY_NPC_AIR   => 'hold_npc_type_4',
					M_NPC::RES_NPC_FOOD   => '',
					M_NPC::RES_NPC_GOLD   => '',
					M_NPC::RES_NPC_OIL    => '',
				);

				$npcType = isset($npcInfo['type']) ? $npcInfo['type'] : 0;
				if ($BD['CurWin'] == T_Battle::CUR_OP_ATK) { //攻击方获胜
					if (isset($holdArr[$npcType])) {
						$dailyAward = M_Config::getVal('active_award');
						list($IsOpen, $activeField) = M_Task::getHoldNpcActiveStaus($atkCityId, $dailyAward);
						if ($IsOpen == 1 || $IsOpen == 2) {
							M_Task::active($atkCityId, $holdArr[$npcInfo['type']]); //更新学院活动的完成状态
						} elseif ($IsOpen == 3 || $IsOpen == 4) {
							if ($mapRow['city_id'] > 0) {
								M_Task::active($atkCityId, $holdArr[$npcInfo['type']]); //更新学院活动的完成状态
							}
						}

						if ($mapRow['city_id'] > 0) {
							//删除敌情列表中的行军Id
							$obj_ml = new M_March_List($mapRow['pos_no']);
							$obj_ml->del($BD['AtkMarchId']);

							M_March::syncDelMarchBack($BD['AtkMarchId'], $mapRow['city_id']);

							//撤回已占领的军队
							M_March::setMarchBack($BD['DefMarchId']);

							//属地删除
							$objPlayer = new O_Player($mapRow['city_id']);

							$delUp = $objPlayer->ColonyNpc()->del($mapRow['pos_no']);
							if ($delUp) { //发送消息邮件
								$content = array(T_Lang::C_WILD_NPC_LOSE, $npcInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y, date('Y'), date('m'), date('d'), date('h'), date('i'), $BD[T_Battle::CUR_OP_ATK]['Nickname']);
								M_Message::sendSysMessage($mapRow['city_id'], json_encode(array(T_Lang::T_WILD_NPC_TIP)), json_encode($content));
							}

							//NPC 野地 被 占领后 更新排队中的记录
							$oldDefCityId = $mapRow['city_id'];
							//Logger::dev("OldDefCityId#".$oldDefCityId."DefPos#{$mapRow['pos_no']}");
							//占领成功 如果有防御方 则需要删除这个 防御方 敌情信息
							//获取防御方的敌情信息
							$marchArr = M_March::getMarchList($oldDefCityId, M_War::MARCH_OWN_DEF);
							//Logger::dev("MarchList#".json_encode($marchArr));
							foreach ($marchArr as $marchId => $val) {
								if ($val['def_pos'] == $mapRow['pos_no']) {
									//更新其他进攻方的行军数据中的 防御城市ID
									$marchData = array('id' => $marchId, 'def_city_id' => $atkCityId);
									M_March_Info::set($marchData);

									//删除当前防守方的敌情
									//Logger::dev("1delSyncMarchData#{$marchId}#{$defCityId}");
									M_March::syncDelMarchBack($marchId, $oldDefCityId);
								}
							}
						}

						if (!empty($BD[T_Battle::CUR_OP_ATK]['HeroPosData'])) {
							//属地添加
							$atkHeroIdList = array_keys($BD[T_Battle::CUR_OP_ATK]['HeroPosData']);

							//Logger::debug(array(__METHOD__, $atkHeroIdList));
							$marchInfo = array(
								'id'          => $BD['AtkMarchId'],
								'atk_pos'     => $BD['AtkPos'],
								'def_pos'     => $BD['DefPos'],
								'hero_list'   => json_encode($atkHeroIdList),
								'atk_city_id' => $atkCityId,
								'def_city_id' => $defCityId,
							);

							$bAdd = $objPlayer->ColonyNpc()->add($marchInfo);
							if ($bAdd) {
								if (in_array($npcType, array(M_NPC::RES_NPC_FOOD, M_NPC::RES_NPC_GOLD, M_NPC::RES_NPC_OIL))) { //资源属地
									$objPlayer = new O_Player($atkCityId);
									$objPlayer->Res()->upGrow('npc_colony');
									$objPlayer->save();
								}


								//发送消息邮件
								$content = array(T_Lang::C_WILD_NPC_HOLD_SUCC, $npcInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
								M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_NPC_TIP)), json_encode($content));
								$atkNeedBack = false;
							} else { //发送消息邮件
								$content = array(T_Lang::C_WILD_NPC_HOLD_FULL, $npcInfo['nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
								M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_NPC_TIP)), json_encode($content));
							}

							$atkNeedFillArmy = true;
						}
					} else if ($npcInfo['type'] == M_NPC::TMP_NPC) { //战斗胜利后 清楚地图数据
						M_MapWild::cleanWildMapInfo($BD['DefPos']);
					} else if ($npcInfo['type'] == M_NPC::FASCIST_NPC) { //战斗胜利后 清楚地图数据
						M_MapWild::cleanWildMapInfo($BD['DefPos']);
					}
					M_MapWild::syncWildMapBlockCache($mapRow['pos_no']);
				} else { //进攻方 输了野地战斗

					//Logger::debug(array(__METHOD__, $BD['DefMarchId'], $mapRow['city_id'], $npcType));

					if (isset($holdArr[$npcType])) {
						if ($mapRow['city_id'] > 0) {
							//发送消息邮件
							$content = array(T_Lang::C_WILD_NPC_DEF_SUCC, $BD[T_Battle::CUR_OP_DEF]['Nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y, date('Y'), date('m'), date('d'), date('h'), date('i'), $BD[T_Battle::CUR_OP_ATK]['Nickname']);
							M_Message::sendSysMessage($mapRow['city_id'], json_encode(array(T_Lang::T_WILD_NPC_TIP)), json_encode($content));

							$defMarchInfo = M_March_Info::get($BD['DefMarchId']);

							M_March::setMarchHold($defMarchInfo);
							$backKeepMarchId = true;
						}

						$content = array(T_Lang::C_WILD_NPC_HOLD_FAIL, $BD[T_Battle::CUR_OP_DEF]['Nickname'], array(T_Lang::$Map[$z]), $x . ',' . $y);
						M_Message::sendSysMessage($atkCityId, json_encode(array(T_Lang::T_WILD_NPC_TIP)), json_encode($content));
					}
				}
			} else {
				Logger::error(array(__METHOD__, 'err npc info', $mapRow['npc_id'], $npcInfo));
			}
		} else {
			Logger::error(array(__METHOD__, 'err npc id at wild map', $BD['DefPos']));
		}


		return array($atkNeedBack, $atkNeedFillArmy, $backKeepMarchId);
	}

	/**
	 * 单人副本战役
	 * @author huwei
	 * @param array $BD
	 * @param array $warReportData
	 * @param array $expArr
	 * @return array
	 */
	static public function singleFB($BD, $isQuick = false) {
		$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId'];

		$objPlayer = new O_Player($atkCityId);

		if ($BD['CurWin'] == T_Battle::CUR_OP_ATK) {
			$fbNo = $BD['DefPos'];
			$log  = "当前副本:" . $objPlayer->City()->last_fb_no . "攻打副本:{$fbNo}";
			Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);

			$objPlayer->Quest()->check('atk_fb', array('id' => $fbNo, 'num' => 1));

			Logger::debug(array(__METHOD__, $objPlayer->City()->last_fb_no, $fbNo));

			if ($objPlayer->City()->last_fb_no < $fbNo) {
				//更新玩家副本编号
				$objPlayer->City()->last_fb_no = $fbNo;
				M_Build::checkShowBuild($objPlayer, $fbNo, 'fb');

				$log = "同步副本ID{$fbNo}";
				Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);

				Logger::debug(array(__METHOD__, $objPlayer->City()->last_fb_no));
			}
		}

		$objPlayer->City()->fb_battle_id = 0;

		$objPlayer->save();
		//副本战斗需要自动补兵
		$atkNeedFillArmy = true;
		//副本无行军记录 不需要返回
		$atkNeedBack = false;

		return array($atkNeedBack, $atkNeedFillArmy);
	}

	/**
	 * 更新fb排行
	 * @author duhuihui
	 * @param array $BD
	 * @param array $warReportData
	 * @param array $expArr
	 */
	private static function _updateFBRank($BD, $warReportData, $expArr) {
		//攻打副本 副本编号:$BD['DefPos'] 时间 data('Y-m-d')  战报地址$warReportData['replay_address']  cityId $cityInfo['id']等级$expArr[atkAvgLv],损失$expArr[atkDiePct]

		$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId'];
		$defCityId = $BD[T_Battle::CUR_OP_DEF]['CityId'];

		$cityInfo       = M_City::getInfo($atkCityId); //城市信息
		$time           = time(); //当前时间
		$recentlyPassed = array(); //最近攻打战役排行
		$firstPassed    = array(); //最早攻打战役排行
		$lossLeast      = array(); //损失最少排行
		$levelLowest    = array(); //等级最低排行
		$list           = M_Ranking::getFBPass($BD['DefPos']); //从内存读取数据，及时没有值，会在数据表中插入空数据
		if (!empty($list['recently_passed'])) {
			$recentlyPassed = $list['recently_passed'];
		}
		$recentlyPassed[] = array(
			'cityId'        => $atkCityId,
			'nickName'      => $cityInfo['nickname'],
			'time'          => $time,
			'replayAddress' => $warReportData['replay_address']
		);
		$volume           = array();
		foreach ($recentlyPassed as $key => $row) {
			$volume[] = $row['time'];
		}
		array_multisort($volume, SORT_DESC, $recentlyPassed); //对数组进行排序

		$recentlyPassed          = array_slice($recentlyPassed, 0, M_War::FB_PASS_RANK_NUM, true); //去前面5个数据
		$list['recently_passed'] = $recentlyPassed;

		if (!empty($list['first_passed'])) {
			$firstPassed = $list['first_passed'];
		}
		$firstPassed[] = array(
			'cityId'        => $atkCityId,
			'nickName'      => $cityInfo['nickname'],
			'time'          => $time,
			'replayAddress' => $warReportData['replay_address']
		);
		$volume        = array();
		foreach ($firstPassed as $key => $row) {
			$volume[] = $row['time'];
		}
		array_multisort($volume, SORT_ASC, $firstPassed); //对数组进行排序
		$firstPassed          = array_slice($firstPassed, 0, M_War::FB_PASS_RANK_NUM, true); //去前面5个数据
		$list['first_passed'] = $firstPassed;

		/*如果成绩相同就按名字的升序排列。
		 这时我们就需要根据$guys的顺序多弄两个数组出来：
		$scores = array(80,70,80,20);
		$names = array('jake','jin','john','ben');
		array_multisort($scores, SORT_DESC, $names, $guys);就行了*/
		if (!empty($list['loss_least'])) {
			$lossLeast = $list['loss_least'];
		}
		$lossLeast[] = array(
			'cityId'        => $atkCityId,
			'nickName'      => $cityInfo['nickname'],
			'time'          => $time,
			'loss'          => round($expArr['atkDiePct'], 4),
			'replayAddress' => $warReportData['replay_address']
		);
		$volume      = array();
		$volumeLoss  = array();
		foreach ($lossLeast as $key => $row) {
			$volume[]     = $row['time'];
			$volumeLoss[] = $row['loss'];
		}

		array_multisort($volumeLoss, SORT_ASC, $volume, $lossLeast); //对数组进行排序
		$lossLeast          = array_slice($lossLeast, 0, M_War::FB_PASS_RANK_NUM, true); //去前面5个数据
		$list['loss_least'] = $lossLeast;

		if (!empty($list['level_lowest'])) {
			$levelLowest = $list['level_lowest'];
		}
		$levelLowest[] = array(
			'cityId'        => $atkCityId,
			'nickName'      => $cityInfo['nickname'],
			'time'          => $time,
			'level'         => round($expArr['atkAvgLv'], 2),
			'replayAddress' => $warReportData['replay_address']
		);
		$volume        = $volumeLevel = array();
		foreach ($levelLowest as $key => $row) {
			$volume[]      = $row['time'];
			$volumeLevel[] = $row['level'];
		}
		array_multisort($volumeLevel, SORT_ASC, $volume, $levelLowest); //对数组进行排序
		$levelLowest          = array_slice($levelLowest, 0, M_War::FB_PASS_RANK_NUM, true); //去前面5个数据
		$list['level_lowest'] = $levelLowest;
		unset($list['fb_no']);
		M_Ranking::setFBPass($BD['DefPos'], $list);

		$list['fb_no']           = $BD['DefPos'];
		$list['recently_passed'] = json_encode($list['recently_passed']);
		$list['first_passed']    = json_encode($list['first_passed']);
		$list['loss_least']      = json_encode($list['loss_least']);
		$list['level_lowest']    = json_encode($list['level_lowest']);
		B_DB::instance('FbPass')->updateBy($list, array('fb_no' => $BD['DefPos']));
	}

	/** 突围战斗结束处理 */
	static public function breakout($BD) {
		$atkNeedBack     = false; //突围 不需要返回
		$atkNeedFillArmy = true; //补兵
		//$now 			= time();
		$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId']; //攻击方城市
		//$defCityId 		= $BD[T_Battle::CUR_OP_DEF]['CityId'];//防守方城市

		$upInfo = array('battle_id' => 0);
		$msRow  = array('battle_id_now' => 0);

		if ($BD['CurWin'] == T_Battle::CUR_OP_ATK) {
			$arrDefPos    = explode('_', $BD['DefPos']); //'突围ID_关编号从1开始'
			$cityBout     = M_BreakOut::getCityBreakOut($atkCityId);
			$cityBoutData = $cityBout['breakout_data'];
			$boutInfo     = $cityBoutData[$arrDefPos[0]]; //某城市某突围数据
			$baseBoutInfo = M_BreakOut::baseInfo($arrDefPos[0]); //基础突围数据

			$baseBoutArr = explode('|', $baseBoutInfo['data']);
			$boutPostNum = count($baseBoutArr);

			//获取当前突围关卡数据
			$tmpData = explode(',', $baseBoutArr[$arrDefPos[1] - 1]);
			//获取当前突围通关积分
			$passPoint = !empty($tmpData[3]) ? $tmpData[3] : 0;
			//Logger::debug(array(__METHOD__, $arrDefPos, $tmpData, $passPoint));
			if ($passPoint > 0) {
				//获取最后突围关卡数据
				$upInfo['point'] = $cityBout['point'] + $passPoint;
				M_Sync::addQueue($atkCityId, M_Sync::KEY_CITY_INFO, array('BreakoutPoint' => $upInfo['point']));
			}

			if ($arrDefPos[1] >= $boutPostNum) //打掉某突围最后一关
			{
				if ($arrDefPos[1] > $boutInfo[1]) //未打过的关
				{
					$cityBoutData[$arrDefPos[0]][1] = intval($arrDefPos[1]);
					$upInfo['breakout_data']        = json_encode($cityBoutData);

					$msRow['story'][$arrDefPos[0]] = array('OVER' => range(1, $arrDefPos[1]), 'NEXT' => 0);
				}

				if ($baseBoutInfo['next_boutid'] > 0) //有下一个突围ID
				{
					$arrBoutPass = explode(',', $cityBout['breakout_pass']);
					if (!in_array($baseBoutInfo['next_boutid'], $arrBoutPass)) {
						$arrBoutPass[]           = $baseBoutInfo['next_boutid'];
						$upInfo['breakout_pass'] = implode(',', $arrBoutPass);
					}

					$has = array();

					$nextBaseBoutInfo = M_BreakOut::baseInfo($baseBoutInfo['next_boutid']); //突围基础数据
					$nextBaseBoutArr  = explode('|', $baseBoutInfo['data']);

					$has = M_BreakOut::getHasAwardIds($nextBaseBoutArr);

					$msRow['story'][$baseBoutInfo['next_boutid']] = array('OVER' => array(), 'NEXT' => 1, 'HAS' => $has);
				}
			} else {
				if ($arrDefPos[1] > $boutInfo[1]) //未打过的关
				{
					$cityBoutData[$arrDefPos[0]][1] = intval($arrDefPos[1]);
					$upInfo['breakout_data']        = json_encode($cityBoutData);

					$msRow['story'][$arrDefPos[0]] = array('OVER' => range(1, $arrDefPos[1]), 'NEXT' => $arrDefPos[1] + 1);
				}
			}
		}

		!empty($upInfo) && M_BreakOut::updateCityBreakOut($atkCityId, $upInfo, true); //更新
		!empty($msRow) && M_Sync::addQueue($atkCityId, M_Sync::KEY_BOUT, $msRow); //同步

		return array($atkNeedBack, $atkNeedFillArmy);
	}

	/** 爬楼战斗结束处理 */
	static public function floor($BD) {
		$atkNeedBack     = false; //爬楼不需要返回
		$atkNeedFillArmy = true; //补兵
		$atkCityId       = $BD[T_Battle::CUR_OP_ATK]['CityId']; //攻击方城市

		$msRow = array(
			'BattleId' => 0,
		);

		$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId']; //攻击方城市

		$objPlayer = new O_Player($atkCityId);
		$objFloor  = $objPlayer->Floor();

		$objFloor->setBId(0);

		if ($BD['CurWin'] == T_Battle::CUR_OP_ATK) {
			list($type, $atkFloorNo) = explode('_', $BD['DefPos']);

			$curTypeData = $objFloor->getData($type);

			if (!empty($curTypeData)) {
				$max = $objFloor->getMaxNum($type);
				list($open, $curFloorNo, $hadAward) = $curTypeData;
				$newFloorNo = min($curFloorNo + 1, $max);
				$objFloor->setData($type, array($open, $newFloorNo, 0));

				$newType = min($type + 1, O_Floor::TYPE_NUM);

				$newTypeData = $objFloor->getData($newType);

				if (empty($newTypeData[0]) && $newFloorNo == $max) { //更新下一级数据
					//array(是否开放,当前楼层,是否领取奖励)
					$objFloor->setData($newType, array(1, 1, 0));
				}


				$msRow['CurFloorNo'] = $newFloorNo;
			}
		}

		$objPlayer->save();


		!empty($msRow) && M_Sync::addQueue($atkCityId, M_Sync::KEY_FLOOR, $msRow); //同步

		return array($atkNeedBack, $atkNeedFillArmy);
	}
}

?>