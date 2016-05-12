<?php

/** 越野系统控制器 */
class C_Horse extends C_I {
	/**
	 * 获取当前初始化数据
	 * @author chenhui on 20121214
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetInit() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$cityId = intval($cityInfo['id']);
		$cycleStageArr = M_Horse::getCycleStageTime();
		$runNo = $cycleStageArr['curRunNo'];
		$cycleNo = $cycleStageArr['curCycleNo'];
		$stage = $cycleStageArr['curStageNo'];
		$stageEndtime = $cycleStageArr['curStageEndTime'][$stage];
		$nowDate = $cycleStageArr['nowDate'];
		$maxCycleNo = $cycleStageArr['maxCycleNo'];

		$sysHorse = M_Horse::getSysHorse($nowDate, $cycleNo); //公共越野数据
		$cityHorse = M_Horse::getCityHorse($cityId, $cycleNo); //当前城市越野数据
		$horseConf = M_Config::getVal('horse'); //越野系统配置

		//第一名的奖励相关(最后一轮在取今天的第一名数据,否则取前天的奖励数据)
		if ($cycleNo == $maxCycleNo &&
			M_Horse::STAGE_AWARD == $stage
		) {
			$canGetFirstCityId = $sysHorse['first_city_id'];
		} else { //非当天最后一轮的领奖时间 只能领取前天奖励
			$yesterdayDate = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
			$yesterdaySysHorse = M_Horse::getSysHorse($yesterdayDate, $maxCycleNo);
			$canGetFirstCityId = $yesterdaySysHorse['first_city_id'];
		}

		//获取当天获取奖励的城市的累计奖励数据
		$curDayCycleNo = (M_Horse::STAGE_AWARD == $stage) ? $cycleNo : $cycleNo - 1;
		$tmp2 = M_Horse::getFirstIdAward($nowDate, $curDayCycleNo);

		$firAward = $tmp2['firstCityAward'];
		$arrCityIdAward = $tmp2['cityAwardArr'];

		for ($i = 1; $i <= 7; $i++) {
			$horseKey = 'horse' . $i;
			$retHorseKey = 'Horse' . $i;
			$retHorseVal[$retHorseKey] = array(
				'Id' => $sysHorse[$horseKey][0],
				'Order' => $sysHorse[$horseKey][1],
				'PayRate' => $sysHorse[$horseKey][2],
				'Status' => $sysHorse[$horseKey][3],
				'BetAmount' => $cityHorse[$horseKey],
			);
		}

		$data = array(
			'Stage' => $stage,
			'CycleNo' => $cycleNo,
			'StageEndtime' => $stageEndtime,
			'BettingRange' => $horseConf[5],
			'MilpayTotal' => isset($arrCityIdAward[$cityId]) ? $arrCityIdAward[$cityId] : 0,
			'FirstIsSelf' => $canGetFirstCityId == $cityId ? M_Horse::ISSELF_YES : M_Horse::ISSELF_NO,
			'FirstAward' => $firAward,
			'HorseInfo' => $retHorseVal,
			'NumPlayer' => count($sysHorse['join_log']),
		);


		$errNo = '';


		return B_Common::result($errNo, $data);
	}

	/**
	 * 同步数据接口
	 * @author chenhui on 20121214
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ASynchorize() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$arrData = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$cycleStageArr = M_Horse::getCycleStageTime();

		$runNo = $cycleStageArr['curRunNo'];
		$cycleNo = $cycleStageArr['curCycleNo'];
		$stage = $cycleStageArr['curStageNo'];
		$stageEndtime = $cycleStageArr['curStageEndTime'][$stage];
		$nowDate = $cycleStageArr['nowDate'];
		$maxCycleNo = $cycleStageArr['maxCycleNo'];

		$cityId = intval($cityInfo['id']);
		$sysHorse = M_Horse::getSysHorse($nowDate, $cycleNo); //公共越野数据
		$horseConf = M_Config::getVal('horse'); //越野系统配置

		$cityHorse = M_Horse::getCityHorse($cityId, $cycleNo); //城市越野数据
		$runIdx = $runNo - 1; //比赛阶段索引(从0开始)

		$arrData = array(
			'Stage' => $stage,
			'StageEndtime' => $stageEndtime,
		);

		$runIdxMax = count($sysHorse['horse1'][5]); //比赛阶段总阶段数

		if (M_Horse::STAGE_AWARD == $stage) {
			for ($i = 1; $i <= 7; $i++) {
				$horseKey = 'horse' . $i;
				$retHorseKey = 'Horse' . $i;

				$arrRankT[$retHorseKey] = $sysHorse[$horseKey][7];
				$retHorseVal[$retHorseKey] = array($sysHorse[$horseKey][6][$runIdxMax - 1][0], $sysHorse[$horseKey][7]);
			}

			$arrRank = array_flip($arrRankT); //交换数组中的键和值
			$horseNo = $arrRank[1]; //第一名马的编号

			//第一名的奖励相关(最后一轮在取今天的第一名数据,否则取前天的奖励数据)
			if ($cycleNo == $maxCycleNo &&
				M_Horse::STAGE_AWARD == $stage
			) {
				$canGetFirstCityId = $sysHorse['first_city_id'];
			} else { //非当天最后一轮的领奖时间 只能领取前天奖励
				$yesterdayDate = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
				$yesterdaySysHorse = M_Horse::getSysHorse($yesterdayDate, $maxCycleNo);
				$canGetFirstCityId = $yesterdaySysHorse['first_city_id'];
			}

			//获取当天获取奖励的城市的累计奖励数据
			$curDayCycleNo = (M_Horse::STAGE_AWARD == $stage) ? $cycleNo : $cycleNo - 1;
			$tmp2 = M_Horse::getFirstIdAward($nowDate, $curDayCycleNo);
			$firAward = $tmp2['firstCityAward'];
			$arrCityIdAward = $tmp2['cityAwardArr'];

			$retHorseVal['Champion'] = $horseNo;
			$retHorseVal['FirstIsSelf'] = ($canGetFirstCityId == $cityId) ? M_Horse::ISSELF_YES : M_Horse::ISSELF_NO;
			$retHorseVal['FirstAward'] = $firAward;
			$retHorseVal['MilTotal'] = isset($arrCityIdAward[$cityId]) ? $arrCityIdAward[$cityId] : 0;

			$arrData['StageData'] = $retHorseVal;
			$arrData['NumPlayer'] = count($sysHorse['join_log']);
		} else if (M_Horse::STAGE_RUN == $stage) {
			$uniformSpeed = $horseConf[3][1][0];
			$retHorseVal = array(
				"SecBeginAt" => $sysHorse['run_per_time'][$runIdx][0], //该比赛阶段开始时间
				"SecEndAt" => $sysHorse['run_per_time'][$runIdx][1], //该比赛阶段结束时间
				'EncourTimes' => max($horseConf[6] - $cityHorse['encour_times'], 0), //本场比赛剩余打气次数
				'RunNo' => $runNo,
			);
			for ($i = 1; $i <= 7; $i++) {
				$horseKey = 'horse' . $i;
				$preP = 0;
				if ($runIdx != 0) { //初始前置位置为0
					$preP = $sysHorse[$horseKey][6][$runIdx - 1][0];
				}
				$eventTimeArr = array();

				$horseEvent = $sysHorse[$horseKey][5][$runIdx];
				if ($horseEvent[0] != $uniformSpeed) { //如果等于匀速事件
					$diff = $horseEvent[1] - $sysHorse['run_per_time'][$runIdx][0];
					$eventTimeArr = array($horseEvent[0] => $diff);
				}
				$endP = $sysHorse[$horseKey][6][$runIdx][1];

				$retHorseKey = 'Horse' . $i;
				$retHorseVal[$retHorseKey] = array($preP, $endP, $eventTimeArr);
			}

			$arrData['StageData'] = $retHorseVal;
			$arrData['NumPlayer'] = count($sysHorse['join_log']);

		} else {
			$arrData['StageData'] = array();
		}

		$errNo = '';

		return B_Common::result($errNo, $arrData);
	}

	/**
	 * 获取越野系统正在运行到的阶段的数据
	 * @author chenhui on 20121206
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	/**
	 * public function AGetSysHorse()
	 * {
	 * //操作结果默认为失败0
	 * $errNo        = T_ErrNo::ERR_ACTION;    //失败原因默认
	 * $arrData    = array();
	 *
	 * $objPlayer = $this->objPlayer;
	 * $cityInfo = $objPlayer->getCityBase();
	 * $cityId        = intval($cityInfo['id']);
	 * $cycleStageArr    = M_Horse::getCycleStageTime();
	 * $cycleNo        = $cycleStageArr['curCycleNo'];
	 * $nowDate        = $cycleStageArr['nowDate'];
	 * $sysHorse        = M_Horse::getSysHorse($nowDate, $cycleNo);
	 *
	 * $arrData = array(
	 * 'CycleNo'        => $sysHorse['cycle_no'],
	 * 'Stage'            => $sysHorse['stage'],
	 * 'StageEndtime'    => $sysHorse['stage_endtime'],
	 * 'StageRunNo'    => $sysHorse['stage_run_no'],
	 * 'FirstIsSelf'    => ($sysHorse['first_city_id'] == $cityId) ? M_Horse::ISSELF_YES : M_Horse::ISSELF_NO,
	 * 'FirstAward'    => $sysHorse['first_award'],
	 * 'Horse1'        => $sysHorse['horse1'],
	 * 'Horse2'        => $sysHorse['horse2'],
	 * 'Horse3'        => $sysHorse['horse3'],
	 * 'Horse4'        => $sysHorse['horse4'],
	 * 'Horse5'        => $sysHorse['horse5'],
	 * 'Horse6'        => $sysHorse['horse6'],
	 * 'Horse7'        => $sysHorse['horse7'],
	 * );
	 *
	 *
	 * $errNo = '';
	 *
	 * return B_Common::result($errNo, $arrData);
	 * }
	 **/

	/**
	 * 获取某城市越野数据
	 * @author chenhui on 20121206
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	/**
	 * public function AGetCityHorse()
	 * {
	 * //操作结果默认为失败0
	 * $errNo = T_ErrNo::ERR_ACTION;    //失败原因默认
	 * $arrData = array();
	 *
	 * $objPlayer = $this->objPlayer;
	 * $cityInfo = $objPlayer->getCityBase();
	 * $cityId = intval($cityInfo['id']);
	 * $cycleStageArr    = M_Horse::getCycleStageTime();
	 *
	 * $cycleNo        = $cycleStageArr['curCycleNo'];
	 * $nowDate        = $cycleStageArr['nowDate'];
	 * $cityHorse = M_Horse::getCityHorse($cityId, $cycleNo);
	 *
	 * $arrData = array(
	 * 'Horse1'        => $cityHorse['horse1'],
	 * 'Horse2'        => $cityHorse['horse2'],
	 * 'Horse3'        => $cityHorse['horse3'],
	 * 'Horse4'        => $cityHorse['horse4'],
	 * 'Horse5'        => $cityHorse['horse5'],
	 * 'Horse6'        => $cityHorse['horse6'],
	 * 'Horse7'        => $cityHorse['horse7'],
	 * 'EncourTimes'    => $cityHorse['encour_times'],
	 * 'MilpayTotal'    => $cityHorse['milpay_total'],
	 * );
	 *
	 *
	 * $errNo = '';
	 *
	 * return B_Common::result($errNo, $arrData);
	 * }
	 **/
	/**
	 * 玩家投注
	 * @author chenhui on 20121215
	 * @param string $horseNo 马编号(类似Horse1)
	 * @param int $sum 投注金额
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ABetting($horseNo, $sum) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$arrData = array();

		$horseNo = trim(strtolower($horseNo));
		$sum = intval($sum);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$cycleStageArr = M_Horse::getCycleStageTime();
		$runNo = $cycleStageArr['curRunNo'];
		$cycleNo = $cycleStageArr['curCycleNo'];
		$stage = $cycleStageArr['curStageNo'];
		$stageEndtime = $cycleStageArr['curStageEndTime'][$stage];
		$nowDate = $cycleStageArr['nowDate'];

		$cityId = intval($cityInfo['id']);
		$cityHorse = M_Horse::getCityHorse($cityId, $cycleNo); //城市越野数据
		$sysHorse = M_Horse::getSysHorse($nowDate, $cycleNo); //公共越野数据

		$horseConf = M_Config::getVal('horse'); //越野系统配置


		if (M_Horse::STAGE_BETTING == $stage) {
			if (isset($cityHorse[$horseNo])) {
				if ($sum > 0 && $sum <= T_App::SYS_VAL_LIMIT_TOP) {
					$betNum = min(max($sum, $horseConf[5][0]), $horseConf[5][1]); //实际投注金额
					if ($betNum <= $cityInfo['mil_pay']) {
						$newBet = $cityHorse[$horseNo] + $betNum;
						$newAll = $cityHorse['horse_all'] + $betNum;
						$bDecr = $objPlayer->City()->decrCurrency(T_App::MILPAY, $betNum, B_Log_Trade::E_BettingHorse, $horseNo);
						$bAdd = $bDecr && M_Horse::updateCityHorse($cityId, array($horseNo => $newBet, 'horse_all' => $newAll), true);
						if ($bAdd) {
							$arrData = array($newBet);

							$flag = T_App::SUCC;
							$errNo = '';
						} else {
							$errNo = T_ErrNo::ERR_UPDATE;
						}
					} else {
						$errNo = T_ErrNo::NO_ENOUGH_MILIPAY;
					}
				} else {
					$errNo = T_ErrNo::HORSE_BETTING_ERR;
				}
			} else {
				$errNo = T_ErrNo::HORSE_CODE_ERR;
			}
		} else {
			$errNo = T_ErrNo::HORSE_NOT_BETTING;
		}

		return B_Common::result($errNo, $arrData);
	}

	/**
	 * 玩家领取投注胜利奖励
	 * @author chenhui on 20121215
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ARecePayBack() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$arrData = array();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$cycleStageArr = M_Horse::getCycleStageTime();
		$runNo = $cycleStageArr['curRunNo'];
		$cycleNo = $cycleStageArr['curCycleNo'];
		$stage = $cycleStageArr['curStageNo'];
		$stageEndtime = $cycleStageArr['curStageEndTime'][$stage];
		$nowDate = $cycleStageArr['nowDate'];

		$cityId = intval($cityInfo['id']);
		$cityHorse = M_Horse::getCityHorse($cityId, $cycleNo); //城市越野数据
		$sysHorse = M_Horse::getSysHorse($nowDate, $cycleNo);
		$horseConf = M_Config::getVal('horse'); //越野系统配置

		//获取当天获取奖励的城市的奖励数据
		$curDayCycleNo = (M_Horse::STAGE_AWARD == $stage) ? $cycleNo : $cycleNo - 1;
		$tmp2 = M_Horse::getFirstIdAward($nowDate, $curDayCycleNo);
		$arrCityIdAward = $tmp2['cityAwardArr'];
		//Logger::debug(array(__METHOD__, $cityId, $arrCityIdAward));
		if (empty($arrCityIdAward[$cityId])) {
			return B_Common::result(T_ErrNo::HORSE_NO_AWARD);
		}
		for ($i = 1; $i <= $curDayCycleNo; $i++) {
			$sysH = M_Horse::getSysHorse($nowDate, $i);
			$tmp = $sysH['award_data'];
			//Logger::debug(array(__METHOD__, $cityId, $tmp));
			if (isset($tmp[$cityId])) {
				unset($tmp[$cityId]);
				$ret = M_Horse::updateSysHorse(array('award_data' => json_encode($tmp)), $nowDate, $i, true);
				//Logger::debug(array(__METHOD__, $ret));
			}
		}

		$objPlayer->City()->mil_pay += $arrCityIdAward[$cityId];

		$objPlayer->Log()->income(T_App::MILPAY, $arrCityIdAward[$cityId], B_Log_Trade::I_HorseBack);

		$objPlayer->save();
		$data = array(0);

		return B_Common::result('', $data);
	}

	/**
	 * 玩家领取每日第一名奖励
	 * @author chenhui on 20121217
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AReceFirstPayBack() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$arrData = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$cityId = intval($cityInfo['id']);
		$cycleStageArr = M_Horse::getCycleStageTime();
		$nowDate = $cycleStageArr['nowDate'];
		$cycleNo = $cycleStageArr['curCycleNo'];
		$maxCycleNo = $cycleStageArr['maxCycleNo'];
		$curStageStartTime = $cycleStageArr['curStageStartTime'];
		//最后一轮奖励开始时间
		$sysHorse = M_Horse::getSysHorse($nowDate, $cycleNo);
		$stage = $cycleStageArr['curStageNo'];

		//第一名的奖励相关
		if ($cycleNo == $maxCycleNo &&	M_Horse::STAGE_AWARD == $stage	) {
			$canGetFirstCityId = $sysHorse['first_city_id'];
			$needDate = $nowDate;
		} else {
			//非当天最后一轮的领奖时间 只能领取前天奖励
			$yesterdayDate = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
			$yesterdaySysHorse = M_Horse::getSysHorse($yesterdayDate, $maxCycleNo);
			$canGetFirstCityId = $yesterdaySysHorse['first_city_id'];
			$needDate = $yesterdayDate;
		}

		if ($canGetFirstCityId == $cityId) { //最后一场领取时间
			$horseConf = M_Config::getVal('horse'); //越野系统配置

			$awardArr = M_Award::rateResult($horseConf[8]);
			$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_HorseFirst);

			$bAward && $arrData = M_Award::toText($awardArr);

			$ret = M_Horse::updateSysHorse(array('first_city_id' => 0), $needDate, $maxCycleNo, true);


			$errNo = '';
		} else {
			$errNo = T_ErrNo::HORSE_NO_AWARD;
		}
		return B_Common::result($errNo, $arrData);
	}

	/**
	 * 获取系统所有打气数据
	 * @author chenhui on 20121215
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetEncourage() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$arrData = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$horseConf = M_Config::getVal('horse'); //越野系统配置
		foreach ($horseConf[7] as $id => $cost) {
			$arrData[] = array($id, $cost);
		}
		$errNo = '';

		return B_Common::result($errNo, $arrData);
	}

	/**
	 * 玩家打气
	 * @author chenhui on 20121215
	 * @param int $id 打气ID
	 * @param sting $horse 马编号，实际无用
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AEncourage($id, $horse = '') {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$arrData = array();

		$id = intval($id);
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$cityId = intval($cityInfo['id']);
		$cycleStageArr = M_Horse::getCycleStageTime();
		$runNo = $cycleStageArr['curRunNo'];
		$cycleNo = $cycleStageArr['curCycleNo'];
		$stage = $cycleStageArr['curStageNo'];
		$nowDate = $cycleStageArr['nowDate'];
		$sysHorse = M_Horse::getSysHorse($nowDate, $cycleNo);

		if (M_Horse::STAGE_RUN == $stage) {
			$horseConf = M_Config::getVal('horse'); //越野系统配置
			$cityHorse = M_Horse::getCityHorse($cityId, $cycleNo); //城市越野数据

			$times = $cityHorse['encour_times'];
			if ($times < $horseConf[6]) {
				if (isset($horseConf[7][$id])) {
					$encourCost = $horseConf[7][$id];
					if ($encourCost <= $cityInfo['mil_pay']) {
						$bDecr = $objPlayer->City()->decrCurrency(T_App::MILPAY, $encourCost, B_Log_Trade::E_EncourHorse, $id);
						$bAdd = $bDecr && M_Horse::updateCityHorse($cityId, array('encour_times' => $times + 1), true);
						if ($bAdd) {
							$arrData = array($horseConf[6] - $times - 1);

							$flag = T_App::SUCC;
							$errNo = '';
						} else {
							$errNo = T_ErrNo::ERR_UPDATE;
						}
					} else {
						$errNo = T_ErrNo::NO_ENOUGH_MILIPAY;
					}
				} else {
					$errNo = T_ErrNo::HORSE_ENCOUR_ID_ERR;
				}
			} else {
				$errNo = T_ErrNo::HORSE_ENCOUR_OVER;
			}
		} else {
			$errNo = T_ErrNo::HORSE_NOT_RUN;
		}

		return B_Common::result($errNo, $arrData);
	}

}

?>