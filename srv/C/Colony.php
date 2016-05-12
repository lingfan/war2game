<?php

/**
 * 属地接口
 */
class C_Colony extends C_I {
	/**
	 * 获取属地
	 * @author duhuihui on 20121029
	 */
	public function ACityList() {

		$errNo = T_ErrNo::ERR_PARAM;
		$data = array();
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$errNo = '';
		$list = M_ColonyCity::get($cityInfo['id']);

		$ownerMarchList = array();
		$marchList = M_March::getMarchList($cityInfo['id'], M_War::MARCH_OWN_ATK);
		foreach ($marchList as $key => $marchInfo) {
			//Logger::debug(array(__METHOD__, $marchInfo['def_pos'], $key));
			$ownerMarchList[$marchInfo['def_pos']] = $key;
		}

		foreach ($list as $no => $val) {
			$zone = $posx = $poxy = $level = $holdMarchId = $curPosMarchId = 0;
			$nickname = $faceId = '';
			$no = intval($no);

			if (!empty($val[1])) {
				$mapPosNo = $val[1];
				$mapInfo = M_MapWild::getWildMapInfo($mapPosNo);

				if (!empty($mapInfo['city_id'])) {
					if (isset($ownerMarchList[$mapPosNo])) {
						$curPosMarchId = $ownerMarchList[$mapPosNo];
					}

					//获取被占领的城市数据
					$defCityColony = M_ColonyCity::getInfo($mapInfo['city_id']);
					if ($defCityColony['atk_city_id'] > 0) {
						$colonyHoldTime = isset($defCityColony['hold_time']) ? $defCityColony['hold_time'] : 0;
						$holdTimeInterval = M_Config::getVal('hold_city_time_interval');
						$holdTime = $now + (T_App::ONE_HOUR * $holdTimeInterval - $colonyHoldTime);
						if ($defCityColony['atk_march_id'] > 0) {
							$holdMarchId = $defCityColony['atk_march_id'];

							$info = M_March_Info::get($curPosMarchId);
							//Logger::debug(array(__METHOD__, $curPosMarchId, $info));
							if (empty($info)) {
								Logger::error(array(__METHOD__, 'err march id', $curPosMarchId, $mapPosNo));

								$fieldArr = array('march_id' => 0);
								M_MapWild::setWildMapInfo($mapPosNo, $fieldArr);
								M_MapWild::syncWildMapBlockCache($mapPosNo);
								$curPosMarchId = 0;

							}

							$marchType = 1;
						} else {
							$marchType = 0;
							if ($curPosMarchId > 0) {
								$marchType = 2;
							}

						}

						list($zone, $posx, $poxy) = M_MapWild::calcWildMapPosXYByNo($val[1]);

						$cityColonyInfo = M_City::getInfo($mapInfo['city_id']);
						$nickname = $cityColonyInfo['nickname'];
						$level = $cityColonyInfo['level'];
						$faceId = $cityColonyInfo['face_id'];
					} else {
						$val = array($val[0], 0, 0, 0);
						M_ColonyCity::set($cityInfo['id'], $no, $val);
						Logger::error(array(__METHOD__, 'city not occupied', $val));
					}
				} else {
					$val = array($val[0], 0, 0, 0);
					M_ColonyCity::set($cityInfo['id'], $no, $val);
				}
			}

			$data['list'][$no] = array(
				'IsOpen' => $val[0],
				'FaceId' => $faceId,
				'Name' => $nickname, //城市属地的城市昵称
				'PosX' => $posx,
				'PosY' => $poxy,
				'PosArea' => $zone,
				'Level' => $level, //城市属地的城市等级
				'MarchId' => $holdMarchId > 0 ? intval($holdMarchId) : 0,
				'MarchType' => !empty($marchType) ? $marchType : 0,
				'TaxExprieTime' => $val[2],
				'ExprieTime' => !empty($holdTime) ? $holdTime : 0,
				'IntervalTime' => max($val[2] - $now, 0),
			);
		}

		$tmpColony = array();
		$cityColonyInfo = M_ColonyCity::getInfo($cityInfo['id']);

		if (!empty($cityColonyInfo['atk_city_id'])) {
			//获取占领我的城市信息
			$holdCityInfo = M_City::getInfo($cityColonyInfo['atk_city_id']);

			$tmpColony['hold_flag'] = 0; //驻军状态
			if (!empty($cityColonyInfo['atk_march_id'])) {
				$tmpColony['hold_flag'] = 1; //驻军状态
			}

			$tmpColony['nickName'] = $holdCityInfo['nickname']; //占领者的昵称
			$tmpColony['level'] = $holdCityInfo['level']; //占领者的等级
			$cityColonyUnionInfo = M_Union::getInfo($holdCityInfo['union_id']);
			$tmpColony['unionName'] = $cityColonyUnionInfo['name']; //所属联盟
			$tmpColony['posNo'] = M_MapWild::calcWildMapPosXYByNo($holdCityInfo['pos_no']); //占领者的坐标
			$rescueTimes = explode(',', M_Config::getVal('rescue_cd_times'));

			list($diff, $flag) = $objPlayer->CD()->toFront(O_CD::TYPE_RESCUE);
			//剩余时间|当日解救的次数|解救花费的基础军饷|解救花费的军饷上限
			$rescueCD = array(0, $cityColonyInfo['rescue_num'], $rescueTimes[1], $rescueTimes[2]);
			if ($diff > 0) {
				$rescueCD[0] = $diff;
			}
			$tmpColony['rescueCd'] = $objPlayer->CD()->toFront(O_CD::TYPE_RESCUE);
			$tmpColony['SelfRsMhTime'] = T_App::ONE_MINUTE; //自己解救自己的行军时间
		}

		$data['info'] = $tmpColony;

		return B_Common::result($errNo, $data);
	}

	/**
	 * 开启属地
	 * VIP0：默认所有玩家开启1个野地；
	 * VIP5：玩家可以花费500军饷开启第2个野地占领名额；
	 * VIP8：玩家可以花费1000军饷开启第3个野地占领名额；
	 * @author duhuihui on 20121029
	 * @param int $no
	 */
	public function ACityOpen($no) {

		$errNo = T_ErrNo::ERR_PARAM;
		$data = array();
		$no = intval($no);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if ($no > 0) {
			$list = M_ColonyCity::get($cityInfo['id']);

			if (isset($list[$no]) && $list[$no][0] == 0) {
				$vipLevel = $cityInfo['vip_level'];
				$needMilpay = 0;
				$colonyConf = M_Base::getColonyCityConf();

				if (isset($colonyConf[$no][1]) && $vipLevel >= $colonyConf[$no][0]) {
					$needMilpay = intval($colonyConf[$no][1]);
				}
				if ($cityInfo['mil_pay'] >= $needMilpay) {
					$bCost = true;
					if ($needMilpay > 0) {
						$bCost = $objPlayer->City()->decrCurrency(T_App::MILPAY, $needMilpay, B_Log_Trade::E_BuyColony, $needMilpay);
					}
					$param = array(1, 0, 0, 0);
					$ret = $bCost && M_ColonyCity::set($cityInfo['id'], $no, $param);
					if ($ret) {

						$errNo = '';
					}
				} else {
					$errNo = T_ErrNo::NO_ENOUGH_MILIPAY;
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 放弃属地
	 * @author duhuihui on 20121029
	 * @param int $zone
	 * @param int $posX
	 * @param int $posY
	 */
	public function ACityDel($zone, $posX, $posY) {

		$errNo = T_ErrNo::ERR_PARAM;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if ($zone > 0 && $posX > 0 && $posY > 0) {
			$posNo = M_MapWild::calcWildMapPosNoByXY($zone, $posX, $posY);
			$mapInfo = M_MapWild::getWildMapInfo($posNo);
			$colonyInfo = M_ColonyCity::getInfo($mapInfo['city_id']);
			if (!empty($mapInfo) &&
				$mapInfo['type'] == T_Map::WILD_MAP_CELL_CITY &&
				!empty($colonyInfo['atk_city_id'])
			) {
				if ($colonyInfo['atk_city_id'] == $cityInfo['id']) { //如果有部队 先撤回部队
					if ($colonyInfo['atk_march_id'] > 0) {
						M_March::setMarchBack($colonyInfo['atk_march_id']);
					}
				}

				//更新自己的属地信息
				$ret = M_ColonyCity::del($cityInfo['id'], $posNo);
				if ($ret) {

					$errNo = '';
				}
			} else {
				$errNo = T_ErrNo::WILD_POS_IS_SPACE;
			}

		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 税收属地
	 * @author duhuihui on 20121029
	 * @param int $zone
	 * @param int $posX
	 * @param int $posY
	 */
	public function ACityTax($zone = 0, $posX = 0, $posY = 0) {
		$now = time();

		$errNo = T_ErrNo::ERR_PARAM;
		$data = array();
		$zone = intval($zone);
		$posX = intval($posX);
		$posY = intval($posY);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if ($zone > 0 && $posX > 0 && $posY > 0) {
			$posNo = M_MapWild::calcWildMapPosNoByXY($zone, $posX, $posY);
			$list = M_ColonyCity::get($cityInfo['id']);
			foreach ($list as $no => $val) {
				if ($val[0] == 1 &&
					$val[1] == $posNo &&
					$val[2] < $now
				) //占领过期时间
				{
					$mapInfo = M_MapWild::getWildMapInfo($val[1]);
					$err = '';
					if (!empty($mapInfo)) {
						if ($val[3] != date('Ymd')) { //如果日期不同 则重新计算次数
							$val[3] = date('Ymd');
						}

						$expireTime = $now + M_Config::getVal('tax_cd') * T_App::ONE_MINUTE;
						$param = array(1, intval($val[1]), $expireTime, $val[3]);
						$ret = M_ColonyCity::set($cityInfo['id'], $no, $param);
						if ($ret) {
							$objRes = $objPlayer->Res();
							$cityResInfo = M_ColonyCity::getTempWareHouse($mapInfo['city_id']);

							$objRes->incr('gold', $cityResInfo['gold'], true);
							$objRes->incr('food', $cityResInfo['food'], true);
							$objRes->incr('oil', $cityResInfo['oil'], true);

							$objPlayer->save();

							$errNo = '';
							$data = array(
								'food' => round($cityResInfo['food'], 0),
								'oil' => round($cityResInfo['oil'], 0),
								'gold' => round($cityResInfo['gold'], 0),
								'TaxExprieTime' => $expireTime,
							);
							$arr = array('food' => 0, 'oil' => 0, 'gold' => 0);
							M_ColonyCity::setTempWareHouse($mapInfo['city_id'], $arr);
						} else {
							$errNo = T_ErrNo::WILD_POS_IS_SPACE;
						}
					}
					break;
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 获取属地
	 * @author huwei on 20120307
	 */
	public function ANpcList() {

		$errNo = T_ErrNo::ERR_PARAM;
		$data = array();
		$now = time();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (!empty($cityInfo['id'])) {

			$errNo = '';
			$colonyNpcList = $objPlayer->ColonyNpc()->get();
			foreach ($colonyNpcList as $no => $val) {

				if (!empty($val[1])) {
					$mapPosNo = $val[1];
					$mapInfo = M_MapWild::getWildMapInfo($mapPosNo);
					$holdExprie = $val[4] - $now;

					if (empty($mapInfo['npc_id']) || $mapInfo['city_id'] != $cityInfo['id'] || $holdExprie < 0) {
						$objPlayer->ColonyNpc()->open($no);
					}

					if ($mapInfo['march_id'] > 0) {
						$marchId = $mapInfo['march_id'];
						$info = M_March_Info::get($marchId);
						if (empty($info)) {
							M_MapWild::setWildMapInfo($mapPosNo, array('march_id' => 0));
							M_MapWild::syncWildMapBlockCache($mapPosNo);
							$marchId = 0;
						}
					}

				}

				$data[$no] = $objPlayer->ColonyNpc()->buildSyncData($no);

			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 开启属地
	 * VIP0：默认所有玩家开启1个野地；
	 * VIP5：玩家可以花费500军饷开启第2个野地占领名额；
	 * VIP8：玩家可以花费1000军饷开启第3个野地占领名额；
	 * @author huwei on 20120307
	 * @param int $no
	 */
	public function ANpcOpen($no) {

		$errNo = T_ErrNo::ERR_PARAM;
		$data = array();
		$no = intval($no);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (!empty($cityInfo['id']) && $no > 0) {

			$colonyNpcList = $objPlayer->ColonyNpc()->get();
			if (isset($colonyNpcList[$no]) && $colonyNpcList[$no][0] == 0) {
				$vipLevel = $cityInfo['vip_level'];

				$needMilpay = 0;
				$colonyConf = M_Base::getColonyNpcConf();

				if (isset($colonyConf[$no][1]) && $vipLevel >= $colonyConf[$no][0]) {
					$needMilpay = intval($colonyConf[$no][1]);
				}

				$errNo = T_ErrNo::NO_ENOUGH_MILIPAY;
				if ($cityInfo['mil_pay'] >= $needMilpay) {
					$bCost = $objPlayer->City()->decrCurrency(T_App::MILPAY, $needMilpay, B_Log_Trade::E_BuyColony, $no);
					$ret = $bCost && $objPlayer->ColonyNpc()->open($no);
					if ($ret) {
						$errNo = '';
					}
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 放弃属地
	 * @author huwei on 20120307
	 * @param int $zone
	 * @param int $posX
	 * @param int $posY
	 */
	public function ANpcDel($zone, $posX, $posY) {

		$errNo = T_ErrNo::ERR_PARAM;
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (!empty($cityInfo['id']) && $zone > 0 && $posX > 0 && $posY > 0) {
			$posNo = M_MapWild::calcWildMapPosNoByXY($zone, $posX, $posY);
			$mapInfo = M_MapWild::getWildMapInfo($posNo);

			list($no,) = $objPlayer->ColonyNpc()->getNoByPos($posNo);
			$errNo = T_ErrNo::WILD_POS_IS_SPACE;
			if (!empty($no) && !empty($mapInfo) && $mapInfo['type'] == T_Map::WILD_MAP_CELL_NPC) {
				$err = '';
				if ($mapInfo['city_id'] == $cityInfo['id']) {
					//如果有部队 先撤回部队
					if ($mapInfo['march_id'] > 0) {
						$marchInfo = M_March_Info::get($mapInfo['march_id']);
						if ($marchInfo['flag'] == MMarch::MARCH_FLAG_BATTLE) {
							$err = T_ErrNo::MARCH_ON_FIGHT;
						} else {
							M_March::setMarchBack($mapInfo['march_id']);
						}

					}
				}

				//更新自己的属地信息
				$errNo = $err;
				if (empty($err)) {
					$ret = $objPlayer->ColonyNpc()->del($no);
					if ($ret) {
						$errNo = '';
					}
				}
			}

		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 探索属地
	 * @author huwei on 20120307
	 * @param int $zone
	 * @param int $posX
	 * @param int $posY
	 */
	public function ANpcExplore($zone = 0, $posX = 0, $posY = 0) {
		$now = time();

		$errNo = T_ErrNo::ERR_PARAM;
		$data = array();
		$zone = intval($zone);
		$posX = intval($posX);
		$posY = intval($posY);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		if (!empty($cityInfo['id']) && $zone > 0 && $posX > 0 && $posY > 0) {
			$posNo = M_MapWild::calcWildMapPosNoByXY($zone, $posX, $posY);
			list($no, $val) = $objPlayer->ColonyNpc()->getNoByPos($posNo);

			if ($val[0] == 1 &&
				$val[3] < $now &&
				$val[4] > $now
			) {
				//<探索时间 >占领时间
				$mapInfo = M_MapWild::getWildMapInfo($val[1]);
				$errNo = T_ErrNo::WILD_POS_IS_SPACE;
				if (!empty($mapInfo)) {
					//可以探索 获取探索列表
					$ret = $objPlayer->ColonyNpc()->explore($no);
					if ($ret) {
						$npcInfo = M_NPC::getInfo($mapInfo['npc_id']);
						$eventId = 0;
						$awardTxt = array();
						if (!empty($npcInfo)) {
							$eventData = json_decode($npcInfo['probe_event_data'], true);
							$eventId = B_Utils::dice($eventData);
							$probeInfo = M_Probe::getInfo($eventId);

							$awardArr = M_Award::rateResult($probeInfo['award_id']);
							$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Probe);
							$awardTxt = M_Award::toText($awardArr);

							$activenessNum = $objPlayer->Liveness()->check(M_Liveness::GET_POINT_EXPLORE);
							$objPlayer->save();
							if (!empty($activenessNum)) {
								$awardTxt[] = array('item', 'activeness', array(T_Lang::ACTIVENESS), $activenessNum);
							}
						}

						$errNo = '';

						$colonyNpcList = $objPlayer->ColonyNpc()->get();

						$data = array(
							'EventNo' => $eventId,
							'EventAward' => $awardTxt,
							'ExploreTimes' => $colonyNpcList[$no][2],
							'ExploreExprieTime' => $colonyNpcList[$no][3],
						);
					}
				}
			}
		}

		return B_Common::result($errNo, $data);

	}
}

?>