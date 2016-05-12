<?php

/**
 * 据点
 */
class M_Campaign {
	static $campOpenWeek = array(
		0 => 1,
		1 => 2,
		2 => 4,
		3 => 8,
		4 => 16,
		5 => 32,
		6 => 64,
	);

	/** 据点基地编号  */
	static $CampaignBase = array(11, 12, 13, 14, 15, 16, 21, 22, 23, 31, 32, 41);

	/** 资源类 */
	const TYPE_RES = 1;
	/** 军事类 */
	const TYPE_WAR = 2;
	/** 同一个据点战斗最大排队数 */
	const MAX_QUEUE_NUM = 5;
	/** 同一个据点容纳部队数 */
	const MAX_HOLD_NUM = 3;
	/** 据点最大巡逻次数 */
	const MAX_EXPLORE_NUM = 2;
	/** 提前开始驻军时间[小时] */
	const START_HOLD_TIME = 3;
	/** 据点行军[分钟] */
	const MARCH_TIME = 2;

	//资源据点类型
	const CAMP_TYPE_RES = 1;
	//军事据点类型
	const CAMP_TYPE_MIL = 2;

	/**
	 * 获取据点信息
	 * @author huwei
	 * @param int $campId
	 */
	static public function getInfo($campId) {
		$ret = false;
		if (!empty($campId)) {
			$baseInfo = M_Base::campaignAll();
			if (isset($baseInfo[$campId])) {
				$rc  = new B_Cache_RC(T_Key::CAMPAIGN_INFO, $campId);
				$ret = $rc->hgetall();
				if (empty($ret['id'])) {
					$info = B_DB::instance('Campaign')->getRow($campId);
					$bSet = $rc->hmset($info);

					if ($bSet) {
						$ret = $info;
					}
				}
			}
		}
		return $ret;
	}

	/**
	 * 更新据点信息
	 * @author huwei
	 * @param int $id
	 * @param array $upInfo
	 * @param bool $upDB 是否直接更新
	 */
	static public function setInfo($id, $upInfo, $upDB = true) {
		$ret = false;
		$id  = intval($id);
		if ($id > 0) {
			$arr = array();
			foreach ($upInfo as $k => $v) {
				if (in_array($k, T_DBField::$campaignFields)) {
					$arr[$k] = $v;
				}
			}

			if (!empty($arr)) {
				$rc  = new B_Cache_RC(T_Key::CAMPAIGN_INFO, $id);
				$ret = $rc->hmset($arr);
				if ($ret) {
					$upDB && M_CacheToDB::addQueue(T_Key::CAMPAIGN_INFO . ':' . $id);
				} else {
					$msg = array(__METHOD__, 'Update Campaign Info Fail', func_get_args());
					Logger::error($msg);
				}
			}
		}
		return $ret;

	}

	/**
	 * 更新巡逻次数
	 * @author huwei
	 * @param int $campId 据点
	 * @param int $cityId
	 * @return bool
	 */
	static public function setExploreTimes($campId, $unionId, $cityId, $num) {
		$ret     = false;
		$campId  = intval($campId);
		$cityId  = intval($cityId);
		$num     = intval($num);
		$nowDate = date('Ymd');

		if ($campId > 0 && $cityId > 0 && $unionId > 0 && $num > 0) {
			$rc  = new B_Cache_RC(T_Key::CAMPAIGN_TIMES, $campId . ':' . $unionId);
			$str = $nowDate . '_' . $num;
			$ret = $rc->hmset(array($cityId => $str), T_App::ONE_DAY);

		}
		return $ret;
	}

	/**
	 * 清除巡逻次数
	 * @author huwei
	 * @param int $campId 据点
	 * @param int $cityId
	 */
	static public function cleanExploreTimes($campId, $unionId) {
		$ret = false;
		if ($campId > 0 && $unionId > 0) {
			$rc  = new B_Cache_RC(T_Key::CAMPAIGN_TIMES, $campId . ':' . $unionId);
			$ret = $rc->delete();
		}
		return $ret;
	}

	/**
	 * 获取巡逻次数
	 * @author huwei
	 * @param int $campId 据点
	 * @param int $cityId
	 * @param int $maxProbeTimes
	 */
	static public function getExploreTimes($campId, $unionId, $cityId) {
		$times   = 0;
		$campId  = intval($campId);
		$cityId  = intval($cityId);
		$nowDate = date('Ymd');
		if ($campId > 0 && $cityId > 0) {
			$rc  = new B_Cache_RC(T_Key::CAMPAIGN_TIMES, $campId . ':' . $unionId);
			$str = $rc->hget($cityId);
			if (empty($str)) {
				$times = 0;
			} else {
				list($d, $times) = explode('_', $str);
				if ($d != $nowDate) {
					$times = 0;
				}
			}
		}
		return $times;
	}

	/**
	 * 删除老基地中的行军id
	 * @param array $campInfo
	 * @param string $noField
	 * @param int $marchId
	 */
	static public function delCampainHoldId($campInfo, $noField, $marchId) {
		//删除老基地中的行军id
		$ret = false;
		if (!empty($campInfo) &&
			!empty($marchId) &&
			!empty($noField)
		) {
			list($begUnionId, $begMarchIds) = json_decode($campInfo[$noField], true);
			$idx = array_search($marchId, $begMarchIds);
			if ($idx !== false) {
				unset($begMarchIds[$idx]);
				$newArr   = array_values($begMarchIds);
				$newArr[] = 0;

				$upInfo = array(
					$noField => json_encode(array($begUnionId, $newArr))
				);

				$ret = M_Campaign::setInfo($campInfo['id'], $upInfo);
				if (!$ret) {
					Logger::error(array(__METHOD__, 'Update Campaign Fail', $campInfo['id'], $upInfo));
				}
			} else { //不存在 标记删除成功
				$ret = true;
			}
		}
		return $ret;
	}

	/**
	 * 是否是据点编号（据点才有"_"符号）
	 * @author huwei
	 * @param string $no
	 */
	static public function isDefLineNo($no) {
		$ret = false;
		if (strstr($no, '_')) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * 获取据点加成
	 * @author huwei at 20120406
	 * @param int $type
	 * @param int $unionId
	 * @return int
	 */
	static public function getAddition($type, $unionId) {
		$ret = 0;
		if (!empty($type) && !empty($unionId)) {
			$rc   = new B_Cache_RC(T_Key::CAMP_UNION_EFFECT, $unionId);
			$info = $rc->jsonget();

			if (isset($info[$type])) {
				foreach ($info[$type] as $val) {
					list($num, $expire) = $val;
					if ($expire > time()) {
						$ret += $num;
					}
				}

			}
		}
		return $ret;
	}

	/**
	 * 设置据点加成
	 * @author huwei at 20120406
	 * @param int $campId
	 * @param int $type
	 * @param int $unionId
	 * @param int $addNum
	 * @return int
	 */
	static public function setAddition($campId, $type, $unionId, $addNum, $newExpireTime) {

		if (!empty($unionId) && !empty($type) && !empty($addNum)) {
			$rc   = new B_Cache_RC(T_Key::CAMP_UNION_EFFECT, $unionId);
			$info = $rc->jsonget();
			$arr  = array();

			if (isset($info[$type])) {
				foreach ($info[$type] as $k => $val) {
					list($num, $expire) = $val;
					if ($expire > time()) {
						$arr[$type][$k] = $val;
					}
				}
			}

			$arr[$type][$campId] = array($addNum, $newExpireTime);
			$ret                 = $rc->jsonset($arr);
		}
		return $ret;
	}

	/**
	 * 设置据点战斗结束
	 * @author huwei at 20120406
	 * @param int $campId
	 * @return bool
	 */
	static public function setCampEnd($campId) {
		$rc = new B_Cache_RC(T_Key::CAMP_END, $campId);
		return $rc->set(1, T_App::ONE_HOUR);
	}

	/**
	 * 获取据点战斗是否结束
	 * @author huwei at 20120406
	 * @param int $campId
	 * @return bool
	 */
	static public function getCampEnd($campId) {
		$rc = new B_Cache_RC(T_Key::CAMP_END, $campId);
		return $rc->get();
	}

	/**
	 * 据点战斗系统守护进程
	 * @author huwei
	 */
	static public function run() {
		$list = M_Base::campaignAll();
		//Logger::debug(array(__METHOD__,$list));
		$now         = time();
		$curWeek     = date('w');
		$curWeekFlag = M_Campaign::$campOpenWeek[$curWeek];

		$tmpRc        = new B_Cache_RC(T_Key::TMP_EXPIRE, 'camp');
		$tmpExpireArr = $tmpRc->jsonget();
		//Logger::debug(array(__METHOD__,$tmpExpireArr));
		foreach ($list as $val) {
			$campId       = $val['id'];
			$sysStartTime = strtotime($val['open_start_time']);
			$sysEndTime   = strtotime($val['open_end_time']);

			$end            = $sysEndTime + T_App::ONE_MINUTE * 30;
			$startRadioTime = $sysStartTime - 5 * T_App::ONE_MINUTE;
			$expireTime     = isset($tmpExpireArr[$campId]) && ($tmpExpireArr[$campId] >= $startRadioTime) ? $tmpExpireArr[$campId] : $startRadioTime;

			$isCurDay = ($curWeekFlag & $val['open_week']) > 0 ? true : false;
			if ($isCurDay && $now > $expireTime && $now < $sysStartTime) //5分钟内
			{
				$tmpExpireArr[$val['id']] = $expireTime + 1 * T_App::ONE_MINUTE;
				//提早5分钟发公告
				$tArr  = explode(':', $val['open_start_time']);
				$title = json_encode(array(T_Lang::CAMP_START_RADIO, $val['title'], $tArr[0], $tArr[1]));
				$msg   = implode("\t", array($title, T_Chat::SYS_RADIO_PRIO, T_Chat::SYS_RADIO_STAY_TIME));
				M_Chat::addWorldMessage(uniqid(), $msg, T_Chat::CHAT_SYS_RADIO);
			}

			$isEnd = false;

			$calcResult = M_Campaign::getCampEnd($campId);
			if (!$calcResult &&
				$isCurDay &&
				$now > $sysEndTime &&
				$now < $end
			) {
				$outTip = "\n============================\n";
				$outTip .= "campID#{$campId}\n";
				$outTip .= "Start to calc:";
				$isEnd = true;
				$has   = array();
				foreach (M_Campaign::$CampaignBase as $no) {
					$pos = M_MapWild::calcWildMapPosNoByXY(T_App::MAP_CAMPAIGN, $campId, $no);

					$mw       = new M_March_Wait($pos);
					$battleId = $mw->getBattleId();
					if ($battleId) {
						$has[] = $battleId;
					}
				}

				$outTip .= "hadbattle:" . json_encode($has) . "\n";

				if (empty($has)) {
					//只执行一次
					M_Campaign::setCampEnd($campId);

					$campInfo = M_Campaign::getInfo($campId);
					$outTip .= "CampInfo:" . json_encode($campInfo) . "\n";
					//撤回所有的部队
					foreach (M_Campaign::$CampaignBase as $no) {
						$noField = 'no_' . $no;
						list($holdUnionId, $holdMarchIds) = json_decode($campInfo[$noField], true);
						foreach ($holdMarchIds as $marchId) {
							//发送消息邮件
							$marchInfo = M_March_Info::get($marchId);
							if ($marchInfo) {
								$content = array(T_Lang::C_CAMP_WAR_END, $val['title']);
								M_Message::sendSysMessage($marchInfo['atk_city_id'], json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content));
								M_March::setMarchBack($marchId);
							}
						}
						$holdUnion[$no] = intval($holdUnionId);
					}

					if ($campInfo['owner_union_id'] > 0) {
						M_Campaign::cleanExploreTimes($campId, $campInfo['owner_union_id']);
						$outTip .= "CleanExploreTimes#campId:{$campId};owner_union_id:{$campInfo['owner_union_id']}" . "\n";
					}

					$outTip .= "HadUnion:" . json_encode($holdUnion) . "\n";
					//判定是否占领属地
					if (!empty($holdUnion[41]) &&
						$holdUnion[41] == $holdUnion[31] &&
						$holdUnion[41] == $holdUnion[32]
					) {
						$ownerUnionId = intval($holdUnion[41]);
						$campId       = strval($campId);
						//据点类型
						$campType = $campId{0};

						//更新据点所有者
						//自动给防守方报名
						$upInfo = array(
							'owner_union_id' => $ownerUnionId,
							'join_union_ids' => json_encode(array($ownerUnionId)),
							'had_award'      => 0,
						);
						//更新据点基地所有者
						foreach (M_Campaign::$CampaignBase as $no) {
							$noField          = 'no_' . $no;
							$upInfo[$noField] = json_encode(array($ownerUnionId, array(0, 0, 0)));
						}
						$bUp = M_Campaign::setInfo($campId, $upInfo, true);

						//据点效果
						list($addNum, $unionCoin) = explode('|', $list[$campId]['effect']);

						$outTip .= "ownerUnionId#{$ownerUnionId};Effect:unionCoin#{$unionCoin};addNum#{$addNum}\n";

						//添加联盟资金
						$unionInfo = M_Union::getInfo($ownerUnionId);

						//发布联盟占领据点通知
						$title = json_encode(array(T_Lang::CAMP_UNION_WIN_RADIO, $unionInfo['name'], $val['title']));
						$msg   = implode("\t", array($title, T_Chat::SYS_RADIO_PRIO, T_Chat::SYS_RADIO_STAY_TIME));
						M_Chat::addWorldMessage(uniqid(), $msg, T_Chat::CHAT_SYS_RADIO);

						if ($unionInfo) {
							$newCoin = $unionInfo['coin'] + $unionCoin;
							$outTip .= "Campaign Add Coin#{$newCoin}\n";
							M_Union::setInfo($ownerUnionId, array('coin' => $unionInfo['coin'] + $unionCoin));
						}
						if (!empty($ownerUnionId)) {
							$UnionMemberIds = M_Union::getUnionMemberIds($ownerUnionId);
							foreach ($UnionMemberIds as $UnionMemberId) {
								$objPlayerUnionMember = new O_Player($UnionMemberId);
								$objPlayerUnionMember->Liveness()->check(M_Liveness::GET_POINT_UNION_OCCUPIED);
								$objPlayerUnionMember->save();
							}
						}

						//添加据点联盟加成效果
						$diffDays = M_Formula::calcCampOpenNextWeek($val['open_week'], $curWeek);
						list($sh, $si, $ss) = explode(':', $val['open_start_time']);
						$newExpireTime = mktime($sh, $si, $ss, date('m'), date('d') + $diffDays, date('Y'));
						$bUp           = M_Campaign::setAddition($campId, $campType, $ownerUnionId, $addNum, $newExpireTime);
						$outTip .= "'M_Campaign::setAddition:{$bUp};campType:{$campType};ownerUnionId:{$ownerUnionId};addNum:{$addNum};newExpireTime:" . date('YmdHis', $newExpireTime) . "\n";
					} else {
						//更新据点基地所有者
						//清空
						$upInfo['had_award']      = 0;
						$upInfo['owner_union_id'] = 0;
						$upInfo['join_union_ids'] = json_encode(array());
						foreach (M_Campaign::$CampaignBase as $no) {
							$noField          = 'no_' . $no;
							$upInfo[$noField] = json_encode(array(0, array(0, 0, 0)));

							//清除掉据点中的行军信息
							$defLinePos = M_MapWild::calcWildMapPosNoByXY(T_App::MAP_CAMPAIGN, $campId, $no);
							$obj_ml     = new M_March_List($defLinePos);
							$obj_ml->clean();
						}
						$bUp = M_Campaign::setInfo($campId, $upInfo, true);
					}
					unset($tmpExpireArr[$campId]);
				}
				$outTip .= "\nEnd@" . $isEnd ? 1 : 0;
				Logger::debug(array(__METHOD__, $outTip));
			}
		}

		$tmpRc->jsonset($tmpExpireArr);
	}

}

?>