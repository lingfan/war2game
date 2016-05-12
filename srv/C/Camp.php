<?php

/**
 * 据点接口
 */
class C_Camp extends C_I {
	/**
	 * 据点列表
	 * @author huwei
	 */
	public function AList() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$curWeek = date('w');
		$curTime = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$list = M_Base::campaignAll();

		foreach ($list as $id => $val) {
			list($begh, $begi, $begs) = explode(':', $val['open_start_time']);
			$sysStartTime = mktime($begh, $begi, $begs, date('m'), date('d'), date('Y'));
			//开始报名时间
			$sysJoinTime = $sysStartTime - T_App::ONE_DAY;

			list($endh, $endi, $ends) = explode(':', $val['open_end_time']);
			$sysEndTime = mktime($endh, $endi, $ends, date('m'), date('d'), date('Y'));

			$isOpen = false;
			if ((M_Campaign::$campOpenWeek[$curWeek] & $val['open_week']) > 0 &&
				$curTime >= $sysStartTime &&
				$curTime <= $sysEndTime
			) {
				$isOpen = true;
			}

			$campDetail = M_Campaign::getInfo($id);

			$unionName = $unionOwner = '';
			if ($campDetail['owner_union_id'] > 0) {
				$unionInfo = M_Union::getInfo($campDetail['owner_union_id']);
				if ($unionInfo) {
					$unionName = $unionInfo['name'];
					$unionOwner = $unionInfo['boss'];
				}
			}

			//能否申请加入
			//@todo 申请时间开服前24小时
			$joinFlag = 0; //未加入
			if ($cityInfo['union_id'] > 0) { //判断是否加入过
				$myUnionInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
				if ($myUnionInfo['position'] > 0) { //军团长和副团长才有权限
					$jionList = json_decode($campDetail['join_union_ids'], true);
					if (in_array($cityInfo['union_id'], $jionList)) {
						$joinFlag = 1; //已加入
					}
				}
			}

			$times = M_Campaign::getExploreTimes($id, $cityInfo['union_id'], $cityInfo['id']);
			$base = $list[$id];
			$data[] = array(
				'Id' => $id,
				'Title' => $val['title'],
				'Desc' => $val['desc'],
				'OpenWeek' => $val['open_week'],
				'BegTime' => $val['open_start_time'],
				'EndTime' => $val['open_end_time'],
				'IsOpen' => $isOpen ? 1 : 0,
				'UnionId' => $campDetail['owner_union_id'],
				'UnionName' => $unionName,
				'UnionOwner' => $unionOwner,
				'JoinFlag' => $joinFlag,
				'ExploreTimes' => $times,
				'MaxExploreTimes' => $base['probe_times'],
				'DrawAward' => !empty($campDetail['had_award']) ? 1 : 0,

			);
		}
		$errNo = '';

		return B_Common::result($errNo, $data);

	}

	/**
	 * 加入据点争夺
	 * @author huwei
	 * @param int $campId 据点ID
	 */
	public function AJoin($campId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = $newJoinList = array();

		$curWeek = date('w');
		$curTime = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$campId = intval($campId);
		if (!empty($campId) && $cityInfo['union_id'] > 0
		) {
			$campBaseList = M_Base::campaignAll();
			if (isset($campBaseList[$campId])) {
				$campBaseInfo = $campBaseList[$campId];
				list($h, $i, $s) = explode(':', $campBaseInfo['open_start_time']);
				$sysStartTime = mktime($h, $i, $s, date('m'), date('d'), date('Y'));
				$sysJoinTime = $sysStartTime - T_App::ONE_DAY;
				list($h, $i, $s) = explode(':', $campBaseInfo['open_end_time']);
				$sysEndTime = mktime($h, $i, $s, date('m'), date('d'), date('Y'));

				$err = '';
				if ((M_Campaign::$campOpenWeek[$curWeek] & $campBaseInfo['open_week']) <= 0 ||
					$curTime < $sysJoinTime ||
					$curTime > $sysStartTime
				) {
					$errNo = T_ErrNo::CAMPAIGN_NO_OPEN;
				} else { //判断是否加入过
					$myUnionInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);

					if ($myUnionInfo['position'] > 0) { //军团长和副团长才有权限
						$campDetail = M_Campaign::getInfo($campId);
						$jionList = (array)json_decode($campDetail['join_union_ids'], true);
						if (in_array($cityInfo['union_id'], $jionList)) {
							$errNo = T_ErrNo::CAMPAIGN_HAD_JOIN;
						} else {
							array_push($jionList, $cityInfo['union_id']);
							$upInfo = array('join_union_ids' => json_encode($jionList));
							$ret = M_Campaign::setInfo($campId, $upInfo);
							if ($ret) {

								$errNo = '';
							}
						}
					} else {
						$errNo = T_ErrNo::CAMPAIGN_NO_PERM;
					}
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 退出据点争夺
	 * @author huwei
	 * @param int $campId 据点ID
	 */
	public function AQuit($campId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = $newJoinList = array();

		$curWeek = date('w');
		$curTime = time();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$campId = intval($campId);
		if (!empty($campId) && $cityInfo['union_id'] > 0
		) {
			$campBaseList = M_Base::campaignAll();
			if (isset($campBaseList[$campId])) {
				$campBaseInfo = $campBaseList[$campId];

				list($h, $i, $s) = explode(':', $campBaseInfo['open_start_time']);
				$sysStartTime = mktime($h, $i, $s, date('m'), date('d'), date('Y'));
				$sysJoinTime = $sysStartTime - T_App::ONE_DAY;
				list($h, $i, $s) = explode(':', $campBaseInfo['open_end_time']);
				$sysEndTime = mktime($h, $i, $s, date('m'), date('d'), date('Y'));

				if ((M_Campaign::$campOpenWeek[$curWeek] & $campBaseInfo['open_week']) == 0 ||
					$curTime < $sysJoinTime ||
					$curTime > $sysStartTime
				) {
					$errNo = T_ErrNo::CAMPAIGN_NO_OPEN;
				} else { //判断是否加入过
					$campDetail = M_Campaign::getInfo($campId);
					$jionList = (array)json_decode($campDetail['join_union_ids'], true);

					if (in_array($cityInfo['union_id'], $jionList)) {
						$myUnionInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);

						if ($myUnionInfo['position'] > 0) { //军团长和副团长才有权限
							$jionList = array_flip($jionList);
							unset($jionList[$cityInfo['union_id']]);
							$newJoinList = array_flip($jionList);
							$upInfo = array('join_union_ids' => json_encode($newJoinList));
							$ret = M_Campaign::setInfo($campId, $upInfo);
							if ($ret) {

								$errNo = '';
							}
						} else {
							$errNo = T_ErrNo::CAMPAIGN_NO_PERM;
						}
					} else {
						$errNo = T_ErrNo::CAMPAIGN_NO_JOIN;
					}
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 据点巡逻
	 * @author huwei
	 * @param int $campId 据点ID
	 */
	public function AExplore($campId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = $newJoinList = array();

		$curWeek = date('w');
		$curTime = time();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$campId = intval($campId);
		if (!empty($campId) && $cityInfo['union_id'] > 0) {
			$campBaseList = M_Base::campaignAll();
			if (isset($campBaseList[$campId])) {
				$campBaseInfo = $campBaseList[$campId];
				$campDetail = M_Campaign::getInfo($campId);

				if ($campDetail['owner_union_id'] == $cityInfo['union_id']) {
					//次数判断
					$times = M_Campaign::getExploreTimes($campId, $cityInfo['union_id'], $cityInfo['id']);
					if ($times < $campBaseInfo['probe_times']) {
						$times = $times + 1;
						$ret = M_Campaign::setExploreTimes($campId, $cityInfo['union_id'], $cityInfo['id'], $times);
						if ($ret) {
							$eventData = json_decode($campBaseInfo['probe_event_data'], true);

							$eventId = B_Utils::dice($eventData);
							$probeInfo = M_Probe::getInfo($eventId);

							$awardArr = M_Award::rateResult($probeInfo['award_id']);
							$objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Probe);

							$errNo = '';
							$data = array(
								'EventNo' => $eventId,
								'EventAward' => M_Award::toText($awardArr),
								'ExploreTimes' => $times,
								'LeftTimes' => $campBaseInfo['probe_times'] - $times,
							);
						}
					} else {
						$errNo = T_ErrNo::CAMPAIGN_NO_TIMES;
					}
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 抽取团长奖励
	 * @author huwei
	 * @param int $campId 据点ID
	 */
	public function ADrawAward($campId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$campId = intval($campId);
		if (!empty($campId) && $cityInfo['union_id'] > 0
		) {
			$campBaseList = M_Base::campaignAll();
			if (isset($campBaseList[$campId])) {
				$campBaseInfo = $campBaseList[$campId];
				$campDetail = M_Campaign::getInfo($campId);
				if ($campDetail['owner_union_id'] == $cityInfo['union_id'] &&
					empty($campDetail['had_award'])
				) {
					$unionMember = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);

					if ($unionMember['position'] == M_Union::UNION_MEMBER_TOP) { //军团长才可以领取
						$upInfo = array('had_award' => 1);
						$ret = M_Campaign::setInfo($campId, $upInfo);
						if ($ret) {
							$vipLv = $cityInfo['vip_level'];

							$awardArr = M_Award::rateResult($campBaseInfo['award_id']);
							$objPlayer->City()->toAward($awardArr, __METHOD__);

							$errNo = '';
							$data = array(
								'Award' => M_Award::toText($awardArr),
							);
						}
					} else {
						$errNo = T_ErrNo::CAMPAIGN_AWARD_NO_PERM;
					}
				} else {
					$errNo = T_ErrNo::CAMPAIGN_AWARD_HAD;
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 据点中自己的行军部队
	 * @author huwei
	 * @param int $campId
	 */
	public function AMarchList($campId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$curWeek = date('w');
		$curTime = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$campId = intval($campId);
		if (!empty($campId)) {

			$errNo = '';
			$list = M_March::getCampMarchList($cityInfo['id'], $campId);
			if ($list) {
				foreach ($list as $marchInfo) {
					$data[] = array(
						'AtkCityId' => $marchInfo['atk_city_id'],
						'MarchId' => $marchInfo['id'],
						'AtkNickName' => $marchInfo['atk_nickname'],
						'MarchFlag' => $marchInfo['flag'],
						'StartTime' => $marchInfo['create_at'],
						'ArrivedTime' => $marchInfo['arrived_time'],
						'AttPos' => M_MapWild::calcWildMapPosXYByNo($marchInfo['start_pos_ext']),
						'DefPos' => M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']),
						'CampStartPos' => M_MapWild::calcWildMapPosXYByNo($marchInfo['start_pos_ext']),
					);
				}
			}
		}

		return B_Common::result($errNo, $data);
	}


	/**
	 * 据点基地的排队队列
	 * @author huwei
	 * @param int $campId 据点ID
	 * @param int $defLineNo 基地编号
	 */
	public function AQueue($campId = 0, $defLineNo = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$curWeek = date('w');
		$curTime = time();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$campId = intval($campId);
		$defLineNo = strval($defLineNo);
		if (!empty($campId) && !empty($defLineNo)) {
			$campDetail = M_Campaign::getInfo($campId);
			//据点编号
			$defLineNoField = 'no_' . $defLineNo;

			if (isset($campDetail[$defLineNoField])) {
				$marchArr = array();
				$defLinePos = M_MapWild::calcWildMapPosNoByXY(T_App::MAP_CAMPAIGN, $campId, $defLineNo);
				$obj_ml = new M_March_List($defLinePos);
				//攻击据点 排队中的部队ID
				$idList = $obj_ml->get();

				$holdInfo = json_decode($campDetail[$defLineNoField], true);
				//据点驻守的部队ID
				$holdMarchs = $holdInfo[1];

				foreach ($idList as $marchId) {
					if (!empty($marchId) && !in_array($marchId, $holdMarchs)) { //进攻队列 非驻守军队
						$marchInfo = M_March_Info::get($marchId);
						if (!empty($marchInfo['id'])) {
							$atkCityInfo = M_City::getInfo($marchInfo['atk_city_id']);
							$marchArr[] = array(
								'AtkCityId' => $marchInfo['atk_city_id'],
								'MarchId' => $marchInfo['id'],
								'UnionId' => $atkCityInfo['union_id'],
								'AtkNickName' => $marchInfo['atk_nickname'],
								'MarchFlag' => $marchInfo['flag'],
								'ArrivedTime' => $marchInfo['arrived_time'],
							);
						}
					}
				}

				$errNo = '';
				$data = $marchArr;
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 据点基地的驻守
	 * @author huwei
	 * @param int $id 据点ID
	 * @param int $no 基地编号
	 * @param array $heroIdList 英雄ID列表
	 * @param int $isAuto 自动战斗1, 手动战斗0
	 */
	public function AHold($campId = 0, $defLineNo = 0, $heroIdList = '', $isAuto = 1, $spPercent = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = $newJoinList = array();

		$curWeek = date('w');
		$curTime = time();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$campId = intval($campId);
		$defLineNo = strval($defLineNo);
		$tmpArr = !empty($heroIdList) ? explode(',', $heroIdList) : array();

		$attHeroIdArr = array_flip(array_flip($tmpArr));

		$heroNum = count($attHeroIdArr);

		$heroConf = M_Config::getVal();
		if (!empty($campId) && !empty($defLineNo) && $cityInfo['union_id'] > 0 && $heroNum > 0 && $heroNum <= $heroConf['hero_num_troop']
		) //检查英雄数量是否正确
		{
			$campDetail = M_Campaign::getInfo($campId);
			//据点编号
			$defLineNoField = 'no_' . $defLineNo;
			if (isset($campDetail[$defLineNoField])) {
				$spPercent = 0;
				$vipLv = $cityInfo['vip_level'];
				$needMilPay = M_Vip::getDecrMarchTimeCost($vipLv, $spPercent);

				$campBaseList = M_Base::campaignAll();
				$campBaseInfo = $campBaseList[$campId];

				list($h, $i, $s) = explode(':', $campBaseInfo['open_start_time']);
				$sysStartTime = mktime($h, $i, $s, date('m'), date('d'), date('Y'));
				//开始驻扎时间
				$sysHoldTime = $sysStartTime - M_Campaign::START_HOLD_TIME * T_App::ONE_DAY;
				list($h, $i, $s) = explode(':', $campBaseInfo['open_end_time']);
				$sysEndTime = mktime($h, $i, $s, date('m'), date('d'), date('Y'));

				//获取据点中某个基地的信息(联盟ID,主防行军ID,(协防行军id1,协防行军id2))
				$defLineNoInfo = json_decode($campDetail[$defLineNoField]);
				$holdUnionId = $defLineNoInfo[0];

				$joinList = json_decode($campDetail['join_union_ids'], true);
				$canHold = false;

				if ((M_Campaign::$campOpenWeek[$curWeek] & $campBaseInfo['open_week']) > 0 &&
					$curTime < $sysEndTime
				) {
					if ($campDetail['owner_union_id'] == $cityInfo['union_id'] &&
						$curTime > $sysHoldTime
					) { //自己联盟属地
						$canHold = true;
					} else if (in_array($cityInfo['union_id'], $joinList) &&
						$curTime > $sysStartTime
					) { //报名过的联盟
						$canHold = true;
					}
				}

				if (!$canHold) {
					//不可以驻军
					$errNo = T_ErrNo::CAMPAIGN_NO_OPEN;
				} else if (!M_Vip::isDecrMarchTime($vipLv, $spPercent)) //VIP减少出征时间
				{
					$errNo = T_ErrNo::VIP_NOT_LEVEL;
				} else if ($needMilPay > 0 && $cityInfo['mil_pay'] < $needMilPay) {
					$errNo = T_ErrNo::NO_ENOUGH_MILIPAY;
				} else if (!isset($campDetail[$defLineNoField])) {
					$errNo = T_ErrNo::CAMPAIGN_NO_EXISTS;
				} else if ($defLineNo{0} != 1) { // 必须从第一防线开始
					$errNo = T_ErrNo::CAMPAIGN_NO_HOLD;
				} else if (M_March::getMarchCampMaxNum($cityInfo['id'], $cityInfo['pos_no'], $campId) >= M_Config::getVal('march_camp_max_num')) { //据点出征部队数量限制的
					$errNo = T_ErrNo::MARCH_CAMP_MAX_NUM;
				} else {
					//获取出征信息
					$tmpMarchData = M_Hero::getArmyMarchInfo($objPlayer, $attHeroIdArr);

					if (!empty($tmpMarchData[0])) {
						$atkInfo = array(
							'city_id' => $cityInfo['id'],
							'nickname' => $cityInfo['nickname'],
							'pos_no' => $cityInfo['pos_no'],
							'gender' => $cityInfo['gender'],
							'face_id' => $cityInfo['face_id'],
						);

						list($npcId, $warBgNo) = explode('|', $campBaseInfo[$defLineNoField]);

						$npcInfo = M_NPC::getInfo($npcId);

						$defInfo = array(
							'city_id' => 0,
							'nickname' => $npcInfo['nickname'],
							'pos_no' => M_MapWild::calcWildMapPosNoByXY(T_App::MAP_CAMPAIGN, $campId, $defLineNo),
							'gender' => 1,
							'face_id' => $npcInfo['face_id'],
						);


						$bCost = true;
						if ($spPercent > 0) {
							$bCost = false;
						}
						if ($needMilPay > 0) {
							$bCost = $objPlayer->City()->decrCurrency(T_App::MILPAY, $needMilPay, B_Log_Trade::E_ReductionMarchTime, $spPercent);
						}

						if ($bCost) {
							$marchTime = intval($defLineNo{0}) * M_Campaign::MARCH_TIME * T_App::ONE_MINUTE;
							$marchTime = T_App::ONE_MINUTE;
							$marchTime = round($marchTime * ((100 - $spPercent) / 100));
							$arrivedTime = $curTime + $marchTime;
							$marchId = M_March::buildCampaignMarch($atkInfo, $defInfo, $attHeroIdArr, $arrivedTime, $isAuto);
							if ($marchId) {
								//更新缓存
								//M_March::syncOutForcesById($cityInfo['id']);
								M_March::syncMarch2Front($marchId);
								$data = array(
									'MarchId' => $marchId,
									'MarchTime' => $marchTime,
								);

								$errNo = '';
							}
						}
					} else {
						$errNo = T_ErrNo::ARMY_MARCH_FAIL;
					}
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 据点中的基地移动
	 * @author huwei
	 * @param int $campId
	 * @param int $begDefLineNo
	 * @param int $endDefLineNo
	 * @param int $marchId
	 */
	public function AMove($campId = 0, $begDefLineNo = 0, $endDefLineNo = 0, $marchId = 0) {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = $newJoinList = array();

		$curTime = time();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$campId = intval($campId);
		if (!empty($campId) && !empty($begDefLineNo) && !empty($endDefLineNo) && !empty($marchId) && $cityInfo['union_id'] > 0
		) {
			$campBaseList = M_Base::campaignAll();
			$campBaseInfo = $campBaseList[$campId];
			list($h, $i, $s) = explode(':', $campBaseInfo['open_end_time']);
			$sysEndTime = mktime($h, $i, $s, date('m'), date('d'), date('Y'));

			$campDetail = M_Campaign::getInfo($campId);
			$begDefLineNo = strval($begDefLineNo);
			$endDefLineNo = strval($endDefLineNo);
			$begDefLineNoField = 'no_' . $begDefLineNo;
			$endDefLineNoField = 'no_' . $endDefLineNo;

			if ($curTime > $sysEndTime) {
				$errNo = T_ErrNo::CAMPAIGN_NO_OPEN;
			} else if (isset($campDetail[$begDefLineNoField]) &&
				isset($campDetail[$endDefLineNoField])
			) {
				$errNo = T_ErrNo::MARCH_NO_EXIST;
				$marchInfo = M_March_Info::get($marchId);
				if ($marchInfo['atk_city_id'] == $cityInfo['id'] &&
					$marchInfo['flag'] == M_March::MARCH_FLAG_HOLD
				) { //自己的行军记录
					$diff = $endDefLineNo{0} - $begDefLineNo{0};

					$errNo = T_ErrNo::CAMPAIGN_NO_ATK;
					if (abs($diff) < 2) { //不能夸防线战斗
						list($begUnionId, $begMarchIds) = json_decode($campDetail[$begDefLineNoField], true);

						$errNo = T_ErrNo::CAMPAIGN_NO_MARCH;

						if (in_array($marchId, $begMarchIds)) {
							//@todo 删除老基地中的行军id
							$bUp = M_Campaign::delCampainHoldId($campDetail, $begDefLineNoField, $marchId);
							if ($bUp) {
								//删除老的基地的数据
								$defLinePos = M_MapWild::calcWildMapPosNoByXY(T_App::MAP_CAMPAIGN, $campId, $begDefLineNo);
								$obj_ml = new M_March_List($defLinePos);
								$bDel = $obj_ml->del($marchId);
								if ($bDel) {
									$campBaseList = M_Base::campaignAll();

									list($defNpcId, $warBgNo) = explode('|', $campBaseList[$campId][$endDefLineNoField]);

									$npcInfo = M_NPC::getInfo($defNpcId);
									$diff = max(1, $diff);
									$arrivedTime = $curTime + $diff * T_App::ONE_MINUTE * M_Campaign::MARCH_TIME;

									$upData = array(
										'id' => $marchId,
										'def_nickname' => $npcInfo['nickname'],
										'def_pos' => M_MapWild::calcWildMapPosNoByXY(T_App::MAP_CAMPAIGN, $campId, $endDefLineNo),
										'flag' => M_March::MARCH_FLAG_MOVE,
										'arrived_time' => $arrivedTime,
										'create_at' => $curTime,
										'start_pos_ext' => M_MapWild::calcWildMapPosNoByXY(T_App::MAP_CAMPAIGN, $campId, $begDefLineNo),
									);
									//更新行军记录
									$ret = M_March_Info::set($upData);
									if ($ret) {
										//设置英雄状态 [驻守 => 移动]
										$bFalg = M_Hero::changeHeroFlag($marchInfo['atk_city_id'], json_decode($marchInfo['hero_list'], true), T_Hero::FLAG_MOVE);
										if ($bFalg) {
											$syncMarchData = array(
												$marchInfo['id'] => array(
													'AttCityId' => $marchInfo['atk_city_id'],
													'DefCityId' => 0,
													'DefCityNickName' => $npcInfo['nickname'],
													'AttPos' => M_MapWild::calcWildMapPosXYByNo($marchInfo['atk_pos']),
													'DefPos' => array(T_App::MAP_CAMPAIGN, $campId, intval($endDefLineNo)),
													'ArrivedTime' => $arrivedTime,
													'Flag' => M_March::MARCH_FLAG_MOVE,
													'CampStartPos' => array(T_App::MAP_CAMPAIGN, $campId, $begDefLineNo))
											);

											M_Sync::addQueue($marchInfo['atk_city_id'], M_Sync::KEY_MARCH_DATA, $syncMarchData);

											//更新新的基地的数据
											$atkLinePos = M_MapWild::calcWildMapPosNoByXY(T_App::MAP_CAMPAIGN, $campId, $endDefLineNo);
											$obj_ml = new M_March_List($atkLinePos);
											$bAdd = $obj_ml->add($marchId);
											if (!$bAdd) {
												Logger::error(array(__METHOD__, 'Fail for March_List->add', array($atkLinePos, $marchId)));
											}


											$errNo = '';
											$data = array(
												'AtkCityId' => $marchInfo['atk_city_id'],
												'MarchId' => $marchInfo['id'],
												'AtkNickName' => $marchInfo['atk_nickname'],
												'MarchFlag' => $upData['flag'],
												'StartTime' => $upData['create_at'],
												'ArrivedTime' => $arrivedTime,
												'AttPos' => array(T_App::MAP_CAMPAIGN, $campId, $begDefLineNo),
												'DefPos' => array(T_App::MAP_CAMPAIGN, $campId, $endDefLineNo),
												'CampStartPos' => array(T_App::MAP_CAMPAIGN, $campId, $begDefLineNo),
											);
										} else {
											Logger::error(array(__METHOD__, 'Fail for M_Hero::changeHeroFlag', array($marchInfo['atk_city_id'], json_decode($marchInfo['hero_list'], true), T_Hero::FLAG_MOVE)));
										}

									} else {
										Logger::error(array(__METHOD__, 'Fail for M_March_Info::set', $upData));
									}
								} else {
									Logger::debug(array(__METHOD__, 'Fail for March_List->del', array($defLinePos, $marchId)));
								}
							} else {
								Logger::error(array(__METHOD__, 'Fail for M_Campaign::delCampainHoldId', array($campDetail, $begDefLineNoField, $marchId)));
							}
						}
					}
				} else {
					$errNo = T_ErrNo::CAMPAIGN_MARCH_BATTLE;
				}
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 据点中的基地信息
	 * @author huwei
	 * @param $campId $id
	 */
	public function ADetail($campId = 0, $type = 'bases') {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();

		$campId = intval($campId);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (!empty($campId)) {
			$campDetail = M_Campaign::getInfo($campId);
			//据点编号
			if (isset($campDetail['id'])) {
				$union = $noArr = array();
				$list = array();
				$unionIds = json_decode($campDetail['join_union_ids'], true);
				if ($type == 'unions') {
					foreach ($unionIds as $unionId) {
						$unionInfo = M_Union::getInfo($unionId);

						$list[] = array(
							'UnionId' => $unionId,
							'UnionName' => $unionInfo['name'],
							'UnionLeader' => $unionInfo['boss'],
						);
					}
				} else if ($type == 'bases') {
					$joinFlag = 0;
					if (in_array($cityInfo['union_id'], $unionIds)) {
						$joinFlag = 1; //已加入
					}

					$campBaseList = M_Base::campaignAll();
					$campBaseInfo = $campBaseList[$campId];

					//据点编号
					foreach (M_Campaign::$CampaignBase as $defLineNo) {
						$defLineNo = strval($defLineNo);
						$defLineNoField = 'no_' . $defLineNo;
						list($defNpcId, $warMapNo) = explode('|', $campBaseInfo[$defLineNoField]);
						$npcInfo = M_NPC::getInfo($defNpcId);

						$noInfo = json_decode($campDetail[$defLineNoField], true);

						$unionName = '';
						if ($noInfo[0] > 0) {
							$unionInfo = M_Union::getInfo($noInfo[0]);
							$unionName = $unionInfo['name'];
						}

						$marchArr = array();
						$tmp = array();
						$err = array();
						foreach ($noInfo[1] as $marchId) {
							if (!empty($marchId)) {
								$marchInfo = M_March_Info::get($marchId);
								if (!empty($marchInfo)) {
									$marchArr[] = array(
										'MarchId' => $marchInfo['id'],
										'AtkCityId' => $marchInfo['atk_city_id'],
										'AtkNickName' => $marchInfo['atk_nickname'],
										'AttPos' => M_MapWild::calcWildMapPosXYByNo($marchInfo['atk_pos']),
										'DefPos' => M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']),
										'StartTime' => $marchInfo['create_at'],
										'ArrivedTime' => $marchInfo['arrived_time'],
									);
									$tmp[] = $marchId;
								} else {
									$err[] = $marchId;
								}
							}
						}

						if (count($err) > 0) {
							$size = count($tmp);
							for ($i = 0; $i <= 3 - $size; $i++) {
								array_push($tmp, 0);
							}
							$upInfo = array(
								$defLineNoField => json_encode(array($noInfo[0], $tmp))
							);

							Logger::debug(array(__METHOD__, "Error Camp Data", array($campId, $noInfo, $upInfo, $err)));
							//@todo 校正数据
							//M_Campaign::setInfo($campId, $upInfo);
						}

						$list[] = array(
							'No' => $defLineNo,
							'Name' => $npcInfo['nickname'],
							'UnionId' => $noInfo[0],
							'UnionName' => $unionName,
							'MarchIds' => $marchArr,
						);
					}

					$data['JoinFlag'] = $joinFlag;
					$data['OwnerUnionId'] = $campDetail['owner_union_id'];
					$data['HadDrawAward'] = !empty($campDetail['had_award']) ? 1 : 0;
				}

				$data['List'] = $list;

				$errNo = '';
			}
		}

		return B_Common::result($errNo, $data);
	}
}

?>