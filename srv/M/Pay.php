<?php

/**
 * 充值模块
 */
class M_Pay {

	/**
	 * 付费类型
	 * @param $payType
	 * @return bool
	 */
	static public function isPayType($payType) {
		return in_array($payType, array(T_App::MILPAY, T_App::COUPON));
	}

	/**
	 * 充值接口
	 * @author huwei
	 * @param int $cityId 玩家ID
	 * @param array $orderInfo 订单信息
	 * @param bool $isQQBuyProps QQ是否购买道具
	 * @return bool
	 */
	static public function call($cityId, $orderInfo, $isQQBuyProps = false) {
		$orderInfo['city_id'] = $cityId;
		Logger::pay(json_encode($orderInfo), 'tmp');

		$ret       = false;
		$tmpMilPay = intval($orderInfo['mil_pay']);
		$tmpCoupon = intval($orderInfo['coupon']);

		if ($cityId > 0 && ($tmpMilPay > 0 || $tmpCoupon > 0)) {
			$info = M_City::getInfo($cityId);
			if (!empty($info['id'])) {
				$fieldArr = array();

				$tmpMilPay = max($tmpMilPay, 0);
				$tmpCoupon = max($tmpCoupon, 0);

				$totalMilPay = $tmpMilPay + $info['total_mil_pay'];
				$milPay      = $tmpMilPay + $info['mil_pay']; //新当前军饷
				$coupon      = $tmpCoupon + $info['coupon'];

				$vipLevel = M_Formula::calcVipLevelByTotalMilPay($totalMilPay); //VIP判断

				list($vipLevel, $milPay, $coupon) = self::_qqpay($info, $vipLevel, $milPay, $coupon, $isQQBuyProps);

				$fieldArr['mil_pay'] = $milPay;
				$fieldArr['coupon']  = $coupon;

				$dbInfo = array(
					'city_id'      => $orderInfo['city_id'],
					'server_id'    => $orderInfo['server_id'],
					'username'     => $orderInfo['username'],
					'username_ext' => $orderInfo['username_ext'],
					'consumer_id'  => $orderInfo['consumer_id'],
					'pay_action'   => B_Log_Trade::I_Pay,
					'milpay'       => $tmpMilPay,
					'left_milpay'  => $milPay,
					'coupon'       => $tmpCoupon,
					'left_coupon'  => $coupon,
					'total_milpay' => $totalMilPay,
					'order_no'     => $orderInfo['order_no'],
					'rmb'          => isset($orderInfo['rmb']) ? $orderInfo['rmb'] : ($tmpMilPay / 10), //新增RMB字段
					'data'         => isset($orderInfo['data']) ? $orderInfo['data'] : '',
				);
				//$paySucc = B_Log_Trade::add(B_Log_Trade::TYPE_INCOME, $dbInfo);

				$objPlayer = new O_Player($cityId);
				$objPlayer->City()->mil_pay += $info['mil_pay'];
				$objPlayer->Log()->income(T_App::MILPAY, $info['mil_pay'], 'Pay');
				$objPlayer->save();

				if ($paySucc) {
					//$objPlayer->Quest()->check('pay', array('num'=>$totalMilPay));

					$fieldArr['total_mil_pay'] = $totalMilPay;
					$fieldArr['vip_level']     = $vipLevel;
					$fieldArr['vip_endtime']   = strtotime('+10 year');
					$ret                       = M_City::setCityInfo($cityId, $fieldArr);
					if (!$ret) {
						Logger::error(array(__METHOD__, 'err qq pay', $fieldArr));
					}

					//同步军饷数据
					$msRow = array(
						'milpay'        => $milPay,
						'coupon'        => $coupon,
						'total_mil_pay' => $totalMilPay,
						'vip_level'     => $vipLevel,
						'vip_endtime'   => strtotime('+10 year'),
					);

					if ($vipLevel > $info['vip_level']) {
						$msRow['vip_pack_date'] = 1;
						M_MapWild::syncWildMapBlockCache($info['pos_no']); //刷新此块地图数据
					}

					$ret && M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, $msRow);

					M_Pay::AwardForTime($cityId, $orderInfo['rmb']); //充值奖励

					M_Pay::PayAward($cityId, $orderInfo['rmb']);
				}
			}
		}
		return $paySucc;
	}

	static private function _qqpay($info, $vipLevel, $milPay, $coupon, $isQQBuyProps) {
		//start====qq平台=================
		$qqUserInfo = M_Qq::getQQLive($info['user_id']);
		if (!empty($qqUserInfo)) {
			//qq平台
			$vipParams = array(
				'openid'     => $qqUserInfo['openid'],
				'openkey'    => $qqUserInfo['openkey'],
				'pf'         => $qqUserInfo['pf'],
				'sig'        => $qqUserInfo['sig'],
				// 				'member_vip'	=> 1,
				// 				'blue_vip'		=> 1,
				'yellow_vip' => 1,
				// 				'red_vip'		=> 1,
				// 				'green_vip'		=> 1,
				// 				'pink_vip'		=> 1,
				// 				'superqq'		=> 1,
			);

			$qqVipData = M_Qq::instance()->api('/v3/user/total_vip_info', $vipParams);
			if ($qqVipData['ret'] == 0) {
				$vipLv = 0;
				if ($qqUserInfo['pf'] == '3366') {
					$vipLv = isset($qqVipData['is_blue']) ? $qqVipData['is_blue'] : 0;
				} else {
					$vipLv = isset($qqVipData['is_yellow']) ? $qqVipData['is_yellow'] : 0;
				}

				//Logger::debug(array(__METHOD__, $vipLv, $vipParams, $qqVipData));
				if ($vipLv < 1) {
					//非黄钻充值 不能升游戏VIP等级
					$vipLevel = $info['vip_level'];
				}
			} else {
				Logger::qq(array(__METHOD__, 'Msg' => 'Error is_vip', 'Data' => $qqVipData, 'Params' => $vipParams));
			}

			if ($isQQBuyProps) { //qq购买道具不累计军饷
				$milPay = $info['mil_pay'];
				$coupon = $info['coupon'];
			}
		}
		//end====qq平台=====================

		return array($vipLevel, $milPay, $coupon);
	}

	/**
	 * 获取充值日志
	 * @param int $curPage
	 * @param int $offset
	 * @param array $parms
	 * @param string $sidx
	 * @param string $sord
	 * @return array
	 */
	static public function getPayLog($curPage = 1, $offset = 10, $parms = null, $sidx = 'create_at', $sord = 'DESC') {
		$curPage = max(1, $curPage);
		//$parms = array_merge($parms, array('pay_action'=>$formVals['filter']['pay_action']));
		$totalNum = B_DBStats::totalRows('stats_log_pay', $parms);
		$list     = B_DBStats::apiPageData('stats_log_pay', '*', $curPage, $offset, $parms, $sidx, $sord);
		if (!empty($list) && is_array($list)) {
			foreach ($list as $key => $val) {
				if (!empty($val['username'])) { //兼容老板的city_id做索引
					$arr      = M_User::getInfoByUsername($val['username']);
					$cityId   = M_City::getCityIdByUserId($arr['id']);
					$cityInfo = M_City::getInfo($cityId);
					if ($cityInfo) {
						$list[$key]['city_id']       = $cityId;
						$list[$key]['nickname']      = isset($cityInfo['nickname']) ? $cityInfo['nickname'] : '';
						$list[$key]['total_mil_pay'] = isset($cityInfo['total_mil_pay']) ? $cityInfo['total_mil_pay'] : 0;
						$list[$key]['vip_level']     = isset($cityInfo['vip_level']) ? $cityInfo['vip_level'] : 0;
					}
				}
			}

		}

		$totalRmb  = B_DBStats::getColumnsSum('stats_log_pay', 'rmb', $parms);
		$totalUser = B_DB::instance('StatsLogPay')->getTotalUser($parms);

		$ret['total']     = $totalNum;
		$ret['page']      = $curPage;
		$ret['totalPage'] = $totalNum % $offset == 0 ? $totalNum / $offset : intval($totalNum / $offset) + 1;
		$ret['rows']      = $offset;
		$ret['list']      = $list;
		$ret['totalRmb']  = $totalRmb;
		$ret['totalUser'] = $totalUser;
		return $ret;
	}

	static public function getIncomeLog($curPage = 1, $offset = 10, $parms = null, $sidx = 'id', $sord = 'DESC') {
		$curPage = max(1, $curPage);
		//$parms = array_merge($parms, array('pay_action'=>$formVals['filter']['pay_action']));
		$totalNum = B_DBStats::totalRows('stats_log_income', $parms);
		$list     = B_DBStats::apiPageData('stats_log_income', '*', $curPage, $offset, $parms, $sidx, $sord);
		if (!empty($list) && is_array($list)) {
			foreach ($list as $key => $val) {
				if ($val['city_id']) {
					//Logger::debug('3:'.$val['city_id']);
					$cityInfo = M_City::getInfo($val['city_id']);
					if ($cityInfo) {
						$list[$key]['nickname']      = isset($cityInfo['nickname']) ? $cityInfo['nickname'] : '';
						$list[$key]['total_mil_pay'] = isset($cityInfo['total_mil_pay']) ? $cityInfo['total_mil_pay'] : 0;
						$list[$key]['vip_level']     = isset($cityInfo['vip_level']) ? $cityInfo['vip_level'] : 0;
					}
				}
			}

		}

		$totalRmb  = 0;
		$totalUser = B_DB::instance('StatsLogIncome')->getTotalUser($parms);

		$ret['total']     = $totalNum;
		$ret['page']      = $curPage;
		$ret['totalPage'] = $totalNum % $offset == 0 ? $totalNum / $offset : intval($totalNum / $offset) + 1;
		$ret['rows']      = $offset;
		$ret['list']      = $list;
		$ret['totalRmb']  = $totalRmb;
		$ret['totalUser'] = $totalUser;
		return $ret;
	}

	/** 消费明细 */
	static public function getExpenseLog($curPage = 1, $offset = 10, $parms = null, $sidx = 'id', $sord = 'DESC') {
		//$time1 = microtime(true);
		$curPage  = max(1, $curPage);
		$totalNum = B_DB::instance('StatsLogExpense')->totalExpenseLog('stats_log_expense', $parms);
		//$list = B_DB::instance('StatsLogExpense')->getPageExpenseLog('stats_log_expense', '*',  $curPage, $offset, $parms, $sidx, $sord);
		//$time2 = microtime(true);
		//$msg = 'msg1:' . ($time2 - $time1);
		//Logger::debug($msg);
		$list = B_DB::instance('StatsLogExpense')->getPageExpenseLog('stats_log_expense', 'id,city_id, pay_action,milpay,left_milpay,coupon,left_coupon,num,`data`,create_at', $curPage, $offset, $parms, $sidx, $sord);
		//$time3 = microtime(true);
		//$msg = 'msg2:' . ($time3 - $time2);
		//Logger::debug($msg);
		$dataList = array();
		if (!empty($list)) {
			foreach ($list as $key => $val) {
				$dataList[$key]                  = $val;
				$cityInfo                        = M_City::getInfo($val['city_id']);
				$dataList[$key]['city_id']       = $cityInfo['id'];
				$dataList[$key]['user_id']       = $cityInfo['user_id'];
				$dataList[$key]['nickname']      = $cityInfo['nickname'];
				$dataList[$key]['total_mil_pay'] = $cityInfo['total_mil_pay'];
				$dataList[$key]['vip_level']     = $cityInfo['vip_level'];

				$dataList[$key]['pay_name'] = isset(T_Word::$EXPENSE_TYPE[$val['pay_action']]) ? T_Word::$EXPENSE_TYPE[$val['pay_action']] : $val['pay_action'];

				if ($val['pay_action'] == B_Log_Trade::E_BuyProps) {
					$propsInfo              = M_Props::baseInfo($val['data']);
					$dataList[$key]['data'] = isset($propsInfo['name']) ? $propsInfo['name'] : $val['data'];
				} else if ($val['pay_action'] == B_Log_Trade::E_FindHero) {
					$heroInfo               = M_Hero::baseInfo($val['data']);
					$dataList[$key]['data'] = isset($heroInfo['nickname']) ? $heroInfo['nickname'] : $val['data'];
				} else if ($val['pay_action'] == B_Log_Trade::E_LearnSkill) {
					$skillInfo              = M_Skill::getBaseInfo($val['data']);
					$dataList[$key]['data'] = isset($skillInfo['name']) ? $skillInfo['name'] : $val['data'];
				} elseif ($val['pay_action'] == B_Log_Trade::E_BuyVipProps) {
					$tmp = explode('_', $val['data']);
					if (count($tmp) == 2) {
						$txt = M_Vip::$shop_type[$tmp[0]] . '_';
						if ($tmp[0] == M_Vip::SHOP_DRAW) {
							$pInfo = M_Props::baseInfo($tmp[1]);
							$txt .= $pInfo['name'];
						} elseif ($tmp[0] == M_Vip::SHOP_EQUI) {
							$eqInfo = M_Equip::baseInfo($tmp[1]);
							$txt .= $eqInfo['name'];
						}
						$dataList[$key]['data'] = $txt;
					}
				}
			}
		}
		//$time4 = microtime(true);
		//$msg = 'msg3:' . ($time4 - $time3);
		//Logger::debug($msg);
		$ret['total']            = $totalNum;
		$ret['page']             = $curPage;
		$ret['totalPage']        = $totalNum % $offset == 0 ? $totalNum / $offset : intval($totalNum / $offset) + 1;
		$ret['rows']             = $offset;
		$ret['list']             = $dataList;
		$ret['ConstExpenseType'] = T_Word::$EXPENSE_TYPE;
		if (isset($parms['currency_type'])) {
			unset($parms['currency_type']);
		}
		$ret['totalMilpay'] = B_DBStats::getColumnsSum('stats_log_expense', 'milpay', $parms);
		$ret['totalCoupon'] = B_DBStats::getColumnsSum('stats_log_expense', 'coupon', $parms);
		//$time5 = microtime(true);
		//$msg = 'msg4:' . ($time5 - $time4);
		//Logger::debug($msg);
		//$msg = 'msg5:' . ($time5 - $time1);
		//Logger::debug($msg);
		return $ret;
	}

	/** 单个玩家消费记录 */
	static public function getExpenseLogOne($parms) {
		$logMilPay = B_DB::instance('StatsLogExpense')->getExpenseLogOneMilPay($parms); //军饷消费
		$logCoupon = B_DB::instance('StatsLogExpense')->getExpenseLogOneCoupon($parms); //点券消费

		return array('milpay' => $logMilPay, 'coupon' => $logCoupon);
	}

	/** 充值消费统计 */
	static public function getConsumerPayLog() {
		$sql  = 'select username,sum(rmb) as pay_total_rmb,sum(milpay) as pay_total_milpay, count(id) as times, create_at from `stats_log_pay`  group by username order by create_at asc';
		$sth  = B_DBStats::getStatsDB()->prepare($sql);
		$ret  = $sth->execute();
		$list = $sth->fetchAll(PDO::FETCH_ASSOC);
		$arr  = array();
		foreach ($list as $val) {
			$username = $val['username'];

			$row1 = B_DB::instance('User')->getBy(array('username' => $username));
			$row2 = B_DB::instance('City')->getBy(array('user_id' => $row1['id']));

			$sql = "select create_at from `stats_log_pay`  where username=\"{$username}\"  order by create_at desc limit 1";
			$sth = B_DBStats::getStatsDB()->prepare($sql);
			$ret = $sth->execute();
			$row = $sth->fetch(PDO::FETCH_ASSOC);

			$tmp                    = array();
			$tmp['mingid']          = $val['username'];
			$tmp['username']        = $username;
			$tmp['username_ext']    = $row1['username_ext'];
			$tmp['city_id']         = isset($row2['id']) ? $row2['id'] : 0;
			$tmp['nickname']        = isset($row2['nickname']) ? $row2['nickname'] : 0;
			$tmp['vip_level']       = isset($row2['vip_level']) ? $row2['vip_level'] : 0;
			$tmp['left_milpay']     = isset($row2['mil_pay']) ? $row2['mil_pay'] : 0;
			$tmp['total_milpay']    = $val['pay_total_milpay'];
			$tmp['total_rmb']       = $val['pay_total_rmb'];
			$tmp['times']           = $val['times'];
			$tmp['first_pay_time']  = isset($val['create_at']) ? date('Y-m-d H:i:s', $val['create_at']) : 0;
			$tmp['last_pay_time']   = isset($row['create_at']) ? date('Y-m-d H:i:s', $row['create_at']) : 0;
			$tmp['last_visit_ip']   = isset($row1['last_visit_ip']) ? $row1['last_visit_ip'] : 0;
			$tmp['last_visit_time'] = isset($row1['last_visit_time']) ? date('Y-m-d H:i:s', $row1['last_visit_time']) : 0;

			$arr[] = $tmp;
		}
		return $arr;
	}


	static public function getExpenseGroupByAction() {
		return B_DB::instance('StatsLogExpense')->getGroupLogByAction();
	}


	static public function PayAward($cityId, $amount = 0) {

		$ret = false;
		if ($amount > 0) {
			$now      = time();
			$baseonce = M_Config::getVal('config_pay_once_award');
			$baseadd  = M_Config::getVal('config_pay_add_award');
			$cityTask = M_Task::getCityTask($cityId);


			$upData = array();
			if (!empty($baseonce)) {
				if ($now > strtotime($baseonce['start']) && $now < strtotime($baseonce['end'])) {
					$cityPayOnce = $cityTask['section_pay_once'];
					if (empty($cityPayOnce) || $baseonce['start'] != $cityPayOnce['t'][0] || $baseonce['end'] != $cityPayOnce['t'][1]) {
						$cityPayOnce = array(
							't'     => array($baseonce['start'], $baseonce['end']),
							'award' => array(),
						);
					}

					foreach ($baseonce['data'] as $k => $val) {
						list($s, $e, $awardId) = $val;
						if ($amount >= $s && $amount <= $e) {
							$cityPayOnce['award'][$k] = 1;
							break;
						}
					}
					$upData['section_pay_once'] = json_encode($cityPayOnce);

				}

			}

			if (!empty($baseadd)) {
				if ($now > strtotime($baseadd['start']) && $now < strtotime($baseadd['end'])) {
					$cityPayAdd = $cityTask['section_pay_add'];
					if (empty($cityPayAdd) || $baseadd['start'] != $cityPayAdd['t'][0] || $baseadd['end'] != $cityPayAdd['t'][1]) {
						$cityPayAdd = array(
							't'     => array($baseadd['start'], $baseadd['end']),
							'num'   => 0,
							'award' => array(),
						);
					}

					$cityPayAdd['num'] = $cityPayAdd['num'] + $amount;
					foreach ($baseadd['data'] as $k => $val) {
						list($num, $awardId) = $val;

						if ($cityPayAdd['num'] >= $num) {
							if (empty($cityPayAdd['award'][$k])) {
								$cityPayAdd['award'][$k] = 1;

								break;
							}
						}
					}
					$upData['section_pay_add'] = json_encode($cityPayAdd);
				}
			}

			if (!empty($upData)) {
				$ret = M_Task::updateCityTask($cityId, $upData);
				if (!$ret) {
					Logger::error(array(__METHOD__, $cityId, $upData));
				}
			}
		}

		return $ret;
	}


	/**
	 * 充值奖励
	 * @param int $cityId 城市ID
	 * @param int $amount 充值金额
	 */
	static public function AwardForTime($cityId, $amount) {
		$data = array();
		if (!empty($cityId)) //判断是不是当前在线用户
		{
			$objPlayer  = new O_Player($cityId);
			$basecfg    = M_Config::getVal();
			$dailyAward = $basecfg['config_pay_award'];
			//print_r($dailyAward);
			$conf = array(
				'start' => $dailyAward[1],
				'end'   => $dailyAward[2]
			);
			if (!empty($dailyAward)) {
				foreach ($dailyAward as $key => $val) {
					if ($key > 2) {
						$conf['list'][] = $dailyAward[$key];
					}
				}
			}

			$d  = time();
			$d1 = strtotime($conf['start']);
			$d2 = strtotime($conf['end']);
			if ($d > $d1 && $d < $d2) { //在起止时间范围内
				$awardId = 0; //没有奖励
				if (!empty($conf['list'])) {
					foreach ($conf['list'] as $va) {
						if ($amount < $va[0]) { //充值金额小于范围没有奖励
							$awardId = 0;
							break;
						}
						if ($amount >= $va[0] && $amount <= $va[1]) //充值金额
						{
							$awardId = isset($va[2]) ? $va[2] : 0;
							if ($awardId > 0) {
								$awardArr = M_Award::rateResult($awardId);
								$objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task);
								$data = M_Award::toText($awardArr);
							} else {
								Logger::error(array(__METHOD__, 'error awardId'));
							}
							break;
						}
					}
					$objPlayer->save();
				}
			}

			//Logger::debug(array(__METHOD__, $amount, $awardId, $conf));
		}
		return $data;

	}

	/** 玩家消费排行 */
	static public function getExpenseRank($curPage = 1, $offset = 10, $parms = null, $sidx = 'id', $sord = 'DESC') {
		$curPage  = max(1, $curPage);
		$totalNum = B_DB::instance('StatsLogExpense')->totalExpenseRank('stats_log_expense', $parms);
		$dataList = array();
		$list     = B_DB::instance('StatsLogExpense')->getPageExpenseRank('stats_log_expense', $curPage, $offset, $parms);
		// 		print_r($list);
		if (!empty($list)) {
			$rank = ($curPage - 1) * $offset;
			foreach ($list as $key => $val) {

				$cityInfo = M_City::getInfo($val['city_id']);
				if (!empty($cityInfo['id'])) {
					$dataList[$key] = $val;
					$rank++;
					$dataList[$key]['rank']      = $rank;
					$dataList[$key]['city_id']   = $val['city_id'];
					$dataList[$key]['user_id']   = $cityInfo['user_id'];
					$dataList[$key]['nickname']  = $cityInfo['nickname'];
					$dataList[$key]['mil_pay']   = $cityInfo['mil_pay'];
					$dataList[$key]['coupon']    = $cityInfo['coupon'];
					$dataList[$key]['vip_level'] = $cityInfo['vip_level'];
				}
			}
		}
		$ret['total']            = $totalNum;
		$ret['page']             = $curPage;
		$ret['totalPage']        = $totalNum % $offset == 0 ? $totalNum / $offset : intval($totalNum / $offset) + 1;
		$ret['rows']             = $offset;
		$ret['list']             = $dataList;
		$ret['ConstExpenseType'] = T_Word::$EXPENSE_TYPE;
		if (isset($parms['currency_type'])) {
			unset($parms['currency_type']);
		}
		$ret['totalMilpay'] = B_DBStats::getColumnsSum('stats_log_expense', 'milpay', $parms);
		$ret['totalCoupon'] = B_DBStats::getColumnsSum('stats_log_expense', 'coupon', $parms);
		return $ret;
	}
}

?>