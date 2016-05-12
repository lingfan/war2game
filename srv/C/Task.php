<?php

/**
 * 任务控制器
 */
class C_Task extends C_I {
	/**
	 * 获取系统定义的任务基础数据
	 * @author chenhui on 20110728
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetAllSysInfo() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data  = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$flag  = T_App::SUCC;
		$errNo = '';
		$data  = M_Base::task();

		return B_Common::result($errNo, $data);
	}

	/**
	 * 完成任务并领奖
	 * @author chenhui on 20110727
	 * @param int $taskId 任务ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AFinish($taskId) {

		$errNo  = T_ErrNo::ERR_PARAM;
		$data   = array();
		$taskId = intval($taskId);

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$cityTaskInfo = M_Task::getCityTask($cityInfo['id']); //城市任务信息
		$taskBaseInfo = M_Task::baseInfo($taskId); //任务基础信息

		$which_ok  = 'tasks_ok';
		$which_end = 'tasks_end';
		(M_Task::TYPE_DAILY == $taskBaseInfo['type']) && $which_ok = 'tasks_daily_ok';
		(M_Task::TYPE_DAILY == $taskBaseInfo['type']) && $which_end = 'tasks_daily_end';

		$arrTaskOK  = !empty($cityTaskInfo[$which_ok]) ? json_decode($cityTaskInfo[$which_ok], true) : array();
		$arrTaskEnd = !empty($cityTaskInfo[$which_end]) ? json_decode($cityTaskInfo[$which_end], true) : array();
		if (!empty($cityInfo) && !empty($cityTaskInfo) && !empty($taskBaseInfo)) {
			if (in_array($taskId, $arrTaskOK)) {
				if (!in_array($taskId, $arrTaskEnd)) {
					$ret2 = false;

					$cacheVal = M_Task::getLastTaskCache($cityInfo['id']);
					if ($taskId != $cacheVal) {

						$bAward = true;
						if ($bAward) {
							M_Task::addCityTaskEnd($cityInfo['id'], $taskId, $taskBaseInfo['type']); //添加已领奖
							M_Task::setLastTaskCache($cityInfo['id'], $taskId);
							$ret2 = true;
						}
					} else {
						$ret2 = true;
					}
					$ret2 && $errNo = ''; //错误码置为空
				} else {
					$errNo = '';
				}
			} else {
				$errNo = T_ErrNo::TASK_NOT_COMP; //此任务未完成
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 获取在线奖励时间
	 * @author huwei on 20120130
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetOLAwardTime() {
		$obj = new C_Task();
		return $obj->AGetOLAward(1);
	}


	/**
	 * 领取在线奖励
	 * @author huwei on 20120130
	 * @param int $type 0领取奖励,1查看时间
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetOLAward($type = 0) {

		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$basecfg = M_Config::getVal();
		$conf    = $basecfg['config_online_award'];
		$info    = $objPlayer->City()->getAwardTime();
		$tmpLv   = !empty($info['award_lv']) ? $info['award_lv'] : 1;

		if (empty($type) &&
			!empty($conf[$tmpLv]) &&
			$info['award_time'] >= $conf[$tmpLv][0]
		) {
			$awardArr = M_Award::rateResult($conf[$tmpLv][1]);

			//获取奖励数据
			$nextLv = $tmpLv + 1;
			//设置下级线奖励时间
			$ret = $objPlayer->City()->setVisit(array('award_time' => 0, 'award_lv' => $nextLv));
			if ($ret) {
				$bAward = $objPlayer->City()->toAward($awardArr, __METHOD__);
				$objPlayer->save();
				$data['Content']   = M_Award::toText($awardArr);
				$data['AwardLv']   = $nextLv;
				$data['OLTime']    = 0;
				$data['AwardTime'] = isset($conf[$nextLv][0]) ? $conf[$nextLv][0] : 0;
				$errNo             = '';

			}
		} else {

			$data['OLTime']  = $info['award_time'];
			$data['AwardLv'] = $info['award_lv'];
			//是否最后一个等级 领取完成
			$data['AwardTime'] = isset($conf[$info['award_lv']]) ? $conf[$tmpLv][0] : 0;
			$errNo             = '';
		}

		return B_Common::result($errNo, $data);

	}


	public function AGetCard($code = '') {
		$errNo = T_ErrNo::ERR_ACTION;
		$data  = array();
		$ret   = false;
		$code  = trim($code);
		if (!empty($code)) {
			$codeArr = M_Card::decrypt($code);
			//Logger::debug(array(__METHOD__, $codeArr));
			$errNo = T_ErrNo::CODE_NO_EXIST; //无效的卡号
			if (!empty($codeArr)) {
				$propsId = $codeArr['pid'];
				$type    = $codeArr['type'];
				$idx     = $codeArr['idx'];

				$objPlayer = $this->objPlayer;
				$cityInfo  = $objPlayer->getCityBase();

				$cardType = substr($type, 1);
				$isReuse  = substr($type, 0, 1);
				//Logger::debug(array(__METHOD__, $isReuse, $cardType));
				if (M_Card::checkCityCode($cityInfo['id'], $code)) {
					$errNo = T_ErrNo::SUBMIT_TOO_FAST; //提交太频繁
				} else {
					M_Card::setTmpCityCode($cityInfo['id'], $code);

					$tmpArr = array(
						'card_type' => $cardType,
						'props_id'  => $propsId,
						'idx'       => $idx
					);

					$row = M_Card::getInfo($tmpArr);
					//Logger::debug(array(__METHOD__, $tmpArr, $row));
					if ($row) {
						$errNo = T_ErrNo::CODE_IS_USE; //卡已被使用
					} else {
						$canUse = false;
						if ($isReuse == M_Card::REPEAT_TYPE) //该道具类型是否可重复领取
						{
							//可以则使用
							$canUse = true;
						} else {
							$tmpArr = array(
								'city_id'   => $cityInfo['id'],
								'card_type' => $cardType,
							);

							$row = M_Card::getInfo($tmpArr);
							if ($row) {
								$errNo = T_ErrNo::CODE_TYPE_IS_USE; //同类卡已使用
							} else {
								//可以则使用
								$canUse = true;
							}
						}

						if ($canUse) {
							if ($objPlayer->Pack()->incr($propsId, 1)) {
								$info = array(
									'city_id'   => $cityInfo['id'],
									'card_type' => $cardType,
									'idx'       => $idx,
									'props_id'  => $propsId,
									'create_at' => time()
								);
								$ret  = M_Card::useCard($info);
							}

							if ($ret) {
								$pinfo = M_Props::baseInfo($info['props_id']);
								if (isset($pinfo['name'])) {
									$data = array(
										'name' => $pinfo['name']
									);
									//M_Sync::addQueue($cityInfo['id'], M_Sync::KEY_CITY_INFO, array('ShowNewbeCard'=>0));
								}

								$errNo = '';
							}
						}

					}
				}
			}
		}

		return B_Common::result($errNo, $data);
	}


	/***剧情任务****************/
	/**
	 * 获取已领取剧情任务奖励的战役数据
	 * @author chenhui on 20120814
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetDramaAwardedList() {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data  = array();

		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$cityId   = intval($cityInfo['id']);
		$cityTask = M_Task::getCityTask($cityId);

		$dramaEnd = json_decode($cityTask['drama_end'], true); //已领奖数组
		empty($dramaEnd) && $dramaEnd = array();
		if (is_array($dramaEnd)) {
			list($lastChapterNo, $lastCampaignNo, $lastPointNo) = M_Formula::calcParseFBNo($cityInfo['last_fb_no']);
			for ($i = 1; $i <= $lastChapterNo; $i++) //章(从1开始)
			{
				$chapterInfo = M_SoloFB::getInfo($i);
				$campNo      = count($chapterInfo['fb_list']);
				if ($i == $lastChapterNo) {
					$campNo = $lastCampaignNo;
					if (isset($chapterInfo['fb_list'][$lastCampaignNo]['checkpoint_data'][$lastPointNo + 1])) {
						$campNo = $lastCampaignNo - 1;
					}
				}

				for ($j = 0; $j < $campNo; $j++) {
					$val = 0;
					if (isset($dramaEnd[$i])) {
						$campBinStr = strrev(decbin($dramaEnd[$i]));
						$val        = isset($campBinStr[$j]) ? $campBinStr[$j] : 0;
					}
					$data[$i - 1][] = $val;
				}
			}
		}

		$flag  = T_App::SUCC;
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 领取某剧情任务完成奖励
	 * @author chenhui on 20120814
	 * @param int $chapterNo 章节ID
	 * @param int $campaignNo 战役ID
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AReceDramaAward($chapterNo, $campaignNo) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data  = array();

		$objPlayer  = $this->objPlayer;
		$cityInfo   = $objPlayer->getCityBase();
		$chapterNo  = intval($chapterNo); //章节编号
		$campaignNo = intval($campaignNo); //战役编号


		$fbInfo = M_SoloFB::getDetail($chapterNo, $campaignNo);

		if (empty($fbInfo)) {
			return B_Common::result(T_ErrNo::FB_DATA_ERR);
		}
		list($lastChapterNo, $lastCampaignNo, $lastPointNo) = M_Formula::calcParseFBNo(M_SoloFB::calcNextFBNo($cityInfo['last_fb_no']));

		$canRece = false;
		if ($chapterNo <= $lastChapterNo) {
			if ($chapterNo < $lastChapterNo) {
				$canRece = true;
			} else if ($chapterNo = $lastChapterNo) {
				if ($campaignNo < $lastCampaignNo) {
					$canRece = true;
				} else if ($campaignNo = $lastCampaignNo) {
					if (!isset($chapterInfo['fb_list'][$lastCampaignNo]['checkpoint_data'][$lastPointNo + 1])) {
						$canRece = true;
					}
				}
			}
		}

		if (!$canRece) {
			return B_Common::result(T_ErrNo::FB_NOT_COMPLETE);
		}
		$dramaAward = M_Config::getVal('drama_award'); //剧情任务奖励配置
		if (!isset($dramaAward[$chapterNo][$campaignNo - 1]) || empty($dramaAward[$chapterNo][$campaignNo - 1])) {
			return B_Common::result(T_ErrNo::AWARD_NOT_EXISTS);
		}
		$cityId = intval($cityInfo['id']);

		$cityTask    = M_Task::getCityTask($cityId);
		$arrDramaEnd = json_decode($cityTask['drama_end'], true); //已领奖数组
		$campData    = isset($arrDramaEnd[$chapterNo]) ? $arrDramaEnd[$chapterNo] : 0;
		$andRet      = $campData & pow(2, $campaignNo - 1);

		//未领取[$andRet == 0]
		if ($andRet >= 1) {
			return B_Common::result(T_ErrNo::AWARD_HAD);
		}

		$awardId = $dramaAward[$chapterNo][$campaignNo - 1];

		$awardArr = M_Award::rateResult($awardId);
		$bAward   = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task);

		if ($bAward) {
			$data = M_Award::toText($awardArr);
		}

		$arrDramaEnd[$chapterNo] = $campData + pow(2, $campaignNo - 1);
		$ret                     = M_Task::updateCityTask($cityId, array('drama_end' => json_encode($arrDramaEnd)));
		if (!$ret) {
			return B_Common::result(T_ErrNo::ERR_UPDATE);
		}

		return B_Common::result('');
	}

	/**
	 * 领取每日奖励
	 * @author duhuihui on 20120822
	 * @param int $day 领取第几天的奖励
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AGetDailyAward() {
		//操作结果默认为失败0
		$errNo     = T_ErrNo::ERR_ACTION; //失败原因默认
		$data      = array();
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();

		$now         = time();
		$nowD        = strtotime(date('Ymd')); //当前日期
		$dailyAward  = M_Config::getVal('daily_login_award'); //得到config的内容
		$startDay    = $dailyAward[1];
		$endDay      = $dailyAward[2];
		$dayAwardArr = explode(',', $dailyAward[3]);

		$firstDay = 0;
		if ($startDay == 0 && $nowD <= strtotime($endDay)) {
			$firstDay = date('Ymd', $cityInfo['created_at']);
		} else if ($nowD >= strtotime($startDay) && $nowD <= strtotime($endDay)) //有开始时间
		{
			$firstDay = date('Ymd', strtotime($startDay)); //玩家第一次进入游戏的日期
		}

		if (!empty($firstDay)) {
			$diffTime = max($now - strtotime($firstDay), 0); //计算时间差
			$diffDay  = ceil($diffTime / 3600 / 24); //计算天数
		} else {
			$diffDay = 0;
		}

		$hadAward = M_Task::getLoginDailyAward($cityInfo['id']);
		$idx      = max($diffDay - 1, 0);
		$err      = '';
		if ($nowD < strtotime($startDay) || $nowD > strtotime($endDay)) {
			$err = T_ErrNo::ERR_DAYLY_AWARD_OUT; //不在活动期间
		} else if ($diffDay < 0 || $diffDay > count($dayAwardArr)) {
			$err = T_ErrNo::ERR_LOGIN_DAILY_AWARD;
		} else if (!empty($hadAward[$diffDay])) //是否领取过奖励
		{
			$err = T_ErrNo::ERR_LOGIN_DAILY_HAD;
		} else if (!isset($dayAwardArr[$idx])) //是存在对应奖励数据
		{
			$err = T_ErrNo::ERR_LOGIN_DAILY_AWARD;
		}

		if (empty($err)) {
			M_Task::setLoginDailyAward($cityInfo['id'], $diffDay);

			$awardArr = M_Award::rateResult($dayAwardArr[$idx]);
			$bAward   = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task);

			$flag  = T_App::SUCC;
			$errNo = '';
			$data  = M_Award::toText($awardArr);
		} else {
			$errNo = $err;
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 每日奖励列表
	 * @author duhuihui on 20120822
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ADailyAwardList() {
		//操作结果默认为失败0
		$errNo       = T_ErrNo::ERR_ACTION; //失败原因默认
		$data        = array();
		$objPlayer   = $this->objPlayer;
		$cityInfo    = $objPlayer->getCityBase();
		$now         = time(); //当前日期
		$nowD        = strtotime(date('Ymd')); //当前日期
		$dailyAward  = M_Config::getVal('daily_login_award'); //得到config的内容
		$startDay    = $dailyAward[1];
		$endDay      = $dailyAward[2];
		$dayAwardArr = explode(',', $dailyAward[3]);

		$firstDay = 0;
		if ($startDay == 0 && $nowD <= strtotime($endDay)) {
			$firstDay = date('Ymd', $cityInfo['created_at']);
		} else if ($nowD >= strtotime($startDay) && $nowD <= strtotime($endDay)) //有开始时间
		{
			$firstDay = date('Ymd', strtotime($startDay)); //玩家第一次进入游戏的日期
		}

		if (!empty($firstDay)) {
			$diffTime = max($now - strtotime($firstDay), 0); //计算时间差
			$diffDay  = ceil($diffTime / 3600 / 24); //计算天数
		} else {
			$diffDay = 0;
		}

		$hadAwardDay = M_Task::getLoginDailyAward($cityInfo['id']); //判断是否已经领过奖了1已领奖0未领奖
		if (!empty($dayAwardArr[0])) {
			foreach ($dayAwardArr as $key => $value) {
				$awardArr        = M_Award::rateResult($value);
				$award           = M_Award::toText($awardArr);
				$award[]         = $award;
				$k               = $key + 1;
				$hadAwardDay[$k] = isset($hadAwardDay[$k]) ? $hadAwardDay[$k] : 0;
			}
		}

		$data['AwardList'] = $award;
		$data['HadAward']  = $hadAwardDay;
		$data['CurDay']    = $diffDay;
		$errNo             = '';

		return B_Common::result($errNo, $data);

	}

	/**
	 * 学院奖励列表
	 * @author duhuihui on 20121207
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AActiveList() //得到
	{
		//操作结果默认为失败0
		$errNo      = T_ErrNo::ERR_ACTION; //失败原因默认
		$data       = array();
		$awardList  = array();
		$objPlayer  = $this->objPlayer;
		$cityInfo   = $objPlayer->getCityBase();
		$now        = time();
		$dailyAward = M_Config::getVal('active_award'); //得到config的内容
		list($IsOpen, $awardField) = M_Task::getHoldNpcActiveStaus($cityInfo['id'], $dailyAward);
		if ($IsOpen == 1 || $IsOpen == 2) {
			if (!empty ($dailyAward['list'])) {

				foreach ($dailyAward['list'] as $key => $value) {
					$awardArr        = M_Award::rateResult($value);
					$award           = M_Award::toText($awardArr);
					$awardList[$key] = $award;
				}
				$flag  = T_App::SUCC;
				$errNo = '';
			}
		} elseif ($IsOpen == 3 || $IsOpen == 4) {
			if (!empty ($dailyAward['list2'])) {
				foreach ($dailyAward['list2'] as $key => $value) {
					$awardArr        = M_Award::rateResult($value);
					$award           = M_Award::toText($awardArr);
					$awardList[$key] = $award;
				}
				$flag  = T_App::SUCC;
				$errNo = '';
			}
		} elseif ($IsOpen == 5 || $IsOpen == 6) {
			if (!empty ($dailyAward['list3'])) {
				$awardId            = $dailyAward['list3']['award'];
				$awardArr           = M_Award::rateResult($awardId);
				$award              = M_Award::toText($awardArr);
				$awardList['award'] = $award;
				$flag               = T_App::SUCC;
				$errNo              = '';

			}


		} elseif ($IsOpen == 7 || $IsOpen == 8) {
			if (!empty ($dailyAward['list4'])) {
				$awardId            = $dailyAward['list4']['award'];
				$awardArr           = M_Award::rateResult($awardId);
				$award              = M_Award::toText($awardArr);
				$awardList['award'] = $award;
				$flag               = T_App::SUCC;
				$errNo              = '';

			}
		} elseif ($IsOpen == 9 || $IsOpen == 10) {
			if (!empty ($dailyAward['list5'])) {
				$awardList = array();
				$awardId   = $dailyAward['list5']['award'];

				$awardArr = M_Award::rateResult($awardId);
				$award    = M_Award::toText($awardArr);

				$awardList['award'] = $award;
				$flag               = T_App::SUCC;
				$errNo              = '';

			}

		} elseif ($IsOpen == 0) {
			$flag  = T_App::SUCC;
			$errNo = '';
		}
		$data['AwardList']  = $awardList;
		$data['AwardField'] = $awardField;
		$data['IsOpen']     = $IsOpen; //是否开放
		$data['StartTime']  = $dailyAward['start']; //开始时间
		$data['EndTime']    = $dailyAward['end']; //结束时间

		return B_Common::result($errNo, $data);
	}

	/**
	 * 领取第几个任务奖励
	 * @author duhuihui on 20121207
	 * @param int $day 领取第几个的奖励
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AReceiveState($key) //领取奖励
	{
		//操作结果默认为失败0
		$errNo      = T_ErrNo::ERR_ACTION; //失败原因默认
		$data       = array();
		$objPlayer  = $this->objPlayer;
		$cityInfo   = $objPlayer->getCityBase();
		$dailyAward = M_Config::getVal('active_award'); //得到config的内容

		$nowDay   = date('Ymd'); //玩家点击领奖的当前日期
		$startDay = $dailyAward['start']; //学院奖励起始时间
		$endDay   = $dailyAward['end']; //学院奖励截止时间
		$d1       = strtotime($nowDay); //今天的日期
		$d2       = strtotime($startDay);
		$d3       = strtotime($endDay);
		$taskInfo = M_Task::getCityTask($cityInfo['id']); //获取城市信息
		list($IsOpen, $awardField) = M_Task::getHoldNpcActiveStaus($cityInfo['id'], $dailyAward);
		if (!empty($taskInfo['active_filed'])) {
			$array_active_filed = json_decode($taskInfo['active_filed'], true);
		}

		if ($d1 < $d2 || $d1 > $d3) {
			$err = T_ErrNo::ERR_ACTIVE_AWARD_OUT; //不在活动期间
		} else if (($IsOpen == 1 || $IsOpen == 2) && empty($array_active_filed['list'][$key])) //还未完成此项任务
		{
			$err = T_ErrNo::ERR_ACTIVE_AWARD;
		} else if (($IsOpen == 1 || $IsOpen == 2) && $array_active_filed['list'][$key] == 2) //还未完成此项任务
		{
			$err = T_ErrNo::ERR_ACTIVE_AWARD_HAD;
		} else if (($IsOpen == 3 || $IsOpen == 4) && empty($array_active_filed['list'][$key])) //还未完成此项任务
		{
			$err = T_ErrNo::ERR_ACTIVE_AWARD;
		} else if (($IsOpen == 3 || $IsOpen == 4) && $array_active_filed['list'][$key] == 2) //还未完成此项任务
		{
			$err = T_ErrNo::ERR_ACTIVE_AWARD_HAD;
		} else if (($IsOpen == 5 || $IsOpen == 6) && empty($array_active_filed['list']['award'])) //还未完成此项任务
		{
			$err = T_ErrNo::ERR_ACTIVE_AWARD;
		} else if (($IsOpen == 5 || $IsOpen == 6) && $array_active_filed['list']['award'] == 2) //还未完成此项任务
		{
			$err = T_ErrNo::ERR_ACTIVE_AWARD_HAD;
		} else if (($IsOpen == 7 || $IsOpen == 8) && empty($array_active_filed['list']['award'])) //还未完成此项任务
		{
			$err = T_ErrNo::ERR_ACTIVE_AWARD;
		} else if (($IsOpen == 7 || $IsOpen == 8) && $array_active_filed['list']['award'] == 2) //还未完成此项任务
		{
			$err = T_ErrNo::ERR_ACTIVE_AWARD_HAD;
		} else if (($IsOpen == 9 || $IsOpen == 10) && empty($array_active_filed['list']['award'])) //还未完成此项任务
		{
			$err = T_ErrNo::ERR_ACTIVE_AWARD;
		} else if (($IsOpen == 9 || $IsOpen == 10) && $array_active_filed['list']['award'] == 2) //还未完成此项任务
		{
			$err = T_ErrNo::ERR_ACTIVE_AWARD_HAD;
		}
		if (empty($err)) {
			if ($IsOpen == 1 || $IsOpen == 2) {
				$array_active_filed['list'][$key] = 2;
				$awardId                          = $dailyAward['list'][$key];
			} elseif ($IsOpen == 4 || $IsOpen == 3) {
				$array_active_filed['list'][$key] = 2;
				$awardId                          = $dailyAward['list2'][$key];
			} elseif ($IsOpen == 5 || $IsOpen == 6) {
				$array_active_filed['list']['award'] = 2;
				$awardId                             = $dailyAward['list3'][$key];
			} elseif ($IsOpen == 7 || $IsOpen == 8) {
				$array_active_filed['list']['award'] = 2;

				$awardId = $dailyAward['list4'][$key];
			} elseif ($IsOpen == 9 || $IsOpen == 10) {
				$array_active_filed['list']['award'] = 2;
				$awardId                             = $dailyAward['list5'][$key];
			}

			$awardArr = M_Award::rateResult($awardId);
			$award    = M_Award::toText($awardArr);
			$bAward   = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task);

			M_Task::updateCityTask($cityInfo['id'], array('active_filed' => json_encode($array_active_filed)));
			list($IsOpen, $awardField) = M_Task::getHoldNpcActiveStaus($cityInfo['id'], $dailyAward);
			$flag  = T_App::SUCC;
			$errNo = '';
			$data  = '';
		} else {
			$errNo = $err;
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 补偿奖励列表
	 * @author duhuihui on 20121228
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ACompensateList() //得到
	{
		//操作结果默认为失败0
		$errNo        = T_ErrNo::ERR_ACTION; //失败原因默认
		$data         = array();
		$awardList    = array();
		$now          = time();
		$objPlayer    = $this->objPlayer;
		$cityInfo     = $objPlayer->getCityBase();
		$info         = M_Extra::getInfo($cityInfo['id']);
		$awardCityArr = isset($info['compensate_list']) ? json_decode($info['compensate_list'], true) : array();
		//城市已经领取过得奖励
		$awardBaseArr = M_Compensate::getBaseAwardList(); //基础奖励
		$diffArr      = !empty($awardCityArr) ? array_diff_key($awardCityArr, $awardBaseArr) : array(); // 城市   基础
		if (!empty($diffArr)) {
			$tempArr                 = array_diff_key($awardCityArr, $diffArr); // 城市   基础
			$awardData['award_data'] = $tempArr;
			$ret                     = M_Extra::setInfo($cityInfo['id'], array('compensate_list' => json_encode($tempArr)));

		}

		$flag  = T_App::SUCC;
		$errNo = '';

		if (!empty($awardBaseArr)) {
			$rank = M_Ranking::getRecordRankingsByCityId($cityInfo['id']);

			foreach ($awardBaseArr as $key => $awardList) //对基础奖励进行遍历
			{
				$temp           = 1;
				$awardCondition = json_decode($awardList['award_condition'], true);
				if ($awardList['s_time'] > $now || $awardList['e_time'] < $now) {
					$temp = 3;
				} else if ((!empty($awardCondition['renown']) && $cityInfo['renown'] < $awardCondition['renown']) ||
					(!empty($awardCondition['vip']) && $cityInfo['vip_level'] < $awardCondition['vip']) ||
					(!empty($awardCondition['level']) && $cityInfo['level'] < $awardCondition['level']) ||
					(!empty($awardCondition['warexp']) && $cityInfo['mil_medal'] < $awardCondition['warexp']) ||
					(!empty($awardCondition['record']) && ($rank > $awardCondition['record'] ||
							empty($rank)))
				) {
					$temp = 2;
				} else if (!isset($awardCityArr[$key])) {
					$temp = 0; //可以领取奖励
				} else if (isset($awardCityArr[$key]) &&
					$awardList['type'] == 2 &&
					$awardCityArr[$key] != date('Ymd')
				) {
					$temp = 0;
				}
				$awardText = json_decode($awardList['award_text'], true);

				$award = M_Award::toText($awardText);

				$data[] = array(
					'id'            => $awardList['id'],
					'award_text'    => $award, //领取奖励
					'award_desc'    => $awardList['award_desc'], //领取规则
					'type'          => $awardList['type'], //1已经领取过来 0未领取
					'start_time'    => $awardList['s_time'],
					'end_time'      => $awardList['e_time'],
					'receive_state' => $temp, //1已经领取过来 0未领取2没达到条件不能领取
				);
			}
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 领取第几个补偿奖励
	 * @author duhuihui on 20121228
	 * @param int $day 领取第几个的奖励
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ACompensateState($id) //领取奖励
	{
		//操作结果默认为失败0
		$errNo          = T_ErrNo::ERR_ACTION; //失败原因默认
		$data           = array();
		$now            = time();
		$objPlayer      = $this->objPlayer;
		$cityInfo       = $objPlayer->getCityBase();
		$info           = M_Extra::getInfo($cityInfo['id']);
		$awardCityArr   = isset($info['compensate_list']) ? json_decode($info['compensate_list'], true) : array();
		$awardBaseArr   = M_Compensate::getBaseAwardList(); //基础奖励
		$awardList      = isset($awardBaseArr[$id]) ? $awardBaseArr[$id] : array();
		$rank           = M_Ranking::getRecordRankingsByCityId($cityInfo['id']);
		$awardCondition = json_decode($awardList['award_condition'], true);
		if (!empty($awardList)) {
			$temp = 1;
			if ($awardList['s_time'] > $now || $awardList['e_time'] < $now) {
				$temp = 3;
			} else if ((!empty($awardCondition['renown']) && $cityInfo['renown'] < $awardCondition['renown']) || (!empty($awardCondition['vip']) && $cityInfo['vip_level'] < $awardCondition['vip'])
				|| (!empty($awardCondition['level']) && $cityInfo['level'] < $awardCondition['level']) || (!empty($awardCondition['warexp']) && $cityInfo['mil_medal'] < $awardCondition['warexp'])
				|| (!empty($awardCondition['record']) && ($rank > $awardCondition['record'] || empty($rank)))
			) {
				$temp = 2;
			} else if (!isset($awardCityArr[$id])) {
				$temp = 0; //可以领取奖励
			} else if (isset($awardCityArr[$id]) && $awardList['type'] == 2 && $awardCityArr[$id] != date('Ymd')) {
				$temp = 0;
			}
			if (empty($temp)) {
				$awardText = json_decode($awardList['award_text'], true);

				$award  = M_Award::toText($awardText);
				$bAward = $objPlayer->City()->toAward($awardText, B_Log_Trade::I_Task);


				if (isset($awardList['is_mail']) && $awardList['is_mail'] == 1) {
					$arr = $award;
					if ($arr) {
						$cententArr = array();
						foreach ($arr as $val) {
							if (!empty($val)) {
								$var = array(T_Lang::C_AWARD_MOD_NUM, array($val[2]), $val[3]);
								array_push($cententArr, $var);
							}
						}
					}
					$content = array(T_Lang::C_AWARD_MESSAGE, $cententArr);
					M_Message::sendSysMessage($cityInfo['id'], json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content));
				}
				$flag  = T_App::SUCC;
				$errNo = '';

				$awardCityArr[$id]       = date('Ymd');
				$awardData['award_data'] = $awardCityArr;
				M_Extra::setInfo($cityInfo['id'], array('compensate_list' => $awardCityArr));
			} else {
				$errNo = T_ErrNo::AWARD_NOT_EXISTS;
			}
		} else {
			$errNo = T_ErrNo::AWARD_NOT_EXISTS;
		}

		return B_Common::result($errNo, $data);
	}

	/**
	 * 首次充值奖励
	 * @author huwei
	 * @param int $had 0信息 1领取
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function AOncePay($had = 0) {
		//操作结果默认为失败0
		$errNo     = T_ErrNo::ERR_ACTION; //失败原因默认
		$data      = array();
		$had       = intval($had);
		$objPlayer = $this->objPlayer;
		$cityInfo  = $objPlayer->getCityBase();
		$cityId    = $cityInfo['id'];
		$now       = time(); //当前日期
		$awardId   = M_Config::getVal('first_recharge_id'); //得到config的内容

		if ($had == 1) {
			if (empty($cityInfo['first_recharge']) && !empty($cityInfo['total_mil_pay'])) {
				$awardArr = M_Award::rateResult($awardId);
				$award    = M_Award::toText($awardArr);
				$bAward   = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task);

				$upArr = array(
					'first_recharge' => $awardId,
				);
				$ret   = M_City::setCityInfo($cityId, $upArr);
				if ($ret) {
					$data['HadAward']  = 1;
					$data['AwardList'] = $award;

					M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, array('ShowOncePay' => 0));
					$errNo = '';
				}
			}
		} else {
			//不可领取
			$t = 0;
			if (empty($cityInfo['first_recharge']) && !empty($cityInfo['total_mil_pay'])) { //可领取
				$t = 1;
			}

			$awardArr = M_Award::allResult($awardId);
			$award    = M_Award::toText($awardArr, true);

			$data['HadAward']  = $t;
			$data['AwardList'] = $award;
			$errNo             = '';
		}

		return B_Common::result($errNo, $data);

	}


	/**
	 * 分段一次充值奖励
	 * @author huwei
	 * @param int $id 操作[0信息 1领取]
	 * @param int $type 奖励位置
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ASectionOncePay($id = 0, $type = 1) {
		//操作结果默认为失败0
		$errNo       = T_ErrNo::ERR_ACTION; //失败原因默认
		$data        = array();
		$objPlayer   = $this->objPlayer;
		$cityInfo    = $objPlayer->getCityBase();
		$nowTime     = time();
		$baseonce    = M_Config::getVal('config_pay_once_award');
		$cityTask    = M_Task::getCityTask($cityInfo['id']);
		$cityPayOnce = $cityTask['section_pay_once'];

		if (empty($cityPayOnce) || $baseonce['start'] != $cityPayOnce['t'][0] || $baseonce['end'] != $cityPayOnce['t'][1]) {
			$cityPayOnce = array(
				't'     => array($baseonce['start'], $baseonce['end']),
				'award' => array(),
			);
		}

		$errNo = T_ErrNo::SECTION_ONCE_PAY_EXPIRE;

		//if ($nowTime > strtotime($baseonce['start']) && $nowTime < strtotime($baseonce['end'])) { //时间范围内
		if ($id == 0) { //获取信息
			if (!empty($baseonce)) {
				foreach ($baseonce['data'] as $k => $val) {
					list($s, $e, $awardId) = $val;
					$awardArr = M_Award::allResult($awardId);
					$award    = M_Award::toText($awardArr, true);
					//[0未完成,1已完成未领取,2已领取]
					$flag  = isset($cityPayOnce['award'][$k]) ? $cityPayOnce['award'][$k] : 0;
					$row[] = array($s, $e, $award, $flag);
				}
				$data['Start'] = $baseonce['start'];
				$data['End']   = $baseonce['end'];
				$data['List']  = $row;

				$errNo = '';
			}
		} else if ($id == 1 && isset($baseonce['data'][$type])) { //领取奖励
			$errNo = T_ErrNo::SECTION_ONCE_PAY_NOT;
			$flag  = isset($cityPayOnce['award'][$type]) ? $cityPayOnce['award'][$type] : 0;
			if ($flag == 1) {
				list($s, $e, $awardId) = $baseonce['data'][$type];

				$awardArr = M_Award::rateResult($awardId);
				$award    = M_Award::toText($awardArr);
				$bAward   = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task); //奖励结果

				$flag                        = 2;
				$cityPayOnce['award'][$type] = $flag;
				$upInfo                      = array(
					'section_pay_once' => json_encode($cityPayOnce),
				);
				M_Task::updateCityTask($cityInfo['id'], $upInfo);

				$data = array(
					'Flag'  => $flag,
					'Award' => $award,
				);

				$errNo = '';
			}
		}
		//}
		return B_Common::result($errNo, $data);

	}

	/**
	 * 分段累计充值奖励
	 * @author huwei
	 * @param int $id 操作[0信息 1领取]
	 * @param int $type 奖励位置
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ASectionAddPay($id = 0, $type = 1) {
		//操作结果默认为失败0
		$errNo      = T_ErrNo::ERR_ACTION; //失败原因默认
		$data       = array();
		$objPlayer  = $this->objPlayer;
		$cityInfo   = $objPlayer->getCityBase();
		$nowTime    = time();
		$baseadd    = M_Config::getVal('config_pay_add_award');
		$cityTask   = M_Task::getCityTask($cityInfo['id']);
		$cityPayAdd = $cityTask['section_pay_add'];

		if (empty($cityPayAdd) || $baseadd['start'] != $cityPayAdd['t'][0] || $baseadd['end'] != $cityPayAdd['t'][1]) {
			$cityPayAdd = array(
				't'     => array($baseadd['start'], $baseadd['end']),
				'num'   => 0,
				'award' => array(),
			);
		}

		$errNo = T_ErrNo::SECTION_ADD_PAY_EXPIRE;
		//if ($nowTime > strtotime($baseadd['start']) && $nowTime < strtotime($baseadd['end'])) { //有效期
		if ($id == 0) { //获取信息
			if (!empty($baseadd)) {
				foreach ($baseadd['data'] as $k => $val) {
					list($num, $awardId) = $val;

					$awardArr = M_Award::allResult($awardId);
					$award    = M_Award::toText($awardArr, true);

					//[0未完成,1已完成未领取,2已领取]
					$flag = isset($cityPayAdd['award'][$k]) ? $cityPayAdd['award'][$k] : 0;
					//$flag = rand(0,2);
					$row[] = array($num, $award, $flag, $cityPayAdd['num']);
				}
				$data['Start'] = $baseadd['start'];
				$data['End']   = $baseadd['end'];
				$data['List']  = $row;
			}
		} else if (isset($baseadd['data'][$type])) { //领取奖励
			$flag = isset($cityPayAdd['award'][$type]) ? $cityPayAdd['award'][$type] : 0;
			if ($flag == 1) {
				list($num, $awardId) = $baseadd['data'][$type];

				$awardArr = M_Award::rateResult($awardId);
				$award    = M_Award::toText($awardArr);
				$bAward   = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task); //奖励结果

				$flag                       = 2;
				$cityPayAdd['award'][$type] = $flag;
				$upInfo                     = array(
					'section_pay_add' => json_encode($cityPayAdd),
				);
				M_Task::updateCityTask($cityInfo['id'], $upInfo);

				$data = array(
					'Flag'  => $flag,
					'Award' => $award,
				);
			}

		}
		$errNo = '';
		//}

		return B_Common::result($errNo, $data);

	}

	/**
	 * 日历奖励
	 * @author huwei
	 * @param int $id 操作[0信息 1领取 2签到]
	 * @param int $type 奖励位置
	 * @return array array(1/0,array(ErrNo,Data))
	 */
	public function ACalenderAward($id = 0, $type = 1) {
		//操作结果默认为失败0
		$errNo        = T_ErrNo::ERR_ACTION; //失败原因默认
		$data         = array();
		$objPlayer    = $this->objPlayer;
		$cityInfo     = $objPlayer->getCityBase();
		$basecalender = M_Config::getVal('calender_award');
		$cityTask     = M_Task::getCityTask($cityInfo['id']);

		$citycalender = $cityTask['calender_award'];

		if (empty($citycalender) || $citycalender['t'] != date('Ym')) {
			$citycalender['t']     = date('Ym');
			$citycalender['day']   = array();
			$citycalender['award'] = array();
		}

		if ($id == 0) {
			foreach ($basecalender as $k => $val) {
				list($num, $awardId) = $val;

				$awardArr = M_Award::rateResult($awardId);
				$award    = M_Award::toText($awardArr, true);

				//[0未完成,1已完成未领取,2已领取]
				$flag = isset($citycalender['award'][$k]) ? $citycalender['award'][$k] : 0;

				$row[] = array($num, $award, $flag);
			}

			$data['Year']  = date('Y');
			$data['Month'] = date('m');
			$data['Day']   = date('d');
			$data['Days']  = $citycalender['day'];
			$data['List']  = $row;
			$errNo         = '';
		} else if ($id == 1 && isset($basecalender[$type])) { //领取奖励
			$flag = isset($citycalender['award'][$type]) ? $citycalender['award'][$type] : 0;

			$errNo = T_ErrNo::NO_CALENDER_AWARD;
			if ($flag == 1) {
				list($num, $awardId) = $basecalender[$type];

				$awardArr = M_Award::rateResult($awardId);
				$award    = M_Award::toText($awardArr);
				$bAward   = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task); //奖励结果


				$flag                         = 2;
				$citycalender['award'][$type] = $flag;
				$upInfo                       = array(
					'calender_award' => json_encode($citycalender),
				);
				M_Task::updateCityTask($cityInfo['id'], $upInfo);

				$data  = array(
					'Flag'  => $flag,
					'Award' => $award,
				);
				$errNo = '';
			}

		} else { //签到
			$day = date('d');
			if (!in_array($day, $citycalender['day'])) {
				$citycalender['day'][] = $day;
				$totalDay              = count($citycalender['day']);

				foreach ($basecalender as $k => $val) {
					list($num, $awardId) = $val;
					if ($totalDay >= $num) {
						if (!isset($citycalender['award'][$k])) {
							$citycalender['award'][$k] = 1;
						}
					}
				}
				$upInfo = array(
					'calender_award' => json_encode($citycalender),
				);

				M_Task::updateCityTask($cityInfo['id'], $upInfo);
				$errNo = '';
			}
		}

		return B_Common::result($errNo, $data);

	}

	/**
	 * 指引导航
	 */
	public function AQuestNav() {

	}

	/**
	 * 指引奖励领取
	 */
	public function AQuestFinish($questId) {
		$errNo     = T_ErrNo::ERR_ACTION;
		$data      = array();
		$objPlayer = $this->objPlayer;

		$list = $objPlayer->Quest()->get();
		if (!isset($list[$questId])) {
			return B_Common::result(T_ErrNo::QUEST_NOT_EXIST);
		} else if ($list[$questId][2] != 1) {
			return B_Common::result(T_ErrNo::QUEST_NOT_COMPLETE);
		}

		$bUp   = $objPlayer->Quest()->finish($questId);

		if ($bUp) {
			$qInfo = M_Base::questInfo($questId);
			//获取奖励
			$awardArr = M_Award::rateResult($qInfo['award_id']);
			$award    = M_Award::toText($awardArr);
			$bAward   = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task); //奖励结果
			$objPlayer->save();
			$errNo = '';

			$data['Award'] = $award;
		}

		return B_Common::result($errNo, $data);

	}

	/**
	 * 指引列表
	 */
	public function AQuestList() {

		$data      = array();
		$objPlayer = $this->objPlayer;

		$list = $objPlayer->Quest()->get();

		foreach ($list as $qId => $qVal) {
			$qInfo = M_Base::questInfo($qId);
			if (!empty($qInfo['id'])) {
				//array(完成条件, 已完成数量, 是否完成)
				list($ruleArr, $tmpVal, $isOk) = $qVal;

				$awardArr = M_Award::allResult($qInfo['award_id']);
				$award    = M_Award::toText($awardArr, true);

				$data[] = array(
					'Id'       => $qId,
					'Type'     => $qInfo['type'],
					'Name'     => $qInfo['name'],
					'Intro'    => $qInfo['desc'],
					'Desc'     => $qInfo['guide'],
					'DescArgs' => array($tmpVal),
					'IsOk'     => $isOk,
					'Event'    => json_decode($qInfo['event'], 1),
					'Award'    => $award,
				);
			} else {
				Logger::error(array(__METHOD__, 'err quest id', $qId));
			}
		}
		$objPlayer->save();
		return B_Common::result('', $data);
	}
}

?>