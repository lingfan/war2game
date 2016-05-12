<?php

class M_Battle_Quick {
	private static $_BD = array();

	public function __construct($info) {
		$now = time();
		if (!empty($info)) {
			$mapData       = $info['map_data'];
			$atkHeroIdList = $mapData['atkHeroPos'];
			$defHeroIdList = $mapData['defHeroPos'];

			$curOpEndTime   = 0;
			$startTime      = $now;
			$atkHeroPosData = array();
			foreach ($mapData['atkHeroPos'] as $atkId => $atkPos) {
				$atkHeroPosData[$atkId] = array($atkPos, T_Battle::OP_HERO_INIT_FLAG, array());
			}

			$defHeroPosData = array();
			foreach ($mapData['defHeroPos'] as $defId => $defPos) {
				$defHeroPosData[$defId] = array($defPos, T_Battle::OP_HERO_INIT_FLAG, array());
			}

			$BD = array(
				'Id'                 => (int)$info['id'],
				'StartTime'          => (int)$info['create_at'], //战斗开始时间
				'Type'               => (int)$info['type'], //战斗类型
				'Weather'            => (int)$info['weather'], //天气类型
				'Terrian'            => (int)$info['terrian'], //地形类型
				'AtkMarchId'         => (int)$info['atk_march_id'], //进攻行军ID
				'DefMarchId'         => (int)$info['def_march_id'], //防御行军ID
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
					'Gender'          => $info['atk_gender'],
					'Pos'             => $info['atk_pos'],
					'IsAI'            => $info['atk_is_ai'], //攻击方是否是AI操作
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
					'Gender'          => $info['def_gender'],
					'Pos'             => $info['def_pos'],
					'IsAI'            => $info['def_is_ai'], //防守方是否是AI操作
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

			self::$_BD = $BD;
		}
	}

	static private function _calcAI() {
		$BD       = self::$_BD;
		$curOp    = $BD['CurOp'];
		$othOp    = $curOp ^ 3;
		$heroList = $BD[$curOp]['HeroPosData'];
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
				}
			}
		}
		self::$_BD = $BD;

	}

	static private function _changeBout() {
		$BD           = self::$_BD;
		$curOpBoutNum = $BD['CurOpBoutNum'];
		$curOp        = $BD['CurOp'];
		$othOp        = $curOp ^ 3;
		//切换AI操作标记
		$BD[$othOp]['CalcAI'] = 0;
		$BD['CurOp']          = $othOp;
		$BD['CurOpBoutNum'] += 1;
		//更新战斗英雄列表
		$BD[$othOp]['HeroPosData'] = M_Battle_Calc::updateHeroFlag($BD[$othOp]['HeroPosData']);

		//回合数|操作方|4
		$opLogRow = array($curOpBoutNum,
			$BD[$curOp]['CityId'],
			T_Battle::OP_ACT_END,
			T_Battle::OP_A);
		M_Battle_Calc::addOpLog($BD['Id'], $opLogRow);
		self::$_BD = $BD;
	}

	static private function _isEnd() {
		$ret = false;

		//判定胜负操作
		if (count(self::$_BD[T_Battle::CUR_OP_ATK]['HeroPosData']) == 0) {
			self::$_BD['CurWin']    = T_Battle::CUR_OP_DEF;
			self::$_BD['CurStatus'] = T_Battle::STATUS_RESULT;
			$ret                    = true;
		} elseif (count(self::$_BD[T_Battle::CUR_OP_DEF]['HeroPosData']) == 0) {
			self::$_BD['CurWin']    = T_Battle::CUR_OP_ATK;
			self::$_BD['CurStatus'] = T_Battle::STATUS_RESULT;
			$ret                    = true;
		} elseif (self::$_BD['CurOpBoutNum'] > T_Battle::OP_BATTLE_BOUT_NUM) {
			//@todo 战斗回合数已满 则防御获胜  (竞技中辉有平局出现)
			self::$_BD['CurWin']    = T_Battle::CUR_OP_DEF;
			self::$_BD['CurStatus'] = T_Battle::STATUS_RESULT;
			$ret                    = true;
		}
		return $ret;
	}

	/**
	 * 操作战斗结果
	 * @author huwei
	 * @param bool $quick 快速战斗
	 * @return bool
	 */
	static private function _checkResult() {
		$BD  = self::$_BD;
		$ret = false;
		if (isset($BD['Id']) &&
			$BD['CurStatus'] == T_Battle::STATUS_RESULT &&
			empty($BD['CalcResult'])
		) {
			$atkCityId = $BD[T_Battle::CUR_OP_ATK]['CityId'];
			$defCityId = $BD[T_Battle::CUR_OP_DEF]['CityId'];

			$isAtkWin = ($BD['CurWin'] == T_Battle::CUR_OP_ATK) ? true : false;

			//更新英雄状态经验威望
			$expArr = M_Battle_Handler::upExpInBattleEnd($BD);

			//获取奖励
			list($rewardArr, $noGetAward) = M_Battle_Handler::rewardInBattleEnd($BD, $expArr['defDiePct']);
			//Logger::debug(array(__METHOD__, $rewardArr, $noGetAward));

			//生成战斗报告(如果阵亡改变英雄状态为死亡)
			$reportData    = M_Battle_Handler::reportInBattleEnd($BD, $rewardArr, $expArr, $noGetAward);
			$atkAllDie     = $reportData['atkAllDie'];
			$warReportData = $reportData['warReportData'];

			$quickmapno = M_War::quickmapno();
			if ($quickmapno) {
				//快速战斗无法查看战报
				$warReportData['replay_address'] = '';
			}


			//副本战斗 结束 自动补兵
			//获取进攻方英雄ID列表
			$heroIds = array_keys($BD[T_Battle::CUR_OP_ATK]['HeroDataList']);
			M_Hero::fillHeroArmyNumByHeroId($atkCityId, $heroIds);

			//保持战斗报告
			$BD['CalcResult'] = 1;
			$initData         = array(M_Battle_Calc::REPORT_TYPE_ATK, $atkCityId, $defCityId);
			$BD['ReportId']   = M_WarReport::addWarReport($initData, $warReportData);

			$BD['CurStatus'] = T_Battle::STATUS_END;

			$log = "战报ID#{$BD['ReportId']}--更新战斗状态#{$BD['CurStatus']}";
			Logger::battle($log, $BD['Id'], $BD['CurOpBoutNum']);

			$ret = $BD['Id'];


			//日常任务[挑战战役]
			if ($BD['Type'] == M_War::BATTLE_TYPE_FB) {
				M_Battle_Handler::singleFB($BD);
			} else if ($BD['Type'] == M_War::BATTLE_TYPE_BOUT) {
				M_Battle_Handler::breakout($BD);
			} else if ($BD['Type'] == M_War::BATTLE_TYPE_FLOOR) {
				M_Battle_Handler::floor($BD);
			}
		}

		Logger::battle("战斗结束", $BD['Id'], $BD['CurOpBoutNum']);

		return $ret;
	}

	public function run() {
		$ret = false;
		$bid = self::$_BD['Id'];
		$n   = 1;
		for ($boutNum = 1; $boutNum <= T_Battle::OP_BATTLE_BOUT_NUM; $boutNum++) {
			self::_calcAI();
			self::_changeBout();
			$bEnd = self::_isEnd();
			if ($bEnd) {
				$ret = self::_checkResult();
				break;
			}
			$n++;

		}
		#Logger::dev("BID#{$bid};boutNum#{$n}");
		return $ret;
	}

}

?>