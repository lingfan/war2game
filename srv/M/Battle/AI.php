<?php

class M_Battle_AI {
	static $_BD = array();

	public function __construct($BD) {
		self::$_BD = $BD;
	}

	public function getData() {
		return self::$_BD;
	}

	/**
	 * AI运行脚本
	 * @author huwei
	 * @param int $battleId
	 * @param int $curOp
	 * @param int $heroId
	 */
	public function run($curOp, $heroId) {
		//Logger::debug('run-------------'.$heroId);
		$ret = $sync = $data = false;
		if (isset(self::$_BD['Id'])) {
			list($heroPos, $opFlag, $SkillEffect) = self::$_BD[$curOp]['HeroPosData'][$heroId];

			$heroInfo = self::$_BD[$curOp]['HeroDataList'][$heroId];
			$atkRange = array($heroInfo['shot_range_min'], $heroInfo['shot_range_max']);

			$aimStr = self::_findNearestAimInRound($curOp, $heroPos);

			//获取目标
			if (!empty($aimStr)) {
				list($defAimId, $defAimPos) = explode(',', $aimStr);

				//获取行动路径
				$wp        = new M_Battle_Path(self::$_BD['MapSize'], self::$_BD['MapCell']);
				$rangeList = $wp->getAtkRange($heroPos, $atkRange);
				//是否在攻击范围
				if (M_Battle_AI::checkAtkRange($defAimPos, $rangeList)) {
					$data = self::atk($heroId, intval($defAimId), $curOp);
				} else {
					//如果没有在攻击范围 移动
					$newHoldPos = self::move($heroId, intval($defAimId), $curOp);
					$rangeList  = $wp->getAtkRange($newHoldPos, $atkRange);
					//是否在攻击范围
					if (M_Battle_AI::checkAtkRange($defAimPos, $rangeList)) {
						$data = self::atk($heroId, intval($defAimId), $curOp);
					} else {
						//如果目标不在攻击范围 则查看是否有其他目标在攻击范围
						$aimStr = self::_findNearestAimInAtkRange($curOp, $newHoldPos, $rangeList);
						if ($aimStr) {
							list($defAimId, $defAimPos) = explode(',', $aimStr);
							$data = self::atk($heroId, $defAimId, $curOp);
						}

					}
				}

				$sync = true;
			}


		}

		return $data;
	}

	/**
	 * 移动目标
	 * @author huwei
	 * @param int $heroId 当前英雄ID
	 * @param int $defAimId 目标英雄ID
	 * @param int $curOp 当前操作方
	 * @return string        移动后的坐标
	 */
	public function move($heroId, $defAimId, $curOp) {

		list($heroPos, $opFlag, $atkSkillEffect) = self::$_BD[$curOp]['HeroPosData'][$heroId];

		if (isset($atkSkillEffect['UNMOVE']) && $atkSkillEffect['UNMOVE'] >= self::$_BD['CurOpBoutNum']) {
			self::$_BD[$curOp]['HeroPosData'][$heroId] = array($atkPos, $atkOpFlag + T_Battle::OP_HERO_MOVE_FLAG, $atkSkillEffect);
		} else {
			$defOp    = $curOp ^ 3;
			$heroInfo = self::$_BD[$curOp]['HeroDataList'][$heroId];
			list($defAimPos, $defAimOpFlag, $defSkillEffect) = self::$_BD[$defOp]['HeroPosData'][$defAimId];
			//Logger::debug('move*******************'.json_encode(self::$_BD[$curOp]['HeroPosData'][$heroId]));
			$wp = new M_Battle_Path(self::$_BD['MapSize'], self::$_BD['MapCell']);
			//移动范围
			$moveRange = $wp->getMoveRange($heroInfo['move_type'], $heroPos, $heroInfo['move_range']);

			//目标之间的距离
			$aimDistance    = M_Formula::aiCalcDistance($heroPos, $defAimPos);
			$maxAtkDistance = $heroInfo['move_range'] + $heroInfo['shot_range_max'];

			//默认移动1步
			$move = 1;
			//目标距离 在 最大攻击范围内
			$maxMovePos = '';
			if ($aimDistance < $maxAtkDistance) {
				if ($aimDistance > $heroInfo['shot_range_max']) //如果目标在最大攻击范围外
				{
					$move = $aimDistance - $heroInfo['shot_range_max'];
				} elseif ($aimDistance < $heroInfo['shot_range_min']) //如果目标在最小攻击范围内
				{
					$atkRange = array($heroInfo['shot_range_min'], $heroInfo['shot_range_max']);
					//获取被攻击方能背攻击到的坐标
					$atkRangeList = $wp->getAtkRange($defAimPos, $atkRange);
					$maxMovePos   = M_Battle_AI::getMoveMaxMove($heroPos, $moveRange, $atkRangeList);
				}
			} else {
				$move = $heroInfo['move_range'];
			}


			$log = "英雄#{$heroId}能移动范围{$heroInfo['move_range']}最大攻击范围{$maxAtkDistance}目标距离{$aimDistance}需要移动{$move}";
			Logger::battle($log, self::$_BD['Id'], self::$_BD['CurOpBoutNum']);

			if (!empty($maxMovePos)) {
				$moveAimPos = $maxMovePos;
			} else {
				//移动到离目标最近点
				$moveAimPos = $wp->getMoveLastPos($move, $moveRange, $defAimPos);
			}

			$moveList = $wp->getMovePath($moveAimPos, $moveRange);
			//Logger::debug(array('move_list*******************', $moveList));

			$log = "英雄#{$heroId}目的坐标{$defAimPos}移动范围{$move}目标最近点{$moveAimPos}移动列表:" . json_encode($moveList);
			Logger::battle($log, self::$_BD['Id'], self::$_BD['CurOpBoutNum']);

			if (!empty($moveList)) {
				//更新移动坐标和 加已移动标记
				unset(self::$_BD['MapCell'][$heroPos]);
				self::$_BD['MapCell'][$moveAimPos]         = array($curOp, $heroId);
				self::$_BD[$curOp]['HeroPosData'][$heroId] = array($moveAimPos, $opFlag + T_Battle::OP_HERO_MOVE_FLAG, $atkSkillEffect);

				$log = "地图坐标数据更新:" . json_encode(self::$_BD['MapCell'][$moveAimPos]) . "英雄{$heroId}坐标数据" . json_encode(self::$_BD[$curOp]['HeroPosData'][$heroId]);
				Logger::battle($log, self::$_BD['Id'], self::$_BD['CurOpBoutNum']);

				//回合数|操作方|1|操作方式|英雄ID|坐标数组
				$opLogRow = array(self::$_BD['CurOpBoutNum'],
					self::$_BD[$curOp]['CityId'],
					T_Battle::OP_ACT_MOVE,
					T_Battle::OP_A,
					$heroId,
					$moveList);

				M_Battle_Calc::addOpLog(self::$_BD['Id'], $opLogRow);
				Logger::battle("OP日志" . json_encode($opLogRow), self::$_BD['Id'], self::$_BD['CurOpBoutNum']);
			}
			return $moveAimPos;
		}
	}

	/**
	 * 攻击目标
	 * @param int $heroId1 攻击方英雄ID
	 * @param int $heroId2 目标英雄ID
	 * @param int $curOp 当前操作方
	 * @param int $isAIOP 是否AI操作
	 */
	public function atk($heroId1, $heroId2, $curOp, $isAIOP = T_Battle::OP_A) {
		$data    = array();
		$fonthit = T_Battle::ATK_HIT;
		$backhit = T_Battle::ATK_NO;
		//heroId1 攻击 heroId2
		$defOp     = $curOp ^ 3;
		$heroInfo1 = $heroInfo2 = array();
		//Logger::error(array(__METHOD__, $heroId1, $heroId2, array_keys(self::$_BD[$curOp]['HeroDataList']), array_keys(self::$_BD[$defOp]['HeroDataList'])));
		$heroInfo1 = self::$_BD[$curOp]['HeroDataList'][$heroId1];
		$heroInfo2 = self::$_BD[$defOp]['HeroDataList'][$heroId2];

		if (!empty($heroInfo2) && !empty($heroInfo1)) {
			//Logger::error(array(__METHOD__, $heroInfo1['id'], $heroInfo2['id']));
			//	Logger::debug('atk======atk===='.$heroId1.'==='.json_encode(self::$_BD[$curOp]['HeroPosData'][$heroId1]));
			//	Logger::debug('atk======def===='.$heroId2.'==='.json_encode(self::$_BD[$defOp]['HeroPosData'][$heroId2]));

			list($atkPos, $atkOpFlag, $atkSkillEffect) = self::$_BD[$curOp]['HeroPosData'][$heroId1];
			list($defAimPos, $defAimOpFlag, $defSkillEffect) = self::$_BD[$defOp]['HeroPosData'][$heroId2];

			if (isset($atkSkillEffect['UNATK']) && $atkSkillEffect['UNATK'] >= self::$_BD['CurOpBoutNum']) {
				self::$_BD[$curOp]['HeroPosData'][$heroId1] = array($atkPos, $atkOpFlag + T_Battle::OP_HERO_ATK_FLAG, $atkSkillEffect);
			} else {
				//	Logger::debug('atk======atk================'.self::$_BD['CurOpBoutNum'].'==============');
				$leftArmyNum1 = self::$_BD[$curOp]['HeroDataList'][$heroId1]['left_num'];
				$leftArmyNum2 = self::$_BD[$defOp]['HeroDataList'][$heroId2]['left_num'];
				$debugLogTxt  = "\n攻击方英雄:" . $heroId1 . ",战斗前剩余兵力:" . $leftArmyNum1 . "\n防守方英雄:" . $heroId2 . ",战斗前剩余兵力:" . $leftArmyNum2 . "\n";
				$debugLogTxt .= "\n计算开始==================\n";
				//Logger::debug("atk_heroId:-------".$heroId1."------,leftArmyNum1:------".$leftArmyNum1."------def_heroId:-----".$heroId2."-------leftArmyNum2------:".$leftArmyNum2."\n");
				//Logger::debug("=============start==================\n");
				//计算heroId1 攻击 heroId2 的 攻击力
				$tmpAtkForce  = M_Battle_Calc::calcAtkForce($heroInfo1, $heroInfo2, self::$_BD['Terrian'], 0, array($atkPos, $defAimPos), 'ATK');
				$heroAtkForce = $tmpAtkForce['force'];
				//	Logger::debug('atk======atk================'.self::$_BD['CurOpBoutNum'].'==============');
				$heroAddHurt = $tmpAtkForce['add_hurt']; //增加伤害

				if (!empty($tmpAtkForce['setDefUnAtk'])) { //无法攻击
					$defSkillEffect['UNATK']                    = $tmpAtkForce['setDefUnAtk'] + self::$_BD['CurOpBoutNum'];
					self::$_BD[$defOp]['HeroPosData'][$heroId2] = array($defAimPos, $defAimOpFlag, $defSkillEffect);
				}
				if (!empty($tmpAtkForce['setDefUnMove'])) { //无法移动
					$defSkillEffect['UNMOVE']                   = $tmpAtkForce['setDefUnMove'] + self::$_BD['CurOpBoutNum'];
					self::$_BD[$defOp]['HeroPosData'][$heroId2] = array($defAimPos, $defAimOpFlag, $defSkillEffect);
				}
				$logtmp = self::$_BD[$curOp]['HeroDataList'][$heroId1]['army_id'];
				$debugLogTxt .= "英雄#{$heroId1}兵种#{$logtmp}数量#{$leftArmyNum1}攻击力#{$heroAtkForce}\n";
				//Logger::debug("heroId1--------{$heroId1}--------------armyId--------------{$logtmp}-------num---------{$leftArmyNum1}-----------heroAtkForce------------------{$heroAtkForce}\n");
				//计算heroId2 被heroId1攻击的防御力
				$tmpDefForce  = M_Battle_Calc::calcDefForce($heroInfo1, $heroInfo2, self::$_BD['Terrian'], 0, 'ATK');
				$heroDefForce = $tmpDefForce['force'];
				$heroDefHurt  = $tmpDefForce['def_hurt']; //减少伤害

				$logtmp = self::$_BD[$defOp]['HeroDataList'][$heroId2]['army_id'];
				$debugLogTxt .= "英雄#{$heroId2}兵种#{$logtmp}数量#{$leftArmyNum2}防御力#{$heroDefForce}\n";
				//Logger::debug("heroId2-----------------{$heroId2}-------------armyId----------#{$logtmp}-------num---------{$leftArmyNum2}-----------heroDefForce------------------{$heroDefForce}\n");
				//计算heroId1 攻击 heroId2伤害
				$dmg1 = M_Formula::calcBattleDamage($heroAtkForce, $heroDefForce);
				$debugLogTxt .= "单个攻击力#{$heroAtkForce}单个防御力#{$heroDefForce}单个伤害值#{$dmg1}\n";
				$dmg1 *= $heroInfo1['left_num'];
				$dmg1 = round($dmg1);

				$debugLogTxt .= "英雄#{$heroId1}数量#{$heroInfo1['left_num']}产生的总伤害值#{$dmg1}\n";
				$viewDmg1 = $dmg1;

				$tmpLeftDmg = self::$_BD[$defOp]['HeroDataList'][$heroId2]['left_dmg'];
				$debugLogTxt .= "英雄#{$heroId2}被攻击上次累计伤害值#{$tmpLeftDmg}\n";
				$dmg1 += $tmpLeftDmg;
				$dmg1 = $dmg1 * (1 + $heroAddHurt / 100); //增加伤害
				$dmg1 = $dmg1 * (1 - $heroDefHurt / 100); //减少伤害
				$dmg3 = $dmg1 * ($tmpAtkForce['setDefAtkHurt'] / 100); //持续伤害
				$dmg1 = $dmg1 + $dmg3; //持续伤害加成

				$debugLogTxt .= "英雄#{$heroId1}攻击#{$heroId2}总伤害#{$dmg1}\n";
				//  Logger::debug("heroId1-----------{$heroId1}---------heroId2-------------{$heroId2}-----------hurt------------{$dmg1}");
				//计算heroId2剩余兵数
				$hp2 = M_Battle_Calc::calcBattleHp($heroInfo2, 'ATK');
				list($dieArmyNum, $leftDmg) = M_Formula::calcBattleLeftArmyNum($dmg1, $hp2);

				if ($tmpDefForce['setDefRestorAn']) //恢复兵数
				{
					$dieArmyNum = $dieArmyNum * (1 - $tmpDefForce['setDefRestorAn'] / 100);
				}
				//如果出现闪避 无死兵
				if ($tmpDefForce['miss']) {
					$dieArmyNum = 0;
				}
				$leftArmyNum2 = max($heroInfo2['left_num'] - $dieArmyNum, 0);
				$debugLogTxt .= "总伤害#{$dmg1}英雄#{$heroId2}单个生命值#{$hp2}死亡数量#{$dieArmyNum}剩余伤害#{$leftDmg}剩余数量#{$leftArmyNum2}\n";
				//Logger::debug(" -----leftArmyNum2-------{$leftArmyNum2}");
				self::$_BD[$defOp]['HeroDataList'][$heroId2]['left_num'] = $leftArmyNum2;
				// 更新上次heroId1 攻击 heroId2攻击剩余伤害值
				self::$_BD[$defOp]['HeroDataList'][$heroId2]['left_dmg'] = $leftDmg;

				self::$_BD[$defOp]['HeroDataList'][$heroId2]['atk_hurt'] = $dmg3; //持续伤害
				//  Logger::debug('--------crit-------------'.$tmpAtkForce['crit']);
				if ($tmpAtkForce['crit'] && $tmpDefForce['miss']) {
					$fonthit = T_Battle::ATK_CRIT + T_Battle::ATK_MISS;
				} elseif ($tmpAtkForce['crit']) {
					$fonthit = T_Battle::ATK_CRIT;
				}

				//如果无反击 heroid2的攻击力为0
				$dmg4     = $dmg2 = $viewDmg2 = 0;
				$hero1die = false;
				if ($leftArmyNum2 > 0) {
					//=============================反击计算 ==================================
					//如果没有反击过
					if (($defAimOpFlag & T_Battle::OP_HERO_HIT_FLAG) == 0) {
						$wp        = new M_Battle_Path(self::$_BD['MapSize'], self::$_BD['MapCell']);
						$rangeList = $wp->getAtkRange($defAimPos, array($heroInfo2['shot_range_min'], $heroInfo2['shot_range_max']));

						//攻击范围是否正常
						if (M_Battle_AI::checkAtkRange($atkPos, $rangeList)) {
							$backhit = T_Battle::ATK_HIT;
							//因为已经有兵受到伤害 重新取值
							$heroInfo2 = self::$_BD[$defOp]['HeroDataList'][$heroId2];
							//计算heroId2 攻击 heroId1 的 攻击力
							$tmpAtkForce  = M_Battle_Calc::calcAtkForce($heroInfo2, $heroInfo1, 0, 0, array($defAimPos, $atkPos), 'DEF');
							$heroAtkForce = $tmpAtkForce['force'];
							$heroAddHurt  = $tmpAtkForce['add_hurt'];
							if (!empty($tmpAtkForce['setDefUnAtk'])) { //无法攻击
								$atkSkillEffect['UNATK']                    = $tmpAtkForce['setDefUnAtk'] + self::$_BD['CurOpBoutNum'];
								self::$_BD[$curOp]['HeroPosData'][$heroId1] = array($atkPos, $atkOpFlag, $atkSkillEffect);
							}
							if (!empty($tmpAtkForce['setDefUnMove'])) { //无法移动
								$atkSkillEffect['UNMOVE']                   = $tmpAtkForce['setDefUnMove'] + self::$_BD['CurOpBoutNum'];
								self::$_BD[$curOp]['HeroPosData'][$heroId1] = array($atkPos, $atkOpFlag, $atkSkillEffect);
							}

							$debugLogTxt .= "反:英雄#{$heroId2}攻击力#{$heroAtkForce}\n";
							//Logger::debug("fan:heroId2--------{$heroId2}-----------heroAtkForce------------------{$heroAtkForce}\n");
							//计算heroId1 被heroId2攻击的防御力
							$tmpDefForce  = M_Battle_Calc::calcDefForce($heroInfo2, $heroInfo1, 0, 0, 'DEF');
							$heroDefForce = $tmpDefForce['force'];
							$heroDefHurt  = $tmpDefForce['def_hurt'];
							$debugLogTxt .= "反:英雄#{$heroId1}防御力#{$heroDefForce}\n";
							//	Logger::debug("fan:heroId1--------{$heroId1}------heroDefForce--------{$heroDefForce}\n");

							//计算heroId2 攻击 heroId1 的伤害
							$dmg2 = M_Formula::calcBattleDamage($heroAtkForce, $heroDefForce);


							$debugLogTxt .= "反:单个伤害#{$dmg2}\n";
							$dmg2 *= $heroInfo2['left_num'];
							$dmg2 = round($dmg2);
							$debugLogTxt .= "反:英雄#{$heroId2}数量#{$heroInfo2['left_num']}总伤害#{$dmg2}\n";

							$viewDmg2 = $dmg2;

							//获取上次heroId2 攻击 heroId1攻击剩余伤害值
							$tmpLeftDmg = self::$_BD[$curOp]['HeroDataList'][$heroId1]['left_dmg'];
							$debugLogTxt .= "反:英雄#{$heroId1}上次剩余伤害#{$tmpLeftDmg}\n";
							$dmg2 += $tmpLeftDmg;

							$dmg2 = $dmg2 * (1 + $heroAddHurt / 100); //增加伤害
							$dmg2 = $dmg2 * (1 - $heroDefHurt / 100); //减少伤害

							$dmg4 = $dmg2 * ($tmpAtkForce['setDefAtkHurt'] / 100); //持续伤害
							$dmg2 = $dmg2 + $dmg4; //持续伤害加成
							$debugLogTxt .= "反:英雄#{$heroId2}攻击#{$heroId1}累计总伤害#{$dmg2}\n";
							//Logger::debug("fan:heroId2--------{$heroId2}--------------heroId1--------------{$heroId1}-------dmg2---------{$dmg2} ");
							$hp1 = M_Battle_Calc::calcBattleHp($heroInfo1, 'DEF');
							//计算被反击后heroId1剩余兵数
							list($dieArmyNum, $leftDmg) = M_Formula::calcBattleLeftArmyNum($dmg2, $hp1);
							if ($tmpDefForce['setDefRestorAn']) //恢复兵数
							{
								$dieArmyNum = $dieArmyNum * (1 - $tmpDefForce['setDefRestorAn'] / 100);
							}
							//如果出现闪避 无死兵
							if ($tmpDefForce['miss']) {
								$dieArmyNum = 0;
							}
							$debugLogTxt .= "反:总伤害#{$dmg2}英雄#{$heroId1}单个生命值#{$hp1}死亡数量#{$dieArmyNum}剩余伤害#{$leftDmg}\n";
							//$debugLogTxt1 .= "heroId1--------{$heroId1}--------------armyId--------------{$logtmp}-------num---------{$leftArmyNum1}-----------heroAtkForce------------------{$heroAtkForce}\n";
							$leftArmyNum1 = max($heroInfo1['left_num'] - $dieArmyNum, 0);
							$debugLogTxt .= "反:英雄#{$heroId1}剩余数量#{$leftArmyNum1}\n";
							$debugLogTxt .= "计算结束==================\n";
							// Logger::debug('-----------------end--------------');
							self::$_BD[$curOp]['HeroDataList'][$heroId1]['left_num'] = $leftArmyNum1;
							// 更新上次 heroId1被攻击累计伤害值
							self::$_BD[$curOp]['HeroDataList'][$heroId1]['left_dmg'] = $leftDmg;
							self::$_BD[$curOp]['HeroDataList'][$heroId1]['atk_hurt'] = $dmg4; //持续伤害

							//如果$heroId1无剩余兵力 则$heroId1英雄死亡  移除地图上相关数据
							if ($leftArmyNum1 < 1) {
								$hero1die = true;
								//删除阵亡英雄坐标
								unset(self::$_BD['MapCell'][$atkPos]);
								$debugLogTxt .= json_encode(array_keys(self::$_BD[$curOp]['HeroPosData'])) . "\n";
								unset(self::$_BD[$curOp]['HeroPosData'][$heroId1]);
								$debugLogTxt .= "反:清除英雄#{$heroId1}\n";
								$debugLogTxt .= json_encode(array_keys(self::$_BD[$curOp]['HeroPosData'])) . "\n";
							}

							//加防守方已反击标记
							$newOpAct                                   = $defAimOpFlag + T_Battle::OP_HERO_HIT_FLAG;
							self::$_BD[$defOp]['HeroPosData'][$heroId2] = array($defAimPos, $newOpAct, $defSkillEffect);

							if ($tmpAtkForce['crit'] && $tmpDefForce['miss']) {
								$backhit = T_Battle::ATK_CRIT + T_Battle::ATK_MISS;
							} elseif ($tmpAtkForce['crit']) {
								$backhit = T_Battle::ATK_CRIT;
							}

						}
					}

				} else {
					//如果$heroId2无剩余兵力, 则$heroId2英雄死亡  移除地图上相关数据
					Logger::battle("防守方英雄#{$heroId2}死亡 #{$leftArmyNum2}", self::$_BD['Id'], self::$_BD['CurOpBoutNum']);

					unset(self::$_BD['MapCell'][$defAimPos]);
					$debugLogTxt .= json_encode(array_keys(self::$_BD[$defOp]['HeroPosData'])) . "\n";
					unset(self::$_BD[$defOp]['HeroPosData'][$heroId2]);
					$debugLogTxt .= "清除英雄#{$heroId2}\n";
					$debugLogTxt .= json_encode(array_keys(self::$_BD[$defOp]['HeroPosData'])) . "\n";

					//更新防守方战斗中的英雄列表
					$log = "防守方英雄数据 :" . json_encode(self::$_BD[$defOp]['HeroPosData']);
					Logger::battle($log, self::$_BD['Id'], self::$_BD['CurOpBoutNum']);

				}

				//如果没有阵亡 则更新数据
				!$hero1die && self::$_BD[$curOp]['HeroPosData'][$heroId1] = array($atkPos, $atkOpFlag + T_Battle::OP_HERO_ATK_FLAG, $atkSkillEffect);
				$data[$curOp] = array(
					'heroId'      => $heroId1,
					'heroDmg'     => $viewDmg1,
					'leftArmyNum' => $leftArmyNum1,
				);
				$data[$defOp] = array(
					'heroId'      => $heroId2,
					'heroDmg'     => $viewDmg2,
					'leftArmyNum' => $leftArmyNum2,
				);

				$log = "英雄:{$heroId1},战斗后剩余兵力:{$leftArmyNum1}英雄:{$heroId2},战斗后剩余兵力:{$leftArmyNum2}";
				Logger::battle($log, self::$_BD['Id'], self::$_BD['CurOpBoutNum']);

				//回合数|操作方|2|操作方式|英雄ID|目的英雄ID|伤害值|攻击方剩余兵数|反击值|反击方剩余兵数|攻击效果|反击效果(攻击1,无攻击2,暴击3,闪避4)
				$opLogRow = array(self::$_BD['CurOpBoutNum'],
					self::$_BD[$curOp]['CityId'],
					T_Battle::OP_ACT_ATK,
					$isAIOP,
					(int)$heroId1,
					(int)$heroId2,
					$viewDmg1,
					$leftArmyNum1,
					$viewDmg2,
					$leftArmyNum2,
					$fonthit,
					$backhit);

				M_Battle_Calc::addOpLog(self::$_BD['Id'], $opLogRow);
				$data['opLog'] = $opLogRow;
				Logger::battle("OP日志" . json_encode($opLogRow), self::$_BD['Id'], self::$_BD['CurOpBoutNum']);
			}
		}

		//@todo 调试用  error_log($debugLogTxt, 3, '/tmp/binfo');
		return $data;
	}

	/**
	 * 查找攻击范围内最近的目标
	 * @param string $curOp 当前操作方
	 * @param array $heroPos 当前所在坐标(X_Y)
	 * @param string $rangeList array(15_1, 17_1, 17_2, 14_2)
	 * @return string/bool 英雄ID,坐标/不在为false
	 */
	static private function _findNearestAimInAtkRange($curOp, $heroPos, $rangeList) {
		$aimData = false;

		$defOp = $curOp ^ 3;
		//计算距离哪个英雄最近
		$aimPosList = M_Battle_AI::findRoundAim(self::$_BD[$defOp]['HeroPosData'], $heroPos);

		$minDis = 1000;
		foreach ($aimPosList as $aimStr => $vDis) {
			list($defAimId, $defAimPos) = explode(',', $aimStr);

			if ($vDis < $minDis && in_array($defAimPos, $rangeList)) {
				$minDis  = $vDis;
				$aimData = $aimStr;
			}
		}

		return $aimData;
	}

	/**
	 * 选择目标
	 * @author huwei
	 * @param int $curOp 当前操作方
	 * @param int $heroPos 当前操作英雄坐标
	 * @return string  最近的: '英雄ID,英雄位置'
	 */
	static private function _findNearestAimInRound($curOp, $heroPos) {
		$aimData = false;
		if (isset(self::$_BD['Id'])) {
			$defOp = $curOp ^ 3;

			//计算距离哪个英雄最近
			$aimPosList = M_Battle_AI::findRoundAim(self::$_BD[$defOp]['HeroPosData'], $heroPos);

			//获取最近的英雄ID
			$minDis = 1000;
			foreach ($aimPosList as $aimStr => $vDis) {
				if ($vDis < $minDis) {
					$minDis  = $vDis;
					$aimData = $aimStr;
				}
			}
		}


		return $aimData;
	}

	/**
	 * 获取周围目标到自己的距离
	 * @author huwei
	 * @param array $heroPosData 对方英雄的列表
	 * @param string $heroPos 我方英雄的坐标
	 * @return array ([英雄ID,坐标]=>距离, [100,8_1]=>11)
	 */
	static public function findRoundAim($heroPosData, $heroPos) {
		//计算距离哪个英雄最近
		$aimPosList = array();
		foreach ($heroPosData as $kId => $vData) {
			list($vPos, $vOpFlag) = $vData;
			$key              = $kId . ',' . $vPos;
			$aimPosList[$key] = M_Formula::aiCalcDistance($heroPos, $vPos);
		}
		return $aimPosList;
	}

	/**
	 * 最大移动距离
	 * @author huwei
	 * @param string $atkPos
	 * @param array $atkMoveRange
	 * @param array $atkRangeList
	 * @return bool
	 */
	static public function getMoveMaxMove($atkPos, $atkMoveRange, $atkRangeList) {
		$arr1        = $atkRangeList;
		$arr2        = array_keys($atkMoveRange);
		$arr3        = array_intersect($arr2, $arr1);
		$minDistance = 1000;
		$maxPos      = '';

		foreach ($arr3 as $val) {
			$dist = M_Formula::aiCalcDistance($val, $atkPos);
			if ($dist < $minDistance) {
				$minDistance = $dist;
				$maxPos      = $val;
			}
		}

		return $maxPos;
	}

	/**
	 * 检测目标是否在攻击范围内
	 * @param string $aimPos 目标坐标(X_Y)
	 * @param array $rangeList 攻击范围array(min, max)
	 * @return bool (在目标范围true)
	 */
	static public function checkAtkRange($aimPos, $rangeList) {
		$ret = false;
		if (in_array($aimPos, $rangeList)) {
			$ret = true;
		}
		return $ret;
	}
}

?>