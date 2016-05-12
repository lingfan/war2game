<?php

/** 突围控制器 */
class C_Breakout extends C_I {
	/**
	 * 获取城市突围数据
	 * @author chenhui on 20121019
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetCityBout() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$cityId = intval($cityInfo['id']);
		$cityBout = M_BreakOut::getCityBreakOut($cityId);
		if (!empty($cityBout) && is_array($cityBout)) {
			$bout_times_cost = M_Config::getVal('bout_times_cost');
			$data['BattleIdNow'] = intval($cityBout['battle_id']);
			$data['FreeTimesLeft'] = intval($cityBout['free_times_left']);
			$data['BuyTimesLeft'] = intval($cityBout['buy_times_left']);
			$data['CDBout'] = $objPlayer->CD()->toFront(O_CD::TYPE_BOUT);
			$data['NextBuyCost'] = M_Formula::calcAddupPerCost($bout_times_cost, $cityBout['buy_times'] + 1);
			$data['Point'] = intval($cityBout['point']);

			$story = array();
			$arrBoutId = explode(',', $cityBout['breakout_pass']);
			$cityBoutData = $cityBout['breakout_data'];
			if (!empty($arrBoutId) && is_array($arrBoutId)) {
				foreach ($arrBoutId as $passBoutId) {
					$baseBoutInfo = M_BreakOut::baseInfo($passBoutId);
					$baseBoutArr = explode('|', $baseBoutInfo['data']);
					$boutPostNum = count($baseBoutArr);
					//获取最后突围关卡数据
					$tmpData = explode(',', $baseBoutArr[$boutPostNum - 1]);
					//获取当前突围通关积分
					$passPoint = !empty($tmpData[3]) ? intval($tmpData[3]) : 0;

					if (!empty($cityBoutData[$passBoutId]) && M_BreakOut::STATUS_START == $cityBoutData[$passBoutId][0]) {
						$over = ($cityBoutData[$passBoutId][1] > 0) ? range(1, $cityBoutData[$passBoutId][1]) : array(); //已通过关ID数组
						$next = ($cityBoutData[$passBoutId][1] < $boutPostNum) ? ($cityBoutData[$passBoutId][1] + 1) : 0; //下一关ID

						$has = M_BreakOut::getHasAwardIds($baseBoutArr, $cityBoutData[$passBoutId][2]);
						$story[$passBoutId] = array('OVER' => $over, 'NEXT' => $next, 'HAS' => $has, 'PASS_POINT' => $passPoint);
					} else {
						$has = M_BreakOut::getHasAwardIds($baseBoutArr);

						$story[$passBoutId] = array('OVER' => array(), 'NEXT' => M_BreakOut::BREAKOUT_NOT_OPEN, 'HAS' => $has, 'PASS_POINT' => $passPoint);
					}
				}
			}
			$data['Story'] = $story;


			$errNo = '';
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 购买突围次数
	 * @author chenhui on 20121107
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ABuyTimes() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认
		$data = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$bout_times_cost = M_Config::getVal('bout_times_cost');
		$cityId = intval($cityInfo['id']);
		$cityBreakOutInfo = M_BreakOut::getCityBreakOut($cityId);
		$buyTimes = $cityBreakOutInfo['buy_times'];
		$total = M_Formula::calcAddupPerCost($bout_times_cost, $buyTimes + 1);
		if ($cityInfo['mil_pay'] >= $total) {
			$bDecr = $objPlayer->City()->decrCurrency(T_App::MILPAY, $total, B_Log_Trade::E_BuyBoutTimes, $buyTimes + 1);
			$upInfo = array(
				'buy_times_left' => $cityBreakOutInfo['buy_times_left'] + 1,
				'buy_times' => $cityBreakOutInfo['buy_times'] + 1,
			);
			$bAdd = $bDecr && M_BreakOut::updateCityBreakOut($cityId, $upInfo, true);
			if ($bAdd) {

				$errNo = '';

				$data = array(M_Formula::calcAddupPerCost($bout_times_cost, $buyTimes + 2)); //array(下次购买所需军饷)

				$msRow = array(
					'buy_times_left' => $cityBreakOutInfo['buy_times_left'] + 1,
					'next_buy_cost' => M_Formula::calcAddupPerCost($bout_times_cost, $buyTimes + 2),
				);
				M_Sync::addQueue($cityId, M_Sync::KEY_BOUT, $msRow); //同步
			} else {
				$errNo = T_ErrNo::ERR_UPDATE;
			}
		} else {
			$errNo = T_ErrNo::NO_ENOUGH_MILIPAY;
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 突围开始
	 * @author chenhui on 20121019
	 * @param int $boutId 突围ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AStart($boutId) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$boutId = intval($boutId);
		if ($boutId > 0) {
			$baseBoutInfo = M_BreakOut::baseInfo($boutId);
			if (M_BreakOut::BREAKOUT_OPEN == $baseBoutInfo['is_open']) {
				$cityId = intval($cityInfo['id']);
				$cityBreakOutInfo = M_BreakOut::getCityBreakOut($cityId);

				if (in_array($boutId, explode(',', $cityBreakOutInfo['breakout_pass']))) {
					$cityData = $cityBreakOutInfo['breakout_data'];

					$upInfo = array();
					if ($cityBreakOutInfo['free_times_left'] > 0) {
						$upInfo['free_times_left'] = $cityBreakOutInfo['free_times_left'] - 1;
						$errNo = '';
					} else {
						if ($cityBreakOutInfo['buy_times_left'] > 0) {
							$upInfo['buy_times_left'] = $cityBreakOutInfo['buy_times_left'] - 1;
							$errNo = '';
						} else {
							$errNo = T_ErrNo::BOUT_TIMES_OVER_DAY;
						}
					}

					if (empty($errNo)) {
						if (!empty($cityData[$boutId])) {
							if (M_BreakOut::STATUS_START != $cityData[$boutId][0]) {
								$cityData[$boutId][0] = M_BreakOut::STATUS_START;
								$cityData[$boutId][1] = 0;
								$cityData[$boutId][2] = array();
							} else {
								$errNo = T_ErrNo::STATUS_START;
							}
						} else {
							$cityData[$boutId] = array(M_BreakOut::STATUS_START, 0, array()); //状态0/1,本次挑战到关数
						}
					}

					if (empty($errNo)) {
						$upInfo['breakout_data'] = json_encode($cityData);

						$ret = M_BreakOut::updateCityBreakOut($cityId, $upInfo, true);
						if (!empty($ret)) {

							$errNo = '';
							unset($upInfo['breakout_data']);

							$baseBoutArr = explode('|', $baseBoutInfo['data']);
							$has = M_BreakOut::getHasAwardIds($baseBoutArr);

							$msRow = $upInfo;
							$msRow['story'] = array(
								$boutId => array('OVER' => array(), 'NEXT' => 1, 'HAS' => $has),
							);
							M_Sync::addQueue($cityId, M_Sync::KEY_BOUT, $msRow); //同步
						} else {
							$errNo = T_ErrNo::ERR_UPDATE;
						}
					}
				} else {
					$errNo = T_ErrNo::BOUT_CANT_START;
				}
			} else {
				$errNo = T_ErrNo::BREAKOUT_CLOSE;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 突围撤退
	 * @author chenhui on 20121021
	 * @param int $boutId 突围ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function APullout($boutId) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_PARAM; //失败原因默认
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$boutId = intval($boutId);
		if ($boutId > 0) {
			$baseBoutInfo = M_BreakOut::baseInfo($boutId);
			if (M_BreakOut::BREAKOUT_OPEN == $baseBoutInfo['is_open']) {
				$cityId = intval($cityInfo['id']);
				$cityBreakOutInfo = M_BreakOut::getCityBreakOut($cityId);
				$cityData = $cityBreakOutInfo['breakout_data'];

				if (!empty($cityData[$boutId])) //状态0/1,最后通关ID,array(已领奖关ID...)
				{
					if (M_BreakOut::STATUS_END != $cityData[$boutId][0]) {
						unset($cityData[$boutId]);

						$upInfo = array(
							'breakout_data' => json_encode($cityData),
						);
						$ret = M_BreakOut::updateCityBreakOut($cityId, $upInfo, true);
						if (!empty($ret)) {

							$errNo = '';
							$baseBoutArr = explode('|', $baseBoutInfo['data']);

							$has = M_BreakOut::getHasAwardIds($baseBoutArr);

							$msRow = array(
								'story' => array($boutId => array('OVER' => array(), 'NEXT' => M_BreakOut::BREAKOUT_NOT_OPEN, 'HAS' => $has)),
							);
							M_Sync::addQueue($cityId, M_Sync::KEY_BOUT, $msRow); //同步
						} else {
							$errNo = T_ErrNo::ERR_UPDATE;
						}
					} else {
						$errNo = T_ErrNo::STATUS_END;
					}
				} else {
					$errNo = T_ErrNo::BOUT_CITY_DATA_ERR;
				}
			} else {
				$errNo = T_ErrNo::BREAKOUT_CLOSE;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 出征突围
	 * @author chenhui on 20121022
	 * @param string $boutStr '突围ID,关编号从1开始' 逗号拼接
	 * @param string $heroIdList 逗号拼接英雄列表 'id,id,id'
	 * @param int $isAutoFight 是否自动操作[0手动 1自动 2快速]
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AAtk($boutStr = '', $heroIdList = '', $isAutoFight = M_War::FIGHT_TYPE_AUTO) {
		$errNo = T_ErrNo::ERR_PARAM;
		$data = array();
		$boutArr = !empty($boutStr) ? explode(',', $boutStr) : array();
		$attHeroIdArr = !empty($heroIdList) ? explode(',', $heroIdList) : array();
		$heroNum = count($attHeroIdArr);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (in_array($isAutoFight, array(M_War::FIGHT_TYPE_HAND, M_War::FIGHT_TYPE_AUTO, M_War::FIGHT_TYPE_QUICK)) &&
			count($boutArr) == 2 && //检查坐标是否有(突围ID,关编号)
			!empty($boutArr[0]) &&
			!empty($boutArr[1]) &&
			$heroNum > 0 &&
			$heroNum <= M_Config::getVal('hero_num_troop')
		) //检查英雄数量是否正确
		{
			$cityInfo = $objPlayer->getCityBase();
			$boutId = intval($boutArr[0]); //突围ID
			$outpostId = intval($boutArr[1]); //关卡ID(从1开始)
			$cityId = intval($cityInfo['id']);
			$cityBreakOutInfo = M_BreakOut::getCityBreakOut($cityId);
			//var_dump($cityBreakOutInfo);
			if (intval($cityBreakOutInfo['battle_id']) == 0) {
				$cityData = $cityBreakOutInfo['breakout_data'];
				//var_dump($cityData);
				$boutInfo = isset($cityData[$boutId]) ? $cityData[$boutId] : array(); //此突围数据(状态0/1,最后通关ID,array(已领奖关ID,ID))
				$errNo = T_ErrNo::BOUT_CITY_DATA_ERR;
				if (!empty($boutInfo) && is_array($boutInfo)) {
					$errNo = T_ErrNo::STATUS_END;
					if (M_BreakOut::STATUS_START == $boutInfo[0]) {
						$baseInfo = M_BreakOut::baseInfo($boutId);
						$arrStrOutpost = explode('|', $baseInfo['data']);
						$errNo = T_ErrNo::BOUT_OVER_OUTPOST;
						if ($outpostId <= min(count($arrStrOutpost), $boutInfo[1] + 1)) //突围关卡编号
						{
							$err = '';

							$cdIdx = $objPlayer->CD()->getFreeIdx(O_CD::TYPE_BOUT);
							if (!M_Hero::checkHeroStatus($cityId, $attHeroIdArr)) //检测英雄是否空闲 或 不存在 此英雄
							{
								$err = T_ErrNo::HERO_EXIST_FIGHT;
							} else if ((M_War::FIGHT_TYPE_QUICK == $isAutoFight) && !$cdIdx) {
								$err = T_ErrNo::BOUT_QUICK_CD_NOW;
							}

							$errNo = $err;
							if (empty($err)) {
								//构建战斗数据
								$defPosNo = $boutId . '_' . $outpostId; //'突围ID_关编号从1开始'
								$arrOutpost = explode(',', $arrStrOutpost[$outpostId - 1]); //npc部队ID,地图ID,宝箱奖励ID
								$bData = M_War::buildBoutWarBattleData($cityId, $cityInfo['pos_no'], $defPosNo, $arrOutpost, $isAutoFight, $attHeroIdArr);

								//插入战斗队列
								$battleId = M_War::insertWarBattle($bData, $isAutoFight);

								$errNo = T_ErrNo::BATTLE_DATA_ERR;
								if ($battleId) {
									if ($isAutoFight != M_War::FIGHT_TYPE_QUICK) {
										M_Hero::changeHeroFlag($cityId, $attHeroIdArr, T_Hero::FLAG_WAR); //改变英雄状态为战斗中
										M_BreakOut::updateCityBreakOut($cityId, array('battle_id' => $battleId), true); //更新
										M_Sync::addQueue($cityId, M_Sync::KEY_BOUT, array('battle_id_now' => $battleId)); //同步
									} else {
										$objPlayer->CD()->set(O_CD::TYPE_BOUT, $cdIdx, T_Battle::QUICK_TIME);
										$objPlayer->save();
										$msRow = array('breakout_cd' => $objPlayer->CD()->toFront(O_CD::TYPE_BOUT));
										M_Sync::addQueue($cityId, M_Sync::KEY_BOUT, $msRow); //同步
									}

									$errNo = '';
									$data = array('BattleId' => $battleId);
								}
							}
						}
					}
				}
			} else {
				$BD = M_Battle_Info::get($cityBreakOutInfo['battle_id']);
				if (empty($BD)) {
					M_BreakOut::updateCityBreakOut($cityId, array('battle_id' => 0), true); //更新
					Logger::error(array(__METHOD__, 'err battle id at breakout', $cityBreakOutInfo['battle_id']));
				}
				$errNo = T_ErrNo::BOUT_CITY_BATTLE_NOW;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 领取过关宝物奖励
	 * @author chenhui on 20121105
	 * @param int $boutId 突围ID,编号从1开始
	 * @param int $outpostId 关ID,编号从1开始
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AReceTrea($boutId, $outpostId) {

		$errNo = T_ErrNo::ERR_PARAM;
		$data = array();

		$boutId = intval($boutId);
		$outpostId = intval($outpostId);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$cityId = intval($cityInfo['id']);
		$cityBreakOutInfo = M_BreakOut::getCityBreakOut($cityId);
		$cityData = $cityBreakOutInfo['breakout_data'];
		if (!empty($cityData[$boutId]) && is_array($cityData[$boutId])) //状态0/1,最后通关ID,array(已领奖关ID...)
		{
			if ($cityData[$boutId][1] >= $outpostId) {
				if (!in_array($outpostId, $cityData[$boutId][2])) {
					$baseBoutInfo = M_BreakOut::baseInfo($boutId); //突围基础数据
					if (!empty($baseBoutInfo)) {
						$baseBoutArr = explode('|', $baseBoutInfo['data']);
						$tmpData = explode(',', $baseBoutArr[$outpostId - 1]); //npc部队ID,地图ID,宝箱奖励ID,积分
						$awardId = !empty($tmpData[2]) ? intval($tmpData[2]) : 0;
						if ($awardId > 0) {
							$awardArr = M_Award::rateResult($awardId);
							$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task);
							if ($bAward) {
								$data = M_Award::toText($awardArr);

								$cityData[$boutId][2][] = $outpostId; //已领奖关ID
								$upInfo = array(
									'breakout_data' => json_encode($cityData),
								);
								$ret = M_BreakOut::updateCityBreakOut($cityId, $upInfo, true);
								if ($ret) {

									$errNo = '';

									$has = M_BreakOut::getHasAwardIds($baseBoutArr, $cityData[$boutId][2]);
									M_QqShare::check($objPlayer, 'break_out', array('id' => $awardId));
									$msRow = array(
										'story' => array($boutId => array('HAS' => $has)),
									);
									M_Sync::addQueue($cityId, M_Sync::KEY_BOUT, $msRow); //同步
								} else {
									$errNo = T_ErrNo::ERR_UPDATE;
								}
							} else {
								$errNo = T_ErrNo::AWARD_GET_FAIL;
							}
						} else {
							$errNo = T_ErrNo::BOUT_NO_TREA;
						}
					} else {
						$errNo = T_ErrNo::BOUT_OUTPOST_ERR;
					}
				} else {
					$errNo = T_ErrNo::BOUT_CITY_HAD_AWARD;
				}
			} else {
				$errNo = T_ErrNo::BOUT_CITY_NOT_PASS;
			}
		} else {
			$errNo = T_ErrNo::BOUT_CITY_DATA_ERR;
		}

		return B_Common::result($errNo, $data);
	}

}

?>