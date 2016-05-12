<?php

/**
 * 收入日志类
 */
class Q_StatsLogExpense extends B_DB_Dao {
	/**
	 * 添加日志
	 * @author huwei
	 * @param array $data 键值对
	 */
	public function insert($logData) {
		$ret = false;
		if (!empty($logData)) {
			$ret = B_DBStats::insert('stats_log_expense', $logData);
		}
		return $ret;
	}

	public function getInfo($id) {
		return B_DBStats::findByPk('stats_log_expense', $id);
	}

	/**
	 * 消费明细分组统计
	 */
	public function getGroupLogByAction() {
		$sql = "SELECT SUM(milpay) AS milpay_price, SUM(coupon) AS coupon_price, `pay_action` FROM stats_log_expense GROUP BY `pay_action`";
		$sth = B_DBStats::getStatsDB()->prepare($sql);
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	/**
	 * 单个玩家消费明细(军饷)
	 */
	public function getExpenseLogOneMilPay($parms) {
		$city_id = intval(isset($parms['city_id']) ? $parms['city_id'] : 0);
		$create_start = intval(isset($parms['create_start']) ? $parms['create_start'] : 0);
		$create_end = intval(isset($parms['create_end']) ? $parms['create_end'] : time());
		$where = " WHERE city_id = {$city_id} AND create_at >= {$create_start} AND create_at <= {$create_end}";

		$sql = "SELECT SUM(milpay) AS `num`,pay_action FROM stats_log_expense {$where} GROUP BY pay_action";
		$sth = B_DBStats::getStatsDB()->prepare($sql);
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	/**
	 * 单个玩家消费明细(点券)
	 */
	public function getExpenseLogOneCoupon($parms) {
		$city_id = intval(isset($parms['city_id']) ? $parms['city_id'] : 0);
		$create_start = intval(isset($parms['create_start']) ? $parms['create_start'] : 0);
		$create_end = intval(isset($parms['create_end']) ? $parms['create_end'] : time());
		$where = " WHERE city_id = {$city_id} AND create_at >= {$create_start} AND create_at <= {$create_end}";

		$sql = "SELECT SUM(coupon) AS `num`,pay_action FROM stats_log_expense {$where} GROUP BY pay_action";
		$sth = B_DBStats::getStatsDB()->prepare($sql);
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	public function getPageExpenseLog($table, $selected = '*', $curPage, $offset, $parms = '', $sidx = 'id', $sord = 'DESC') {
		if (empty($table)) {
			return false;
		}
		$selected = $selected ? $selected : '*';
		$sidx = $sidx ? $sidx : 'id';
		$sord = $sord ? $sord : 'DESC';
		$whereArr = array();
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr[] = "`create_at`>=:{$key}";
				} elseif ($key == 'create_end') {
					$whereArr[] = "`create_at`<=:{$key}";
				} elseif ($key == 'currency_type') {
					if ($val == 'milpay') {
						$whereArr[] = "`milpay`>0";
					} elseif ($val == 'coupon') {
						$whereArr[] = "`coupon`>0";
					}
					unset($parms[$key]);
				} else {
					$whereArr[] = "`{$key}`=:{$key}";
				}
			}
		}
		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$start = ($curPage - 1) * $offset;
		$sql = "SELECT {$selected} FROM `{$table}` {$where} ORDER BY `{$sidx}` {$sord}  LIMIT {$start}, {$offset}";
		$sth = B_DBStats::getStatsDB()->prepare($sql);
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				$sth->bindValue(':' . $key, $val);
			}
		}
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	public function getPageExpenseRank($table, $curPage, $offset, $parms = '') {
		if (empty($table)) {
			return false;
		}
		$selected = $parms['currency_type'];
		$whereArr = array();
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr[] = "`create_at`>=:{$key}";
				} elseif ($key == 'create_end') {
					$whereArr[] = "`create_at`<=:{$key}";
				} elseif ($key == 'currency_type') {
					if ($val == 'milpay') {
						$whereArr[] = "`milpay`>0";
					} elseif ($val == 'coupon') {
						$whereArr[] = "`coupon`>0";
					}
					unset($parms[$key]);
				} else {
					if (is_array($val) && !empty($val)) {
						$val1 = implode(',', $val);
						$val2 = '(' . $val1 . ')';
						$whereArr[] = "`{$key}` in $val2";
					} else {
						$whereArr[] = "`{$key}`=:{$key}";
					}
				}
			}
		}

		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$start = ($curPage - 1) * $offset;
		$sql = "SELECT city_id , sum({$selected}) as total_milpay,MAX(create_at) as  create_at FROM `{$table}` {$where} group by city_id ORDER BY total_milpay DESC LIMIT {$start},{$offset}";
		$sth = B_DBStats::getStatsDB()->prepare($sql);
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if (is_array($val) && !empty($val)) {

				} else {
					$sth->bindValue(':' . $key, $val);
				}
			}
		}
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	public function totalExpenseRank($table, $parms = '') {
		$whereArr = array();
		$selected = $parms['currency_type'];
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr[] = "`create_at`>=:{$key}";
				} elseif ($key == 'create_end') {
					$whereArr[] = "`create_at`<=:{$key}";
				} elseif ($key == 'currency_type') {
					if ($val == 'milpay') {
						$whereArr[] = "`milpay`>0";
					} elseif ($val == 'coupon') {
						$whereArr[] = "`coupon`>0";
					}
					unset($parms[$key]);
				} else {
					if (is_array($val) && !empty($val)) {
						$val1 = implode(',', $val);
						$val2 = '(' . $val1 . ')';
						$whereArr[] = "`{$key}` in $val2";
					} else {
						$whereArr[] = "`{$key}`=:{$key}";
					}
				}
			}
		}

		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$sql = "select count(city_id) as num from (SELECT city_id  FROM `{$table}` {$where} group by city_id) as city ";
		$sth = B_DBStats::getStatsDB()->prepare($sql);
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if (is_array($val) && !empty($val)) {

				} else {
					$sth->bindValue(':' . $key, $val);
				}
			}
		}
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		return $row['num'];
	}

	public function totalExpenseLog($table, $parms = '') {
		$whereArr = array();
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr[] = "`create_at`>=:{$key}";
				} elseif ($key == 'create_end') {
					$whereArr[] = "`create_at`<=:{$key}";
				} elseif ($key == 'currency_type') {
					if ($val == 'milpay') {
						$whereArr[] = "`milpay`>0";
					} elseif ($val == 'coupon') {
						$whereArr[] = "`coupon`>0";
					}
					unset($parms[$key]);
				} else {
					$whereArr[] = "`{$key}`=:{$key}";
				}
			}
		}
		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$sql = "SELECT count(id) as num FROM `{$table}` {$where}";
		$sth = B_DBStats::getStatsDB()->prepare($sql);
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				$sth->bindValue(':' . $key, $val);
			}
		}
		$ret = $sth->execute();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		return $row['num'];
	}

}

?>