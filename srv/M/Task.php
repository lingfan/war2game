<?php

class M_Task {
	/** 新手任务 */
	const TYPE_COMMON = 1;
	/** 日常任务 */
	const TYPE_DAILY = 2;
	/** 活动任务 */
	const TYPE_ACTIVE = 3;
	/** 任务类型 */
	static $type = array(
		'1' => '新手任务',
		'2' => '日常任务',
		'3' => '活动任务',
	);

	/** 任务领奖不含等级概念 */
	const NEED_LV_NO = 0;
	/** 任务领奖含有等级概念 */
	const NEED_LV_YES = 1;

	/** 此任务未完成未领奖 */
	const NEW_DEFAULT = 1;
	/** 此任务已完成未领奖 */
	const NEW_COMPLETE = 2;
	/** 此任务已完成已领奖 */
	const NEW_AWARD = 3;

	/** 根据通关章节计算阶段的任务ID列表 */
	static $needPassCrossing = array(30, 35);
	/** 根据城市等级计算阶段的任务ID列表 */
	static $needCityLevel = array(31);

	/** 日常任务分阶段奖励数据(任务ID对应各阶段数据) */
	static $dailyTaskAward = array(
		30 => array( //挑战战役
			1 => array(
				'atkfb_num' => 2,
				'gold'      => 100000,
				'oil'       => 100000,
				'food'      => 100000
			),
			2 => array(
				'atkfb_num' => 4,
				'gold'      => 200000,
				'oil'       => 200000,
				'food'      => 200000
			),
			3 => array(
				'atkfb_num' => 6,
				'gold'      => 400000,
				'oil'       => 400000,
				'food'      => 400000
			),
			4 => array(
				'atkfb_num' => 8,
				'gold'      => 600000,
				'oil'       => 600000,
				'food'      => 600000
			),
			5 => array(
				'atkfb_num' => 10,
				'gold'      => 800000,
				'oil'       => 800000,
				'food'      => 800000
			),
			6 => array(
				'atkfb_num' => 12,
				'gold'      => 1000000,
				'oil'       => 1000000,
				'food'      => 1000000
			),
			6 => array(
				'atkfb_num' => 12,
				'gold'      => 1000000,
				'oil'       => 1000000,
				'food'      => 1000000
			),
			7 => array(
				'atkfb_num' => 12,
				'gold'      => 1000000,
				'oil'       => 1000000,
				'food'      => 1000000
			),
			8 => array(
				'atkfb_num' => 12,
				'gold'      => 1000000,
				'oil'       => 1000000,
				'food'      => 1000000
			),
		),
		31 => array( //加速冷却
			1 => array('coupon' => 5),
			2 => array('coupon' => 10),
			3 => array('coupon' => 15),
			4 => array('coupon' => 20),
			5 => array('coupon' => 25),
		),
		35 => array( //装备强化
			1 => array('props' => array(26 => 1)), //从第1章第1战役第1关即可获得(初级强化石)
			2 => array('props' => array(26 => 1)),
			3 => array('props' => array(27 => 1)), //从第4章第1战役第1关即可获得(中级强化石)
			4 => array('props' => array(27 => 1)),
			5 => array('props' => array(28 => 1)), //从第8章第1战役第1关即可获得(高级强化石)
			6 => array('props' => array(28 => 1)),
		),
	);

	//首次操作数据标记
	const ONCE_PAY = 1; //首次充值
	const ONCE_LOGIN = 2; //首次登陆

	/**
	 * 获取当前代码版本对应的屏蔽任务ID数组
	 * @return array 任务ID数组
	 */
	static public function getShieldId() {
		$ret = array(13, 26, 37);
		if ('tw' == ETC_NO) {
			$ret = array(13, 26, 32, 36, 37);
		}

		return $ret;
	}

	/**
	 * 根据任务ID获取任务基础数据
	 * @author chenhui    on 20110428
	 * @param int $taskId 任务ID
	 * @return array 科技基础数据(一维数组)
	 */
	static public function baseInfo($taskId) {
		$apcKey = T_Key::BASE_TASK . '_' . $taskId;
		$info   = B_Cache_APC::get($apcKey);
		if (empty($info)) {
			$info = B_DB::instance('BaseTask')->get($taskId);
			APC::set($apcKey, $info);
		}
		return $info;
	}

	/**
	 * 根据城市ID获取城市任务信息
	 * @author chenhui on 20110428
	 * @param int $cityId 城市ID
	 * @return array/false 城市任务信息(一维数组)
	 */
	static public function getCityTask($cityId) {
		$cityId = intval($cityId);
		$ret    = false;
		if ($cityId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_TASK, $cityId);
			$ret = $rc->hmget(T_DBField::$cityTaskFields);
			if (empty($ret['city_id'])) {
				$ret = B_DB::instance('CityTask')->getRow($cityId);
				if (!empty($ret)) {
					$rc->hmset($ret, T_App::ONE_DAY);
				}
			}

			$now_date = date('Ymd');
			if ($now_date != $ret['daily_date']) {
				self::updateCityTask($cityId, array('tasks_daily_ok' => json_encode(array()), 'tasks_daily_end' => json_encode(array()), 'daily_date' => $now_date)); //清空
				$ret['tasks_daily_ok']  = json_encode(array());
				$ret['tasks_daily_end'] = json_encode(array());
				$ret['daily_date']      = $now_date;
			}

			$ret['section_pay_once'] = !empty($ret['section_pay_once']) ? json_decode($ret['section_pay_once'], true) : array();
			$ret['section_pay_add']  = !empty($ret['section_pay_add']) ? json_decode($ret['section_pay_add'], true) : array();
			$ret['calender_award']   = !empty($ret['calender_award']) ? json_decode($ret['calender_award'], true) : array();
		}
		return $ret;
	}

	/**
	 * 获取城市全部任务状态
	 * @author chenhui on 20110905
	 * @param int $cityId 城市ID
	 * @return array 任务状态
	 */
	static public function getCityTaskStatus($cityId) {
		$allBaseInfo  = M_Base::taskAll();
		$cityTaskInfo = self::getCityTask($cityId);

		$arrNewOK  = !empty($cityTaskInfo['tasks_ok']) ? json_decode($cityTaskInfo['tasks_ok'], true) : array(); //已完成新手任务列表
		$arrNewEnd = !empty($cityTaskInfo['tasks_end']) ? json_decode($cityTaskInfo['tasks_end'], true) : array(); //已领奖新手任务列表

		$arrDailyOK  = !empty($cityTaskInfo['tasks_daily_ok']) ? json_decode($cityTaskInfo['tasks_daily_ok'], true) : array(); //已完成日常任务列表
		$arrDailyEnd = !empty($cityTaskInfo['tasks_daily_end']) ? json_decode($cityTaskInfo['tasks_daily_end'], true) : array(); //已领奖日常任务列表

		$arrStatus = array();
		if (!empty($allBaseInfo)) {
			foreach ($allBaseInfo as $tId => $baseInfo) {
				$arr = array($tId, self::NEW_DEFAULT, 0, 1); //任务ID、状态、完成次数、所处等级 [初始值]

				if (in_array($tId, $arrNewEnd) || in_array($tId, $arrDailyEnd)) {
					$arr[1] = self::NEW_AWARD;
				} else if (in_array($tId, $arrNewOK) || in_array($tId, $arrDailyOK)) {
					$arr[1] = self::NEW_COMPLETE;
				}

				if (self::TYPE_DAILY == $baseInfo['type']) {
					$arr[2] = self::getCompleteTimes($cityId, $tId);
				}

				if (isset(M_Task::$dailyTaskAward[$tId])) {
					$level = 1;
					if (in_array($tId, M_Task::$needPassCrossing)) {
						$level = M_Task::getCrossingLevel($cityId);
					} else if (in_array($tId, M_Task::$needCityLevel)) {
						$cityInfo = M_City::getInfo($cityId);
						$level    = $cityInfo['level'];
					}

					$tmpAward       = M_Task::$dailyTaskAward[$tId];
					$maxTmpAwardKey = max(array_keys($tmpAward));
					$level          = min($maxTmpAwardKey, $level);

					$arr[3] = $level;
				}

				$arrStatus[] = $arr;
			}
		}
		return $arrStatus;
	}

	/**
	 * 根据城市ID更新城市任务信息
	 * @author chenhui on 20110726
	 * @param int $cityId 城市ID
	 * @param array $upInfo 要更新的键值对数组
	 * @return array/false
	 */
	static public function updateCityTask($cityId, $upInfo, $upDB = true) {
		$ret = false;
		if (!empty($cityId) && is_array($upInfo) && !empty($upInfo)) {
			$info = array();
			foreach ($upInfo as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$cityTaskFields)) {
					$info[$key] = $val;
				}
			}
			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_TASK, $cityId);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$upDB && M_CacheToDB::addQueue(T_Key::CITY_TASK . ':' . $cityId);
				} else {
					$msg = array(__METHOD__, 'Update Task Info Fail', func_get_args());
					Logger::error($msg);
				}
			}
		}

		return $ret ? $info : false;
	}

	/**
	 * 添加某任务至某城市已完成数据中
	 * @author chenhui on 20110727
	 * @param int $cityId 城市ID
	 * @param int $taskId 任务ID
	 * @return bool
	 */
	static public function addCityTaskOK($cityId, $taskId, $taskType = self::TYPE_COMMON) {
		$ret         = true;
		$arrShieldId = M_Task::getShieldId(); //屏蔽任务ID
		if (!in_array($taskId, $arrShieldId)) {
			$cityTask = self::getCityTask($cityId);
			$which_ok = 'tasks_ok';
			(self::TYPE_DAILY == $taskType) && $which_ok = 'tasks_daily_ok';

			$arrTaskOK = !empty($cityTask[$which_ok]) ? json_decode($cityTask[$which_ok], true) : array();
			if (!in_array($taskId, $arrTaskOK)) {
				$arrTaskOK[] = $taskId;
				$ret         = self::updateCityTask($cityId, array($which_ok => json_encode($arrTaskOK)));
				if ($ret) {
					$nowTimes = M_Task::getCompleteTimes($cityId, $taskId);
					M_Sync::addQueue($cityId, M_Sync::KEY_TASK, array($taskId => array(self::NEW_COMPLETE, $nowTimes))); //同步任务状态至前端
				}
				if ($taskType == self::TYPE_DAILY) {
					if (self::IsTaskOK($cityId)) {
						$objPlayer = new O_Player($cityId);
						$objPlayer->Liveness()->check(M_Liveness::GET_POINT_DAILY_TASK);
						$objPlayer->save();
					}
				}
			}

		}

		return $ret;
	}

	/**
	 * 判断日常任务是否全部完成
	 * @author duhuihui on 20130403
	 * @param int $cityId 城市ID
	 * @param int $taskId 任务ID
	 * @return bool
	 */
	static public function IsTaskOK($cityId) {
		$ret       = false;
		$cityTask  = self::getCityTask($cityId);
		$arrTaskOK = !empty($cityTask['tasks_daily_ok']) ? json_decode($cityTask['tasks_daily_ok'], true) : array();
		if (!empty($arrTaskOK)) {
			$allBaseInfo = M_Base::taskAll();
			$arr         = array();
			foreach ($allBaseInfo as $BaseInfo) {
				if ($BaseInfo['type'] == self::TYPE_DAILY) {
					$arr[] = $BaseInfo['id'];
				}
			}
			if (count($arrTaskOK) == count($arr)) {
				$ret = true;
			}
		}
		return $ret;
	}

	/**
	 * 添加某任务至某城市已领奖数据中
	 * @author chenhui on 20110728
	 * @param int $cityId 城市ID
	 * @param int $taskId 任务ID
	 * @param int $taskType 任务类型
	 * @return bool
	 */
	static public function addCityTaskEnd($cityId, $taskId, $taskType = self::TYPE_COMMON) {
		$ret         = true;
		$arrShieldId = M_Task::getShieldId(); //屏蔽任务ID
		if (!in_array($taskId, $arrShieldId)) {
			$cityTask  = self::getCityTask($cityId);
			$which_ok  = 'tasks_ok';
			$which_end = 'tasks_end';
			if (self::TYPE_DAILY == $taskType) {
				$which_ok  = 'tasks_daily_ok';
				$which_end = 'tasks_daily_end';
			}

			$arrTaskOK  = !empty($cityTask[$which_ok]) ? json_decode($cityTask[$which_ok], true) : array();
			$arrTaskEnd = !empty($cityTask[$which_end]) ? json_decode($cityTask[$which_end], true) : array();
			if (in_array($taskId, $arrTaskOK)) {
				if (!in_array($taskId, $arrTaskEnd)) {
					$arrTaskEnd[] = $taskId;
					$ret          = self::updateCityTask($cityId, array($which_end => json_encode($arrTaskEnd)));
					if ($ret) {
						$nowTimes = self::getCompleteTimes($cityId, $taskId);
						M_Sync::addQueue($cityId, M_Sync::KEY_TASK, array($taskId => array(self::NEW_AWARD, $nowTimes))); //同步任务状态至前端
					}
				}
			}
		}

		return $ret;
	}

	/**
	 * 设置最近任务领奖的缓存
	 * @author chenhui on 20110921
	 * @param int $cityId 城市ID
	 * @param int $taskId 任务ID
	 * @return bool
	 */
	static public function setLastTaskCache($cityId, $taskId) {
		$ret = false;
		if (!empty($cityId) && !empty($taskId)) {
			$rc  = new B_Cache_RC(T_Key::CITY_TASK_FIN, $cityId);
			$ret = $rc->set($taskId, T_App::ONE_HOUR);
		}
		return $ret;
	}

	/**
	 * 获取最近任务领奖的缓存
	 * @author chenhui on 20110921
	 * @param int $cityId 城市ID
	 * @return int 任务ID
	 */
	static public function getLastTaskCache($cityId) {
		$ret = '';
		if (!empty($cityId)) {
			$rc = new B_Cache_RC(T_Key::CITY_TASK_FIN, $cityId);
			if ($rc->exists()) {
				$ret = $rc->get();
			}
		}
		return $ret;
	}


	/**
	 * 获取某任务单项进行次数
	 * @author chenhui on 20120206
	 * @param int $cityId 城市ID
	 * @param int $taskId 任务ID
	 * @return int 次数
	 */
	static public function getCompleteTimes($cityId, $taskId) {
		$ret = 0;
		if ($cityId > 0 && $taskId > 0) {
			$hKey = strval($taskId); //redis的hashkey
			$rc   = new B_Cache_RC(T_Key::TASK_DAILY_TIMES, date('Ymd') . $cityId);
			$ret  = intval($rc->hget($hKey));
		}
		return $ret;
	}

	/**
	 * 计算某城市通关任务当前阶段
	 * @author chenhui on 20120206
	 * @param int $cityId 城市ID
	 * @return int 阶段号
	 */
	static public function getCrossingLevel($cityId) {
		$ret = 1;
		if ($cityId > 0) {
			$cityInfo   = M_City::getInfo($cityId);
			$last_fb_no = $cityInfo['last_fb_no'];
			if ($last_fb_no > 0) {
				$ret = ceil(floor($last_fb_no / 10000) / 2);
			}
		}
		return $ret;
	}
	/*********日常任务 处理结束*********************/


	/**********道具模块管理后台所需接口*******************/
	/** 删除任务 基础 数据 缓存 */
	static public function delTaskCache() {
		APC::del(T_Key::BASE_TASK); //删除缓存
	}

	/**
	 * 是否使用过新手卡
	 * @author huwei
	 * @param int $cityId
	 */
	static public function isUseNewbeCard($cityId) {
		$ret = false;
		$rc  = new B_Cache_RC(T_Key::NEWBE_CARD, $cityId);
		if (!$rc->exists()) {
			$tmp = array(
				'props_id' => T_App::NEWBE_CARD_PROPS_ID,
				'city_id'  => $cityId,
			);
			$row = M_Card::getCode($tmp);
			if ($row) {
				$rc->set(true, T_App::ONE_DAY);
				$ret = true;
			}
		} else {
			$ret = $rc->get();
		}

		return $ret;
	}

	/**
	 *
	 * @param unknown_type $cityId
	 * @return int
	 */
	static public function getLoginDailyAward($cityId) {
		$rc  = new B_Cache_RC(T_Key::LOGIN_DAILY_TMP_KEY, $cityId);
		$ret = $rc->jsonget();
		return $ret;
	}

	/**
	 *
	 * @param int $cityId
	 * @param int $day
	 * @return bool
	 */
	static public function setLoginDailyAward($cityId, $day) {
		$rc  = new B_Cache_RC(T_Key::LOGIN_DAILY_TMP_KEY, $cityId);
		$arr = $rc->jsonget();
		if (!empty($arr[$day])) {
			$arr[$day] = $arr[$day];
		} else {
			$arr[$day] = 1;
		}
		$ret = $rc->jsonset($arr, T_App::ONE_WEEK);
		return $ret;

	}

	/**
	 *更新数据表继续学院活动的完成状态
	 * @param int $cityId
	 * @param int $key
	 * @return
	 */
	static public function active($cityId, $key) {
		$ret = false;
		if (!empty($cityId) && !empty($key)) {
			$taskinfo   = M_Task::getCityTask($cityId); //获取城市信息
			$dailyAward = M_Config::getVal('active_award'); //得到config的内容
			$startDay   = $dailyAward['start']; //学院奖励起始时间
			$endDay     = $dailyAward['end']; //学院奖励截止时间
			$d1         = strtotime($startDay);
			$d2         = strtotime($endDay);
			$now        = time();
			if (empty($taskinfo['active_filed'])) //没有值
			{
				$array_active_filed['start'] = $startDay;
				$array_active_filed['end']   = $endDay;
				$array_active_filed['list']  = array();
				$array_active_filed['type']  = 1;
			} else {
				$array_active_filed = json_decode($taskinfo['active_filed'], true); //得到数组
				if (strtotime($array_active_filed['start']) != $d1 || strtotime($array_active_filed['end']) != $d2) //不在活动期间，将值清空
				{
					$array_active_filed['start'] = $startDay;
					$array_active_filed['end']   = $endDay;
					$array_active_filed['list']  = array();
					$array_active_filed['type']  = 1;
				}
			}

			if ($now >= $d1 && $now <= $d2) //在活动期间并且键值$key的值存在
			{
				if (!isset($array_active_filed['list'][$key])) {
					$array_active_filed['list'][$key] = 1;
					$ret                              = M_Task::updateCityTask($cityId, array('active_filed' => json_encode($array_active_filed)));
					$ret && M_Sync::addQueue($cityId, M_Sync::KEY_ACTIVE, array($key => 1));
				}
			}
		}
		return $ret;
	}

	static private function _holdnpcactivetype2($cityId, $dailyAward, $array_active_filed) {
		$IsOpen     = 3;
		$sum        = 0;
		$awardField = !empty($array_active_filed['list']) ? $array_active_filed['list'] : array();
		if (!empty($awardField)) {
			foreach ($awardField as $value) {
				if ($value == 2) //已完成
				{
					$sum += 1;
				} else if ($value == 1) {
					$IsOpen = 4;
				}
			}

			$taskNum = count($dailyAward['list']); //学院活动数
			if ($sum == $taskNum) {
				$rc = new B_Cache_RC(T_Key::CITY_OCCOUPIED_TIMES, $cityId);
				$rc->delete();
				$IsOpen                     = 5;
				$array_active_filed['type'] = 3;
				$array_active_filed['list'] = array();
				M_Task::updateCityTask($cityId, array('active_filed' => json_encode($array_active_filed)));
			}
		}
		return $IsOpen;

	}

	static private function _holdnpcactivetype3($cityId, $dailyAward, $array_active_filed) {
		$IsOpen     = 5;
		$awardField = !empty($array_active_filed['list']) ? $array_active_filed['list'] : array();
		if (!empty($awardField)) {
			if ($awardField['award'] == 2) //已完成
			{
				$IsOpen = 7;
				$rc     = new B_Cache_RC(T_Key::CITY_OCCOUPIED_TIMES, $cityId);
				$rc->delete();
				$array_active_filed['type'] = 4;
				$array_active_filed['list'] = array();
				M_Task::updateCityTask($cityId, array('active_filed' => json_encode($array_active_filed)));
			} else if ($awardField['award'] == 1) {
				$IsOpen = 6;
			}
		}
		return $IsOpen;
	}

	static private function _holdnpcactivetype4($cityId, $dailyAward, $array_active_filed) {
		$IsOpen     = 7;
		$awardField = !empty($array_active_filed['list']) ? $array_active_filed['list'] : array();
		if (!empty($awardField)) {
			if ($awardField['award'] == 2) //已完成
			{
				$IsOpen = 9;
				$rc     = new B_Cache_RC(T_Key::CITY_OCCOUPIED_TIMES, $cityId);
				$rc->delete();
				$array_active_filed['type'] = 5;
				$array_active_filed['list'] = array();
				M_Task::updateCityTask($cityId, array('active_filed' => json_encode($array_active_filed)));
			} else if ($awardField['award'] == 1) {
				$IsOpen = 8;
			}
		}
		return $IsOpen;
	}

	static private function _holdnpcactivetype5($cityId, $dailyAward, $array_active_filed) {
		$IsOpen     = 9;
		$awardField = !empty($array_active_filed['list']) ? $array_active_filed['list'] : array();
		if (!empty($awardField)) {
			if ($awardField['award'] == 2) //已完成
			{
				$IsOpen = 0;
				$rc     = new B_Cache_RC(T_Key::CITY_OCCOUPIED_TIMES, $cityId);
				$rc->delete();
			} else if ($awardField['award'] == 1) {
				$IsOpen = 10;
			}
		}
		return $IsOpen;
	}

	static public function getHoldNpcActiveStaus($cityId, $dailyAward) {
		$now        = time();
		$awardField = array();
		$IsOpen     = 0;
		$taskinfo   = M_Task::getCityTask($cityId); //获取城市信息
		$startDay   = $dailyAward['start']; //学院奖励起始时间
		$endDay     = $dailyAward['end']; //学院奖励截止时间
		if (!empty($taskinfo['active_filed'])) {
			$array_active_filed = json_decode($taskinfo['active_filed'], true); //得到数组
			if (isset($array_active_filed['start']) &&
				isset($array_active_filed['end']) &&
				(strtotime($array_active_filed['start']) != strtotime($startDay) ||
					strtotime($array_active_filed['end']) != strtotime($endDay))
			) //不在活动期间，将值清空
			{
				$array_active_filed['start'] = $startDay;
				$array_active_filed['end']   = $endDay;
				$array_active_filed['list']  = array();
				$array_active_filed['type']  = 1;
				M_Task::updateCityTask($cityId, array('active_filed' => json_encode($array_active_filed)));
			}
		}

		if ($now >= strtotime($startDay) &&
			$now <= strtotime($endDay)
		) //在活动期间
		{
			$IsOpen = 1;
			if (!empty($taskinfo['active_filed'])) {
				$array_active_filed = json_decode($taskinfo['active_filed'], true);
				$awardField         = !empty($array_active_filed['list']) ? $array_active_filed['list'] : array();
				if (isset($array_active_filed['type']) && $array_active_filed['type'] == 1) {
					$IsOpen = 1;
					if (!empty($awardField)) {
						$sum = 0;
						foreach ($awardField as $value) {
							if ($value == 2) //已完成
							{
								$sum += 1;
							} else if ($value == 1) {
								$IsOpen = 2;
							}
						}

						$taskNum = count($dailyAward['list']); //学院活动数
						if ($sum == $taskNum) {
							$IsOpen                     = 3;
							$array_active_filed['type'] = 2;
							$array_active_filed['list'] = array();
							M_Task::updateCityTask($cityId, array('active_filed' => json_encode($array_active_filed)));
						} else {
							return array($IsOpen, $awardField);
						}
					}
				}

				if (isset($array_active_filed['type'])) {
					$awardField = !empty($array_active_filed['list']) ? $array_active_filed['list'] : array();
					switch ($array_active_filed['type']) {
						case 2:
							$IsOpen = self::_holdnpcactivetype2($cityId, $dailyAward, $array_active_filed);
							break;
						case 3:
							$IsOpen = self::_holdnpcactivetype3($cityId, $dailyAward, $array_active_filed);
							break;
						case 4:
							$IsOpen = self::_holdnpcactivetype4($cityId, $dailyAward, $array_active_filed);
							berak;
						case 5:
							$IsOpen = self::_holdnpcactivetype5($cityId, $dailyAward, $array_active_filed);
							break;
					}

				}
			}
		}
		return array($IsOpen, $awardField);
	}

	static public function getQuestIdsByPrevId($id) {
		$ret = array();
		if ($id >= 0) {
			$list = B_DB::instance('BaseQuest')->getsBy(array('prev_id'=>$id), array('id'=>'desc'));
			foreach($list as $val) {
				$ret[] = $val['id'];
			}
		}

		return $ret;

	}

}

?>