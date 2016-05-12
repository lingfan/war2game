<?php

/** 越野模型层 */
class M_Horse {
	/** 越野周期所处阶段 投注 */
	const STAGE_BETTING = 1;
	/** 越野周期所处阶段 等待比赛 */
	const STAGE_WAIT = 2;
	/** 越野周期所处阶段 比赛 */
	const STAGE_RUN = 3;
	/** 越野周期所处阶段 领奖 */
	const STAGE_AWARD = 4;

	/** 任一阶段 系统已计算 */
	const CALC_YES = 1;
	/** 任一阶段 系统未计算 */
	const CALC_NO = 0;

	/** 比赛触发 减速事件 */
	const EVENT_DECR = 1;
	/** 比赛触发 匀速事件 */
	const EVENT_UNIFORM = 2;
	/** 比赛触发 加速事件 */
	const EVENT_INCR = 3;

	/** 每日第一名 是自己*/
	const ISSELF_YES = 1;
	/** 每日第一名 不是自己 */
	const ISSELF_NO = 0;


	/**
	 * 每个比赛周期 阶段时间
	 * @author huwei
	 * @return array (nowTime当前时间, curCycleNo当前周期编号, curCycleEndTime当前周期结束时间, curStage当前阶段, curStageStartTime每个阶段开始时间)
	 */
	static public function getCycleStageTime($date = '') {
		$sTime   = microtime(true);
		$nowTime = time(); //当前时刻时间戳
		if (!empty($date)) { //Ymd = 20120212
			$dayInitTime = strtotime($date); //今天凌晨零点时间戳
			$nowDate     = $date;
		} else {
			$dayInitTime = mktime(0, 0, 0); //今天凌晨零点时间戳
			$nowDate     = date('Ymd');
		}

		$horseConf = M_Config::getVal('horse'); //越野系统配置

		$circleStageInterval = array(
			self::STAGE_BETTING => $horseConf[0][0],
			self::STAGE_WAIT    => $horseConf[0][1],
			self::STAGE_RUN     => $horseConf[0][2],
			self::STAGE_AWARD   => $horseConf[0][3]
		);


		//每个周期所需秒数
		$cycleTotalTime = array_sum($circleStageInterval) * T_App::ONE_MINUTE;
		//当前是第几场比赛
		$curCycleNo = ceil(($nowTime - $dayInitTime) / $cycleTotalTime);
		$maxCycleNo = ceil(T_App::ONE_DAY / $cycleTotalTime);

		//上个周期结束时间
		$preCycleEndTime = $dayInitTime + $cycleTotalTime * ($curCycleNo - 1);

		//投注开始时间
		$stageBettingStartTime = $preCycleEndTime;
		//等待开始时间(计算结果)
		$stageWaitStartTime = $stageBettingStartTime + $circleStageInterval[self::STAGE_BETTING] * T_App::ONE_MINUTE;
		//比赛开始时间(观看)
		$stageRunStartTime = $stageWaitStartTime + $circleStageInterval[self::STAGE_WAIT] * T_App::ONE_MINUTE;
		//领奖开始时间
		$stageAwardStartTime = $stageRunStartTime + $circleStageInterval[self::STAGE_RUN] * T_App::ONE_MINUTE;
		//周期结束时间
		$curCycleEndTime = $stageAwardStartTime + $circleStageInterval[self::STAGE_AWARD] * T_App::ONE_MINUTE;

		//投注结束时间
		$stageBettingEndTime = $stageWaitStartTime;
		//等待结束时间(计算结果)
		$stageWaitEndTime = $stageRunStartTime;
		//比赛结束时间(观看)
		$stageRunEndTime = $stageAwardStartTime;
		//领奖结束时间
		$stageAwardEndTime = $curCycleEndTime;


		$curStageStartTime = array(
			self::STAGE_BETTING => $stageBettingStartTime,
			self::STAGE_WAIT    => $stageWaitStartTime,
			self::STAGE_RUN     => $stageRunStartTime,
			self::STAGE_AWARD   => $stageAwardStartTime,
		);

		$curStageEndTime = array(
			self::STAGE_BETTING => $stageBettingEndTime,
			self::STAGE_WAIT    => $stageWaitEndTime,
			self::STAGE_RUN     => $stageRunEndTime,
			self::STAGE_AWARD   => $stageAwardEndTime,
		);

		//周期中的阶段
		$curStageNo = self::STAGE_BETTING;
		foreach ($curStageStartTime as $tmpNo => $stageStartTime) {
			if ($nowTime > $stageStartTime) {
				$curStageNo = $tmpNo;
			}
		}

		//echo $curStageNo."<hr>";
		//echo date('Y-m-d H:i:s', $nowTime)."<hr>";
		//echo date('Y-m-d H:i:s', $stageBettingStartTime)."  ->  ".date('Y-m-d H:i:s', $stageBettingEndTime)."(".$stageBettingEndTime.")<hr>";
		//echo date('Y-m-d H:i:s', $stageWaitStartTime)."  ->  ".date('Y-m-d H:i:s', $stageWaitEndTime)."(".$stageWaitEndTime.")<hr>";
		//echo date('Y-m-d H:i:s', $stageRunStartTime)."  ->  ".date('Y-m-d H:i:s', $stageRunEndTime)."(".$stageRunEndTime.")<hr>";
		//echo date('Y-m-d H:i:s', $stageAwardStartTime)."  ->  ".date('Y-m-d H:i:s', $stageAwardEndTime)."(".$stageAwardEndTime.")<hr>";

		//比赛进行中的阶段
		$curRunNo        = $runIntervalTime = 0;
		$runIntervalTime = $circleStageInterval[self::STAGE_RUN] * T_App::ONE_MINUTE / 15;

		if ($curStageNo == self::STAGE_RUN) {
			$curRunNo = ceil(($nowTime - $stageRunStartTime) / $runIntervalTime);
		}


		$ret = array(
			'nowDate'           => $nowDate,
			'nowTime'           => $nowTime,
			'curCycleNo'        => $curCycleNo,
			'maxCycleNo'        => $maxCycleNo,
			'curCycleEndTime'   => $curCycleEndTime,
			'curStageNo'        => $curStageNo,
			'curStageStartTime' => $curStageStartTime,
			'curStageEndTime'   => $curStageEndTime,
			'curRunNo'          => $curRunNo,
			'runIntervalTime'   => $runIntervalTime,
		);

		$eTime = microtime(true);

		return $ret;
	}

	/**
	 * 获取玩家越野数据
	 * @author chenhui on 20121206
	 * @param $cityId
	 * @return array 1D
	 */
	static public function getCityHorse($cityId, $cycleNo) {
		$cityId = intval($cityId);
		$ret    = false;
		if ($cityId > 0) {
			$upInfo = $ret = array();
			$rc     = new B_Cache_RC(T_Key::CITY_HORSE, $cityId);
			$ret    = $rc->hmget(T_DBField::$cityHorseFields);
			$ret    = false;
			if (empty($ret['city_id'])) {
				$ret = B_DB::instance('CityHorse')->getRow($cityId);
				if (!empty($ret['city_id'])) {
					$rc->hmset($ret, T_App::ONE_DAY);
				}
			}

			$nowDate  = date('Ymd');
			$sysHorse = M_Horse::getSysHorse($nowDate, $cycleNo);
			if ($nowDate != $ret['horse_date'] || $ret['cycle_no'] != $sysHorse['cycle_no']) {
				$upInfo['horse_date']   = $nowDate;
				$upInfo['cycle_no']     = $sysHorse['cycle_no'];
				$upInfo['encour_times'] = 0;
				$upInfo['horse1']       = 0;
				$upInfo['horse2']       = 0;
				$upInfo['horse3']       = 0;
				$upInfo['horse4']       = 0;
				$upInfo['horse5']       = 0;
				$upInfo['horse6']       = 0;
				$upInfo['horse7']       = 0;
				$upInfo['horse_all']    = 0;

				$ret['horse_date']   = $upInfo['horse_date'];
				$ret['cycle_no']     = $upInfo['cycle_no'];
				$ret['encour_times'] = $upInfo['encour_times'];
				$ret['horse1']       = $upInfo['horse1'];
				$ret['horse2']       = $upInfo['horse2'];
				$ret['horse3']       = $upInfo['horse3'];
				$ret['horse4']       = $upInfo['horse4'];
				$ret['horse5']       = $upInfo['horse5'];
				$ret['horse6']       = $upInfo['horse6'];
				$ret['horse7']       = $upInfo['horse7'];
				$ret['horse_all']    = $upInfo['horse_all'];
			}
			!empty($upInfo) && M_Horse::updateCityHorse($cityId, $upInfo, true); //更新
		}
		return $ret;
	}

	/**
	 * 根据城市ID更新城市越野数据
	 * @author chenhui on 20121206
	 * @param int $cityId 城市ID
	 * @param array $upInfo 要更新的键值对数组
	 * @param bool $upDB 是否更新到DB
	 * @return array/false
	 */
	static public function updateCityHorse($cityId, $upInfo, $upDB = true) {
		$ret = false;
		if (!empty($cityId) && is_array($upInfo) && !empty($upInfo)) {
			$info = array();
			foreach ($upInfo as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$cityHorseFields)) {
					$info[$key] = $val;
				}
			}
			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_HORSE, $cityId);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$upDB && B_DB::instance('CityHorse')->update($info, $cityId);
				} else {
					$msg = array(__METHOD__, 'Update CityHorse Info Fail', func_get_args());
					Logger::error($msg);
				}
			}
		}

		return $ret ? $info : false;
	}

	/******以上为城市越野方法，以下为公共越野方法************************/

	/**
	 * 获取公共越野数据
	 * @author chenhui on 20121206
	 * @param $cityId
	 * @return array 1D
	 */
	static public function getSysHorse($nowDate, $cycleNo) {
		$horseConf = M_Config::getVal('horse'); //越野系统配置

		$rc  = new B_Cache_RC(T_Key::SYS_HORSE, $nowDate . $cycleNo);
		$ret = $rc->hmget(T_DBField::$sysHorseFields);
		if (empty($ret['horse_date']) && empty($ret['cycle_no'])) {
			$ret = B_DB::instance('Horse')->getRow($nowDate, $cycleNo);
			if (!empty($ret)) {
				$rc->hmset($ret, T_App::ONE_DAY);
			}
		}

		if ($ret) {
			$memHorse1 = json_decode($ret['horse1'], true);
			if (empty($memHorse1)) {
				$upInfo = $ret;

				$upInfo['stage']         = M_Horse::STAGE_BETTING; //1 投注
				$upInfo['stage_endtime'] = 0;

				$arrHorseData = M_Horse::getRandHorseData($horseConf[2], $horseConf[11]); //7个array(武器ID,排序号,状态ID)
				$arrBackRate  = M_Horse::getBackReceRate($horseConf[1]); //7个随机赔率
				//武器ID,序号,赔率,状态,投注总额,array(array(事件1ID,发生时间戳,权重)...),array(array(前期/总权重,当前/总权重)...),排名序号
				$upInfo['horse1']       = json_encode(array($arrHorseData[0][0], $arrHorseData[0][1], $arrBackRate[0], $arrHorseData[0][2], 0, array(), array(), 0));
				$upInfo['horse2']       = json_encode(array($arrHorseData[1][0], $arrHorseData[1][1], $arrBackRate[1], $arrHorseData[1][2], 0, array(), array(), 0));
				$upInfo['horse3']       = json_encode(array($arrHorseData[2][0], $arrHorseData[2][1], $arrBackRate[2], $arrHorseData[2][2], 0, array(), array(), 0));
				$upInfo['horse4']       = json_encode(array($arrHorseData[3][0], $arrHorseData[3][1], $arrBackRate[3], $arrHorseData[3][2], 0, array(), array(), 0));
				$upInfo['horse5']       = json_encode(array($arrHorseData[4][0], $arrHorseData[4][1], $arrBackRate[4], $arrHorseData[4][2], 0, array(), array(), 0));
				$upInfo['horse6']       = json_encode(array($arrHorseData[5][0], $arrHorseData[5][1], $arrBackRate[5], $arrHorseData[5][2], 0, array(), array(), 0));
				$upInfo['horse7']       = json_encode(array($arrHorseData[6][0], $arrHorseData[6][1], $arrBackRate[6], $arrHorseData[6][2], 0, array(), array(), 0));
				$upInfo['stage_iscalc'] = json_encode(array(M_Horse::STAGE_WAIT, 1));

				M_Horse::updateSysHorse($upInfo, $nowDate, $cycleNo, true);

				$ret['stage']         = $upInfo['stage'];
				$ret['stage_endtime'] = $upInfo['stage_endtime'];
				$ret['stage_iscalc']  = $upInfo['stage_iscalc'];
				$ret['horse1']        = $upInfo['horse1'];
				$ret['horse2']        = $upInfo['horse2'];
				$ret['horse3']        = $upInfo['horse3'];
				$ret['horse4']        = $upInfo['horse4'];
				$ret['horse5']        = $upInfo['horse5'];
				$ret['horse6']        = $upInfo['horse6'];
				$ret['horse7']        = $upInfo['horse7'];
			}

			$ret['stage_iscalc'] = json_decode($ret['stage_iscalc'], true);
			$ret['run_per_time'] = json_decode($ret['run_per_time'], true);
			$ret['horse1']       = json_decode($ret['horse1'], true);
			$ret['horse2']       = json_decode($ret['horse2'], true);
			$ret['horse3']       = json_decode($ret['horse3'], true);
			$ret['horse4']       = json_decode($ret['horse4'], true);
			$ret['horse5']       = json_decode($ret['horse5'], true);
			$ret['horse6']       = json_decode($ret['horse6'], true);
			$ret['horse7']       = json_decode($ret['horse7'], true);
			$ret['join_log']     = json_decode($ret['join_log'], true);
			$ret['award_log']    = json_decode($ret['award_log'], true);
			$ret['award_data']   = json_decode($ret['award_data'], true);
		}


		return $ret;
	}

	/**
	 * 更新城市公共越野数据
	 * @author chenhui on 20121206
	 * @param array $upInfo 要更新的键值对数组
	 * @param bool $upDB 是否更新到DB
	 * @return array/false
	 */
	static public function updateSysHorse($upInfo, $nowDate, $cycleNo, $upDB = true) {
		$ret = false;
		if (is_array($upInfo) && !empty($upInfo)) {
			$info = array();
			foreach ($upInfo as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$sysHorseFields)) {
					$info[$key] = $val;
				}
			}
			if (!empty($info)) {
				//Logger::debug(array(__METHOD__, $nowDate.$cycleNo));
				$rc  = new B_Cache_RC(T_Key::SYS_HORSE, $nowDate . $cycleNo);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$upDB && B_DB::instance('Horse')->set($info, $nowDate, $cycleNo);
				} else {
					$msg = array(__METHOD__, 'Update SysHorse Info Fail', func_get_args());
					Logger::error($msg);
				}
			}
		}

		return $ret ? $info : false;
	}

	/** *******越野系统定时处理************ */
	static public function updateHorseTimer() {
		$horseConf = M_Config::getVal('horse'); //越野系统配置

		//if (!in_array(ETC_NO, array('en', 'vn', 'tw')))
		if (empty($horseConf[10])) {
			return false;
		}

		$cycleStageArr = M_Horse::getCycleStageTime();

		$runNo             = $cycleStageArr['curRunNo'];
		$cycleNo           = $cycleStageArr['curCycleNo'];
		$stage             = $cycleStageArr['curStageNo'];
		$stageEndTime      = $cycleStageArr['curStageEndTime'][$stage];
		$stageStartTimeArr = $cycleStageArr['curStageStartTime'];
		$nowDate           = $cycleStageArr['nowDate'];

		$sysHorse     = M_Horse::getSysHorse($nowDate, $cycleNo); //系统当前公共越野数据
		$nowtime      = time(); //当前时刻时间戳
		$dayStartTime = mktime(0, 0, 0, date('n'), date('j'), date('Y')); //今天凌晨零点时间戳

		//Logger::debug(array($sysHorse['stage_iscalc'], $cycleNo));

		//下一个回合编号
		$tmpNextCycleNo = max($sysHorse['stage_iscalc'][1], $cycleNo);
		if ($tmpNextCycleNo != $cycleNo) {
			$sysHorse['stage_iscalc'][0] = M_Horse::STAGE_BETTING;
		}

		if ($cycleNo == $tmpNextCycleNo) {
			if ($nowtime > $stageStartTimeArr[M_Horse::STAGE_BETTING] &&
				$sysHorse['stage_iscalc'][0] == M_Horse::STAGE_BETTING
			) //投注
			{

			} else if ($nowtime > $stageStartTimeArr[M_Horse::STAGE_WAIT] &&
				$sysHorse['stage_iscalc'][0] == M_Horse::STAGE_WAIT
			) {
				//Logger::debug("Start==nowWaitStartTime#{$cycleNo}");
				M_Horse::calcWait($horseConf, $sysHorse, $cycleStageArr); //等待比赛
				//Logger::debug("End==Wait==#{$cycleNo}");
			} else if ($nowtime > $stageStartTimeArr[M_Horse::STAGE_RUN] &&
				$sysHorse['stage_iscalc'][0] == M_Horse::STAGE_RUN
			) {
				//Logger::debug("Start==nowRunStartTime#{$cycleNo}");
				M_Horse::calcRun($horseConf, $sysHorse, $cycleStageArr); //比赛进行时
				//Logger::debug("End==Run==#{$cycleNo}");
			} else if ($nowtime > $stageStartTimeArr[M_Horse::STAGE_AWARD] &&
				$sysHorse['stage_iscalc'][0] == M_Horse::STAGE_AWARD
			) {
				//Logger::debug("ReceAward==nowAwardStartTime#{$cycleNo}");
				M_Horse::calcAward($horseConf, $sysHorse, $cycleStageArr); //领奖
				//Logger::debug("End==Award==#{$cycleNo}");
			}
		}
	}

	/** 计算等待比赛阶段 */
	static public function calcWait($horseConf, $sysHorse, $cycleStageArr) {
		$runNo             = $cycleStageArr['curRunNo'];
		$cycleNo           = $cycleStageArr['curCycleNo'];
		$stage             = $cycleStageArr['curStageNo'];
		$runIntervalTime   = $cycleStageArr['runIntervalTime'];
		$nowDate           = $cycleStageArr['nowDate'];
		$maxCycleNo        = $cycleStageArr['maxCycleNo'];
		$curStageStartTime = $cycleStageArr['curStageStartTime'];

		//Logger::debug(array(__METHOD__, 'STAGE_WAIT--I was calcWait==',$cycleNo, $maxCycleNo));
		$nowtime            = time();
		$upInfo             = $sysHorse;
		$upInfo['cycle_no'] = $cycleNo;
		$upInfo['stage']    = M_Horse::STAGE_WAIT; //2 等待比赛
		$arrBetting         = M_Horse::getBettingAll($cycleNo); //7个投注总额

		//$arrBetting 		= array(0,0,0,rand(0,10000),0,0,0);
		$upInfo['horse1'][4] = $arrBetting[0]; // 投注总额
		$upInfo['horse2'][4] = $arrBetting[1]; // 投注总额
		$upInfo['horse3'][4] = $arrBetting[2]; // 投注总额
		$upInfo['horse4'][4] = $arrBetting[3]; // 投注总额
		$upInfo['horse5'][4] = $arrBetting[4]; // 投注总额
		$upInfo['horse6'][4] = $arrBetting[5]; // 投注总额
		$upInfo['horse7'][4] = $arrBetting[6]; // 投注总额


		$arrRankNo = M_Horse::getHorseRanking($sysHorse, $arrBetting); //各号码对应的名次(从0起计)

		//Logger::debug(array(__METHOD__, 'STAGE_WAIT--arrRankNo==', $arrRankNo));
		$upInfo['horse1'][7] = $arrRankNo[0] + 1; // 号码排序号
		$upInfo['horse2'][7] = $arrRankNo[1] + 1; // 号码排序号
		$upInfo['horse3'][7] = $arrRankNo[2] + 1; // 号码排序号
		$upInfo['horse4'][7] = $arrRankNo[3] + 1; // 号码排序号
		$upInfo['horse5'][7] = $arrRankNo[4] + 1; // 号码排序号
		$upInfo['horse6'][7] = $arrRankNo[5] + 1; // 号码排序号
		$upInfo['horse7'][7] = $arrRankNo[6] + 1; // 号码排序号

		$arrEvent = M_Horse::getEventTime($horseConf, $sysHorse, $arrRankNo, $curStageStartTime[M_Horse::STAGE_RUN], $runIntervalTime);
		//Logger::debug(array(__METHOD__, 'STAGE_WAIT--arrEvent==', $arrEvent));
		$upInfo['run_per_time'] = $arrEvent[0]; //array(此段开始时间戳,结束时间戳)...
		$upInfo['horse1'][5]    = $arrEvent[1][0]; // array(array(事件1ID,发生时间戳,权重)...array(事件15,发生时间戳,权重))
		$upInfo['horse2'][5]    = $arrEvent[1][1]; //
		$upInfo['horse3'][5]    = $arrEvent[1][2]; //
		$upInfo['horse4'][5]    = $arrEvent[1][3]; //
		$upInfo['horse5'][5]    = $arrEvent[1][4]; //
		$upInfo['horse6'][5]    = $arrEvent[1][5]; //
		$upInfo['horse7'][5]    = $arrEvent[1][6]; //

		$arrRankT = array(
			'Horse1' => $upInfo['horse1'][7],
			'Horse2' => $upInfo['horse2'][7],
			'Horse3' => $upInfo['horse3'][7],
			'Horse4' => $upInfo['horse4'][7],
			'Horse5' => $upInfo['horse5'][7],
			'Horse6' => $upInfo['horse6'][7],
			'Horse7' => $upInfo['horse7'][7],
		);


		$arrRank    = array_flip($arrRankT); //交换数组中的键和值
		$horseNo    = $arrRank[1]; //第一名马的编号
		$newHorseNo = strtolower($horseNo);
		$runIdxMax  = count($upInfo[$newHorseNo][5]);

		//Logger::debug(array(__METHOD__, 'first horse==', $newHorseNo));
		$powerMax = $powerPre1 = $powerPre2 = $powerPre3 = $powerPre4 = $powerPre5 = $powerPre6 = $powerPre7 = 0;
		for ($m = 0; $m < $runIdxMax; $m++) {
			$powerMax += $upInfo[$newHorseNo][5][$m][2]; //最大权重值
		}
		$arr1 = $arr2 = $arr3 = $arr4 = $arr5 = $arr6 = $arr7 = array();
		for ($i = 0; $i < $runIdxMax; $i++) {
			$powerPre1 += $upInfo['horse1'][5][$i][2]; //权重值
			$powerPre2 += $upInfo['horse2'][5][$i][2];
			$powerPre3 += $upInfo['horse3'][5][$i][2];
			$powerPre4 += $upInfo['horse4'][5][$i][2];
			$powerPre5 += $upInfo['horse5'][5][$i][2];
			$powerPre6 += $upInfo['horse6'][5][$i][2];
			$powerPre7 += $upInfo['horse7'][5][$i][2];

			$arr1[$i] = array(round(100 * $powerPre1 / $powerMax, 2), round(100 * $upInfo['horse1'][5][$i][2] / $powerMax, 2)); // 前期权重/总权重,当前权重/总权重
			$arr2[$i] = array(round(100 * $powerPre2 / $powerMax, 2), round(100 * $upInfo['horse2'][5][$i][2] / $powerMax, 2));
			$arr3[$i] = array(round(100 * $powerPre3 / $powerMax, 2), round(100 * $upInfo['horse3'][5][$i][2] / $powerMax, 2));
			$arr4[$i] = array(round(100 * $powerPre4 / $powerMax, 2), round(100 * $upInfo['horse4'][5][$i][2] / $powerMax, 2));
			$arr5[$i] = array(round(100 * $powerPre5 / $powerMax, 2), round(100 * $upInfo['horse5'][5][$i][2] / $powerMax, 2));
			$arr6[$i] = array(round(100 * $powerPre6 / $powerMax, 2), round(100 * $upInfo['horse6'][5][$i][2] / $powerMax, 2));
			$arr7[$i] = array(round(100 * $powerPre7 / $powerMax, 2), round(100 * $upInfo['horse7'][5][$i][2] / $powerMax, 2));
		}

		$upInfo['horse1'][6] = $arr1; // 权重比值
		$upInfo['horse2'][6] = $arr2; //
		$upInfo['horse3'][6] = $arr3; //
		$upInfo['horse4'][6] = $arr4; //
		$upInfo['horse5'][6] = $arr5; //
		$upInfo['horse6'][6] = $arr6; //
		$upInfo['horse7'][6] = $arr7; //

		$arrCityInfo = B_DB::instance('CityHorse')->getAllCityAward($newHorseNo, $nowDate, $cycleNo);
		if (!empty($arrCityInfo) && is_array($arrCityInfo)) {
			foreach ($arrCityInfo as $cityHorse) {
				$addCityId                        = $cityHorse['city_id'];
				$newAdd                           = $cityHorse[$newHorseNo] * $upInfo[$newHorseNo][2]; //投注额 * 赔率
				$upInfo['award_log'][$addCityId]  = ceil($newAdd);
				$upInfo['award_data'][$addCityId] = ceil($newAdd);
			}
		}

		$joinLog    = array();
		$joinPlayer = B_DB::instance('CityHorse')->getRows($nowDate, $cycleNo);
		if (!empty($joinPlayer) && is_array($joinPlayer)) {
			foreach ($joinPlayer as $tmp) {
				$joinLog[$tmp['city_id']] = $tmp['horse_all'];
			}
		}
		$upInfo['join_log'] = $joinLog;

		$upInfo['stage_iscalc'][0] = M_Horse::STAGE_RUN;

		$upInfo = M_Horse::makeJsonArr($upInfo);
		M_Horse::updateSysHorse($upInfo, $nowDate, $cycleNo, true);
	}

	/** 计算比赛阶段 */
	static public function calcRun($horseConf, $sysHorse, $cycleStageArr) {
		$runNo   = $cycleStageArr['curRunNo'];
		$cycleNo = $cycleStageArr['curCycleNo'];
		$nowDate = $cycleStageArr['nowDate'];

		//Logger::debug(array(__METHOD__, 'STAGE_RUN--I was calcRun==', $cycleNo));
		$upInfo                    = $sysHorse;
		$upInfo['stage']           = M_Horse::STAGE_RUN; //3比赛
		$upInfo['stage_run_no']    = $runNo;
		$upInfo['stage_iscalc'][0] = M_Horse::STAGE_AWARD;

		$upInfo = M_Horse::makeJsonArr($upInfo);
		M_Horse::updateSysHorse($upInfo, $nowDate, $cycleNo, true);
	}

	/** 计算领奖阶段 */
	static public function calcAward($horseConf, $sysHorse, $cycleStageArr) {
		$runNo      = $cycleStageArr['curRunNo'];
		$cycleNo    = $cycleStageArr['curCycleNo'];
		$stage      = $cycleStageArr['curStageNo'];
		$nowDate    = $cycleStageArr['nowDate'];
		$maxCycleNo = $cycleStageArr['maxCycleNo'];

		//Logger::debug(array(__METHOD__, 'STAGE_AWARD--I was calcAward=='));
		$upInfo  = $sysHorse;
		$nowtime = time();

		$upInfo['stage'] = M_Horse::STAGE_AWARD; //4领奖

		M_Horse::addHorseSysMsg($sysHorse); //比赛阶段结束发送广播

		if ($cycleNo == $maxCycleNo) {
			$cityAwardArr = M_Horse::getFirstIdAward($nowDate, $cycleNo);
			$firCityId    = $cityAwardArr['firstCityId'];
			$firAward     = $cityAwardArr['firstCityAward'];

			$upInfo['first_city_id'] = $firCityId;
			$upInfo['first_award']   = $firAward;
			//Logger::debug(array(__METHOD__, "STAGE_Award--friCityId==", $cycleNo, $firCityId, $firAward));
		}

		$upInfo['stage_run_no']    = 0;
		$upInfo['stage_iscalc'][0] = M_Horse::STAGE_BETTING;
		$upInfo['stage_iscalc'][1] = $cycleNo + 1;

		//Logger::debug(array($sysHorse['stage_iscalc'], $cycleNo));

		$upInfo = M_Horse::makeJsonArr($upInfo);
		M_Horse::updateSysHorse($upInfo, $nowDate, $cycleNo, true);
	}

	/******************************************************************************************/
	/** 获取赔率数组 1D */
	static public function getBackReceRate($conf) {
		$arrConf = $RateT = array();
		$valA    = $valB = $valC = $valD = 0;
		foreach ($conf as $key => $val) {
			$arrConf[$key] = $val * 10;
		}
		$valE     = mt_rand($arrConf[2] + 1, $arrConf[3]);
		$arrRange = range(10, $arrConf[3]);

		$i = 0;
		while ($i < 1000) {
			$i++;
			$arrIdx = array_rand($arrRange, 4);
			if (!in_array($valE, array($arrRange[$arrIdx[0]], $arrRange[$arrIdx[1]], $arrRange[$arrIdx[2]], $arrRange[$arrIdx[3]]))) {
				$valA = $arrRange[$arrIdx[0]];
				$valB = $arrRange[$arrIdx[1]];
				$valC = $arrRange[$arrIdx[2]];
				$valD = $arrRange[$arrIdx[3]];
				break;
			}
		}
		(1000 == $i) && Logger::error(array(__METHOD__, 'Rand HorseBackReceRate Data Fail', func_get_args()));

		if (!empty($valA) && !empty($valB) && !empty($valC) && !empty($valD)) {
			$RateT = array($arrConf[0], $arrConf[1], $valA, $valB, $valC, $valD, $valE);
		} else {
			$RateT = array($arrConf[0], $arrConf[1], 15, 25, 35, 45, $valE); //给默认赔率，防止程序出错
		}
		shuffle($RateT); //随机打乱顺序

		$arrBackReceRate = array();
		foreach ($RateT as $v) {
			$arrBackReceRate[] = round($v / 10, 1);
		}
		//Logger::debug(array(__METHOD__, 'arrBackReceRate==', $arrBackReceRate));
		return $arrBackReceRate;
	}

	/** 获取各号码投注总数 1D */
	static public function getBettingAll($cycleNo) {
		$horse1All   = $horse2All = $horse3All = $horse4All = $horse5All = $horse6All = $horse7All = 0;
		$nowDate     = date('Ymd');
		$arrCityInfo = B_DB::instance('CityHorse')->getRows($nowDate, $cycleNo);
		if (!empty($arrCityInfo) && is_array($arrCityInfo)) {
			foreach ($arrCityInfo as $cityHorse) {
				$horse1All += $cityHorse['horse1'];
				$horse2All += $cityHorse['horse2'];
				$horse3All += $cityHorse['horse3'];
				$horse4All += $cityHorse['horse4'];
				$horse5All += $cityHorse['horse5'];
				$horse6All += $cityHorse['horse6'];
				$horse7All += $cityHorse['horse7'];
			}
		}
		$arrBetting = array($horse1All, $horse2All, $horse3All, $horse4All, $horse5All, $horse6All, $horse7All);
		//Logger::debug(array(__METHOD__, 'arrBetting==', $arrBetting));
		return $arrBetting;
	}

	/** 获取各号码需赔付军饷数 1D */
	static public function getBackReceVal($sysHorse, $arrBetting) {
		$arrBackVal = array(0, 0, 0, 0, 0, 0, 0);
		if (!empty($arrBetting) && is_array($arrBetting)) {
			foreach ($arrBetting as $k => $v) {
				$no             = $k + 1;
				$key            = 'horse' . $no;
				$arrBackVal[$k] = $sysHorse[$key][2] * $arrBetting[$k]; //赔率 * 此号投注总额
			}
		}

		//Logger::debug(array(__METHOD__, func_get_args(), 'perPayMoney==', $arrBackVal));
		return $arrBackVal;
	}

	/** 获取排好序的号码数组 1D */
	static public function getHorseRanking($sysHorse, $arrBetting) {
		$winX = array_sum($arrBetting) * 0.7; //胜利数值
		//var_dump($winX);
		$tmp = array();
		//计算各要赔付的军饷数
		foreach ($arrBetting as $k => $v) {
			$no  = $k + 1;
			$key = 'horse' . $no;

			$newVal = $sysHorse[$key][2] * $arrBetting[$k]; //赔率 * 此号投注总额
			if ($winX >= $newVal) {
				$m         = $winX - $newVal;
				$tmp[$m][] = $k;
			}
		}

		//取出最小值
		$minVal = min(array_keys($tmp));

		//取出最小值对应的KEY数组
		$minArr = $tmp[$minVal];

		//从随机抽取1个
		$tKey   = array_rand($minArr, 1);
		$minKey = $minArr[$tKey];

		$arrRanking = array($minKey);

		unset($arrBetting[$minKey]);
		$arrNo = array_keys($arrBetting);
		shuffle($arrNo);

		foreach ($arrNo as $val) {
			$arrRanking[] = $val;
		}

		$ret = array_flip($arrRanking); //交换数组中的键和值
		//var_dump($ret);
		//Logger::debug(array(__METHOD__, 'arrRanking==no=>rank', $ret));
		return $ret; // 号码对应排行(都从0开始计)
	}

	/**
	 * 获取 非NPC武器ID(越野系统7个号码)
	 * 武器ID,序号,状态,array(array(事件1,播放时间戳)...array(事件15,播放时间戳))
	 * @author chenhui on 20121209
	 * @param array array(1=>'神勇',2=>'黑马',3=>'低调',4=>'蛋疼',5=>'懒惰',6=>'正常')
	 * @return array array() 2D
	 */
	static public function getRandHorseData($conf, $horseWeaponArr) {
		$ret = $arrIdSpeed = array();
		//$horseWeaponArr = array(80,81,82,83,93,94,103,107,123,131,134,137,147,218,220,229,265,279,281);
		if (!empty($horseWeaponArr)) {
			foreach ($horseWeaponArr as $weaponId) {
				$baseInfo              = M_Weapon::baseInfo($weaponId);
				$arrIdSpeed[$weaponId] = $baseInfo['speed'];
			}
		}
		asort($arrIdSpeed); //按速度从小到大排序(保持索引)
		$arrId = array_rand($arrIdSpeed, 7); //随机7个已排序武器ID

		foreach ($arrId as $k => $weaId) {
			$ret[] = array($weaId, $k + 1, array_rand($conf)); //武器ID,排序号,状态ID
		}

		shuffle($ret); //打乱顺序
		//Logger::debug(array(__METHOD__, func_get_args(), 'RandHorseData==', $ret));
		return $ret;
	}

	/**
	 *
	 * 获取各事件对应时间戳 3D(时间,事件)
	 * @param array $horseConf
	 * @param array $sysHorse
	 * @param array $arrRankNo
	 * @param int $runStartTime 比赛开始
	 * @param int $runIntervalTime 比赛阶段每段所需秒数
	 */
	static public function getEventTime($horseConf, $sysHorse, $arrRankNo, $runStartTime, $runIntervalTime) {
		$ret = $arrTime = array();
		$i   = 0; //索引
		//Logger::debug(array(__METHOD__, $arrRankNo, $runStartTime, $runIntervalTime));

		foreach ($arrRankNo as $horseNo => $rankNo) {
			$arrT       = array();
			$moreVal    = $horseConf[4][$rankNo][3]; //此号马加速事件比减速事件多的数量
			$incrNum    = mt_rand($horseConf[4][$rankNo][1], $horseConf[4][$rankNo][2]); //此号马加速事件总数
			$decrNum    = $incrNum - $moreVal; //此号马减速事件总数
			$uniformNum = $horseConf[4][$rankNo][0] - $incrNum - $decrNum; //此号马匀速事件总数
			$arrEvent   = array();
			$arrIncr    = ($incrNum > 0) ? array_fill(0, $incrNum, M_Horse::EVENT_INCR) : array();
			$arrDecr    = ($decrNum > 0) ? array_fill(0, $decrNum, M_Horse::EVENT_DECR) : array();
			$arrUniform = ($uniformNum > 0) ? array_fill(0, $uniformNum, M_Horse::EVENT_UNIFORM) : array();
			//Logger::debug(array(__METHOD__, 'STAGE_WAIT--getEventTime==HorseEvent==', $rankNo, $incrNum, $uniformNum, $decrNum));
			$arrEvent = array_merge($arrIncr, $arrDecr, $arrUniform); //15个事件
			shuffle($arrEvent); //打乱顺序

			foreach ($arrEvent as $k => $event) {
				$arrEId = $horseConf[3][$event - 1];
				$eId    = $arrEId[array_rand($arrEId)]; //随机一个此类型事件的ID
				$begin  = $runStartTime + $runIntervalTime * $k;
				//Logger::debug(array(__METHOD__, $runStartTime, $runIntervalTime, $k, $begin));

				$end = $begin + $runIntervalTime;

				$eventRndTime = $begin + mt_rand(1, $runIntervalTime);

				$arrT[] = array(intval($eId), $eventRndTime, $event); //事件ID1,发生时间戳,权重值

				if ($i == 0) {
					$arrTime[] = array($begin, $end); //此段开始时间戳,结束时间戳
				}
			}
			$i++;
			$ret[$horseNo] = $arrT;
		}
		//Logger::debug(array(__METHOD__, $arrTime));
		return array($arrTime, $ret); //时间,事件
	}

	/**
	 * 获取城市累计奖励值
	 * @author
	 * @param $nowDate
	 * @param $cycleNo
	 * @return array(    'firstCityId'        => 累计奖励第一名城市ID<br>
	 * 'firstCityAward'    => 累计奖励第一名的奖励值<br>
	 * 'cityAwardArr'        => 城市ID=>累计奖励值);
	 */
	static public function getFirstIdAward($nowDate, $cycleNo) {
		$firstCityId = $firstCityAward = 0;

		$cityAwardArr = array();
		for ($i = 1; $i <= $cycleNo; $i++) {
			$sysH = M_Horse::getSysHorse($nowDate, $i);
			$arrT = $sysH['award_data'];
			if (!empty($arrT) && is_array($arrT)) {
				foreach ($arrT as $cityId => $award) {
					if (isset($cityAwardArr[$cityId])) {
						$cityAwardArr[$cityId] += $award;
					} else {
						$cityAwardArr[$cityId] = $award;
					}
				}
			}
		}
		if ($cityAwardArr) {
			arsort($cityAwardArr); //对数组进行逆向排序并保持索引关系
			$firstCityId    = key($cityAwardArr);
			$firstCityAward = current($cityAwardArr);
		}

		$ret = array(
			'firstCityId'    => $firstCityId, //累计奖励第一名城市ID
			'firstCityAward' => $firstCityAward, //累计奖励第一名的奖励值
			'cityAwardArr'   => $cityAwardArr //城市ID=>累计奖励值
		);
		return $ret;
	}

	/** 比赛阶段结束发送广播 */
	static public function addHorseSysMsg($sysHorse) {
		$arrRankT = array(
			'Horse1' => $sysHorse['horse1'][7],
			'Horse2' => $sysHorse['horse2'][7],
			'Horse3' => $sysHorse['horse3'][7],
			'Horse4' => $sysHorse['horse4'][7],
			'Horse5' => $sysHorse['horse5'][7],
			'Horse6' => $sysHorse['horse6'][7],
			'Horse7' => $sysHorse['horse7'][7],
		);
		$arrRank  = array_flip($arrRankT); //交换数组中的键和值
		$horseNo  = $arrRank[1]; //第一名马的编号
		//$newHorseNo = strtolower($horseNo);
		$firstNo = substr($horseNo, -1); //马号数字,从1开始

		$title = json_encode(array(T_Lang::HORSE_NO_WIN, $firstNo));
		$msg   = implode("\t", array($title, 0, 5));
		M_Chat::addWorldMessage(uniqid(), $msg, T_Chat::CHAT_SYS_RADIO);
	}

	/** 改变数组值格式从数组变成json */
	static public function makeJsonArr($upInfo) {
		$ret = array();
		if (!empty($upInfo) && is_array($upInfo)) {
			foreach ($upInfo as $key => $val) {
				if (in_array($key, array('run_per_time', 'stage_iscalc', 'horse1', 'horse2', 'horse3', 'horse4', 'horse5', 'horse6', 'horse7', 'join_log', 'award_log', 'award_data'))) {
					$ret[$key] = json_encode($val);
				} else {
					$ret[$key] = $val;
				}
			}
		}
		return $ret;
	}

}

?>