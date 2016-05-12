<?php

/**
 * 用户统计信息
 */
class A_Stats_Pay {
	/** 单个玩家消费记录 */
	static public function ConsumpLogOne($formVals) {
		$parms = !empty($formVals['filter']) ? $formVals['filter'] : array();
		if (isset($parms['username'])) {
			$nickname = $parms['username'];
			if ($nickname) {
				$cityId = M_City::getCityIdByNickName($nickname);
				$parms['city_id'] = $cityId;
			}
			unset($parms['username']);
		}

		$ret = M_Pay::getExpenseLogOne($parms);
		return $ret;
	}

	static public function ConsumerPayLog($formVals) {
		$ret = M_Pay::getConsumerPayLog();
		return $ret;
	}

	/**
	 * 充值日志
	 */
	static public function PayLog($formVals) {
		$curPage = !empty($formVals['page']) ? $formVals['page'] : 1;
		$offset = !empty($formVals['rows']) ? $formVals['rows'] : 20;
		$sidx = !empty($formVals['sidx']) ? $formVals['sidx'] : 'id';
		$sord = !empty($formVals['sord']) ? $formVals['sord'] : 'DESC';
		$parms = !empty($formVals['filter']) ? $formVals['filter'] : array();
		if (!empty($parms['username'])) {
			$cityId = M_City::getCityIdByNickName($parms['username']);
			$cityInfo = M_City::getInfo($cityId);
			$userInfo = M_User::getInfo($cityInfo['user_id']);
			$parms['username'] = $userInfo['username'];
		}
		$curPage = max(1, $curPage);
		$ret = M_Pay::getPayLog($curPage, $offset, $parms, $sidx, $sord);

		return $ret;
	}

	/**
	 * 收入日志
	 */
	static public function IncomeLog($formVals) {
		$curPage = !empty($formVals['page']) ? $formVals['page'] : 1;
		$offset = !empty($formVals['rows']) ? $formVals['rows'] : 20;
		$sidx = !empty($formVals['sidx']) ? $formVals['sidx'] : 'id';
		$sord = !empty($formVals['sord']) ? $formVals['sord'] : 'DESC';
		$parms = !empty($formVals['filter']) ? $formVals['filter'] : array();

		if (isset($parms['username'])) {
			$nickname = $parms['username'];
			if ($nickname) {
				$cityId = M_City::getCityIdByNickName($nickname);
				$parms['city_id'] = $cityId;
			}
			unset($parms['username']);
		}

		if (isset($parms['pay_action']) && intval($parms['pay_action']) == 0) {
			unset($parms['pay_action']);
		}

		$curPage = max(1, $curPage);
		$ret = M_Pay::getIncomeLog($curPage, $offset, $parms, $sidx, $sord);

		return $ret;
	}

	static public function LastRows($formVals) {
		$lastId = $formVals['last_id'];
		$list = B_DB::instance('StatsLogPay')->getRowsByLastId($lastId);
		return $list;
	}

	static public function Accounts($formVals) {
		$ret = array();
		if (isset($formVals['create_start']) && isset($formVals['create_end'])) {
			if (isset($formVals['consumer_id']) && !$formVals['consumer_id']) {
				unset($formVals['consumer_id']);
			}
			$start = $formVals['create_start'];
			$end = $formVals['create_end'];
			$formVals['pay_action'] = 1;
			//unset($formVals['create_start']);
			//unset($formVals['create_end']);
			$t = $start;
			while ($t <= $end) {
				$formVals['create_start'] = $t;
				$formVals['create_end'] = $t + (3600 * 24) - 1;
				$ret[$t]['rmb'] = B_DBStats::getColumnsSum('stats_log_pay', 'rmb', $formVals);
				$ret[$t]['rmb'] = $ret[$t]['rmb'] ? $ret[$t]['rmb'] : 0;
				$ret[$t]['user'] = B_DB::instance('StatsLogPay')->getTotalUser($formVals);
				$ret[$t]['user'] = $ret[$t]['user'] ? $ret[$t]['user'] : 0;
				$t = $t + (3600 * 24);
			}
		}
		return $ret;
	}

	/**
	 * 消费明细
	 */
	static public function ConsumpLog($formVals) {
		$curPage = !empty($formVals['page']) ? $formVals['page'] : 1;
		$offset = !empty($formVals['rows']) ? $formVals['rows'] : 20;
		$sidx = 'id'; //!empty($formVals['sidx']) ? $formVals['sidx'] : 'id';
		$sord = !empty($formVals['sord']) ? $formVals['sord'] : 'DESC';
		$parms = !empty($formVals['filter']) ? $formVals['filter'] : array();
		if (isset($parms['username'])) {
			$nickname = $parms['username'];
			if ($nickname) {
				$cityId = M_City::getCityIdByNickName($nickname);
				$parms['city_id'] = $cityId;
			}
			unset($parms['username']);
		}
		$curPage = max(1, $curPage);

		$ret = M_Pay::getExpenseLog($curPage, $offset, $parms, $sidx, $sord);
		return $ret;
	}

	static public function GetCityNameById($formVals) {
		if (isset($formVals['city_id']) && $formVals['city_id'] > 0) {
			$cityInfo = M_City::getInfo($formVals['city_id']);
		}

		return $cityInfo['nickname'];
	}

	static public function ConsumpLogGroup() {
		$ret = M_Pay::getExpenseGroupByAction();
		if (!empty($ret)) {
			foreach ($ret as $k => $val) {
				$result[$val['pay_action']]['milpay_price'] = $val['milpay_price'];
				$result[$val['pay_action']]['coupon_price'] = $val['coupon_price'];
				$result[$val['pay_action']]['action_name'] = isset(T_Word::$EXPENSE_TYPE[$val['pay_action']]) ? T_Word::$EXPENSE_TYPE[$val['pay_action']] : '';
			}
		}

		return $result;
	}

	/**
	 * 玩家消费排行
	 */
	static public function ConsumpRank($formVals) {
		$curPage = !empty($formVals['page']) ? $formVals['page'] : 1;
		$offset = !empty($formVals['rows']) ? $formVals['rows'] : 20;
		$sidx = 'id'; //!empty($formVals['sidx']) ? $formVals['sidx'] : 'id';
		$sord = !empty($formVals['sord']) ? $formVals['sord'] : 'DESC';
		$parms = !empty($formVals['filter']) ? $formVals['filter'] : array();
		$curPage = max(1, $curPage);
		if (isset($parms['username'])) {
			$nickname = $parms['username'];
			if ($nickname) {
				$cityId = M_City::getCityIdByNickName($nickname);
				$parms['city_id'] = $cityId;
			}
			unset($parms['username']);
		}
		$ret = M_Pay::getExpenseRank($curPage, $offset, $parms, $sidx, $sord);
		return $ret;
	}

	/**
	 * 玩家消费排行
	 */
	static public function ConsumpRankEdit() {
		$ret['ConstExpenseType'] = T_Word::$EXPENSE_TYPE;
		return $ret;
	}
}

?>