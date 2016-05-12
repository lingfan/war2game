<?php

/**
 * 爬楼
 */
class C_Floor extends C_I {
	public function AInit() {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$objFloor = $objPlayer->Floor();
		for ($i = 1; $i <= O_Floor::TYPE_NUM; $i++) {
			$curTypeData = $objFloor->getData($i);
			//array(级别, 是否开放[1开启,0关闭])
			$data[] = array($i, $curTypeData[0]);
		}
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 初始信息
	 * @author huwei
	 * @param int $type
	 */
	public function AInfo($type = 1) {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$now = time();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$objFloor = $objPlayer->Floor();
		$curTypeData = $objFloor->getData($type);
		if (!empty($curTypeData)) {
			//是否开放, 当前所在楼层,是否领取奖励
			list($open, $curFloorNo, $curFloorAward) = $curTypeData;

			$err = '';
			if (empty($open)) {
				$err = T_ErrNo::FLOOR_NO_OPEN;
			}

			$errNo = $err;
			if (empty($err)) {
				$award = array();
				foreach ($objFloor->baseData[$type] as $no => $val) {
					if (!empty($val[2])) {
						$had = 1;
						if ($no > $curFloorNo) {
							$had = 0;
						} elseif ($no == $curFloorNo && !$curFloorAward) {
							$had = 0;
						}
						$award[] = array($no, $val[2], $had);
					}
				}

				$cd = $objPlayer->CD()->toFront(O_CD::TYPE_FLOOR);;
				$errNo = '';
				$num = $objFloor->getTimes();
				$data = array(
					'MaxFloor' => $objFloor->getMaxNum($type),
					'Times' => $objFloor->leftFreeTimes(),
					'NextCost' => $objFloor->calcCost($type, $num),
					'CurFloorNo' => $curFloorNo,
					'AwardData' => $award,
					'BattleId' => $objFloor->getBId(),
					'CD' => array($cd[0], $cd[1])
				);
			}
		}

		return B_Common::result($errNo, $data);
	}


	/**
	 * 攻击NPC
	 * @author huwei
	 * @param int $type
	 */
	public function AAtk($type, $heroIdList = '', $isAutoFight = M_War::FIGHT_TYPE_AUTO) {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$now = time();

		$heroConf = M_Config::getVal();

		$attHeroIdArr = !empty($heroIdList) ? explode(',', $heroIdList) : array();
		$heroNum = count($attHeroIdArr);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		if (in_array($isAutoFight, array(M_War::FIGHT_TYPE_HAND, M_War::FIGHT_TYPE_AUTO, M_War::FIGHT_TYPE_QUICK)) &&
			$heroNum > 0 &&
			$heroNum <= $heroConf['hero_num_troop']
		) {

			$cityId = $cityInfo['id'];
			$objFloor = $objPlayer->Floor();
			$curTypeData = $objFloor->getData($type);

			//是否开放, 当前所在楼层,是否领取奖励
			list($open, $curFloorNo, $curFloorAward) = $curTypeData;
			$curFloorNo += 1;
			//当前所在楼层,是否领取奖励
			$err = '';

			$bId = $objFloor->getBId();
			$objCD = $objPlayer->CD();
			$cdIdx = $objCD->getFreeIdx(O_CD::TYPE_FLOOR);
			if (!M_Hero::checkHeroStatus($cityId, $attHeroIdArr)) //检测英雄是否空闲 或 不存在 此英雄
			{
				$err = T_ErrNo::HERO_EXIST_FIGHT;
			} else if (!empty($bId)) //是否在爬楼战斗中
			{
				$err = T_ErrNo::FLOOR_BATTLE_EXIST;
			} else if (!isset($objFloor->baseData[$type][$curFloorNo])) {
				$err = T_ErrNo::FLOOR_DATA_ERR;
			} else if ((M_War::FIGHT_TYPE_QUICK == $isAutoFight) && !$cdIdx) {
				$err = T_ErrNo::FLOOR_QUICK_CD_FULL;
			}

			$errNo = $err;
			if (empty($err)) {
				list($npcId, $mapNo, $awardId) = $objFloor->baseData[$type][$curFloorNo];
				if (!empty($npcId) && !empty($mapNo)) {
					//触发战斗
					//构建战斗数据
					$atkNo = array($type, $curFloorNo);
					$npcData = array($npcId, $mapNo);
					$bData = M_War::buildFloorWarBattleData($cityId, $cityInfo['pos_no'], implode("_", $atkNo), $npcData, $isAutoFight, $attHeroIdArr);
					//插入战斗队列
					$battleId = M_War::insertWarBattle($bData, $isAutoFight);
					if ($battleId) {
						if ($isAutoFight != M_War::FIGHT_TYPE_QUICK) {
							$objFloor->setBId($battleId);
							M_Hero::changeHeroFlag($cityId, $attHeroIdArr, T_Hero::FLAG_WAR); //改变英雄状态为战斗中
						} else {
							$objCD->set(O_CD::TYPE_FLOOR, $cdIdx, T_Battle::QUICK_TIME);
							$cd = $objCD->toFront(O_CD::TYPE_FLOOR);
							$msRow = array('cd' => array($cd[0], $cd[1]));
							M_Sync::addQueue($cityId, M_Sync::KEY_FLOOR, $msRow); //同步
						}
						$objPlayer->save();

						$errNo = '';
						$data = array(
							'BattleId' => $battleId,
						);
					}
				}
			}
		}
		return B_Common::result($errNo, $data);
	}


	/**
	 * 领取奖励
	 * @author huwei
	 * @param int $type
	 */
	public function AAward($type = 1) {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$objFloor = $objPlayer->Floor();
		$curTypeData = $objFloor->getData($type);

		if (!empty($curTypeData)) {
			//是否开放, 当前所在楼层,是否领取奖励
			list($open, $curFloorNo, $curFloorAward) = $curTypeData;
			$err = '';
			if (!empty($curFloorAward)) {
				$err = T_ErrNo::FLOOR_AWARD_HAD;
			} elseif (empty($objFloor->baseData[$type][$curFloorNo][2])) {
				$err = T_ErrNo::FLOOR_AWARD_NO;
			}

			$errNo = $err;
			if (empty($err)) {
				$awardId = $objFloor->baseData[$type][$curFloorNo][2];

				$awardArr = M_Award::rateResult($awardId);
				$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_FLOOR);
				$awardTxt = M_Award::toText($awardArr);

				if ($bAward) {
					$curFloorAward = 1;
					$curFloorNo += 1;
					$objFloor->setData($type, array($open, $curFloorNo, $curFloorAward));
					$objPlayer->save();
					$errNo = '';
					$data = array(
						'CurFloorNo' => $curFloorNo,
						'Award' => $awardTxt,
					);
				}
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 重置
	 * @author huwei
	 * @param int $type
	 */
	public function AReset($type = 1) {
		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$objFloor = $objPlayer->Floor();
		$curTypeData = $objFloor->getData($type);

		if (!empty($curTypeData)) {
			$num = $objFloor->incrTimes();

			list($open, $curFloorNo, $curFloorAward) = $curTypeData;
			$curCost = $objFloor->calcCost($type, $num);
			$bId = $objFloor->getBId();
			$objPlayer->City()->mil_pay -= $curCost;
			$err = '';
			if ($objPlayer->City()->mil_pay < 0) {
				$err = T_ErrNo::NO_ENOUGH_MILIPAY;
			} else if (!empty($bId)) //是否在爬楼战斗中
			{
				$err = T_ErrNo::FLOOR_BATTLE_EXIST;
			}

			$errNo = $err;
			if (empty($err)) {
				//当前所在楼层
				$curFloorNo = 1;
				//是否领取奖励
				$curFloorAward = 0;
				$objFloor->setData($type, array($open, $curFloorNo, $curFloorAward));
				$nextCost = $objFloor->calcCost($type, $num + 1);

				$award = array();
				foreach ($objFloor->baseData[$type] as $no => $val) {
					if (!empty($val[2])) {
						$had = 0;
						$award[] = array($no, $val[2], $had);
					}
				}
				$errNo = '';
				$data = array(
					'Times' => $objFloor->leftFreeTimes(),
					'NextCost' => $nextCost,
					'CurFloorNo' => $curFloorNo,
					'AwardData' => $award,
				);
			}
		}

		return B_Common::result($errNo, $data);
	}

}

?>