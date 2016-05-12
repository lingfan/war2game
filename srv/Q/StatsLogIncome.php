<?php

/**
 * 收入日志类
 */
class Q_StatsLogIncome extends B_DB_Dao {
	/**
	 * 添加日志
	 * @author huwei
	 * @param array $data 键值对
	 */
	public function insert($logData) {
		$ret = false;
		if (!empty($logData)) {
			$ret = B_DBStats::insert('stats_log_income', $logData);
		}
		return $ret;
	}

	public function getInfo($id) {
		return B_DBStats::findByPk('stats_log_income', $id);
	}

	/**
	 * 查询订单号是否存在
	 * @author huwei
	 * @param string $order_no 订单编号
	 * @return int
	 */
	public function getInfoByOrderNo($order_no, $consumer_id) {
		$sql = "SELECT id from stats_log_income WHERE order_no=:order_no && consumer_id=:consumer_id";
		$sth = B_DBStats::getStatsDB()->prepare($sql);
		$sth->bindValue(':order_no', $order_no);
		$sth->bindValue(':consumer_id', $consumer_id);
		$ret = $sth->execute();
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		return $row['id'];
	}

	/**
	 * 获取充值的玩家数量
	 * @author Hejunyun
	 * @param array $parms
	 */
	public function getTotalUser($parms) {
		$whereArr = array();
		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr[] = "`create_at`>=:{$key}";
				} elseif ($key == 'create_end') {
					$whereArr[] = "`create_at`<=:{$key}";
				} else {
					$whereArr[] = "`{$key}`=:{$key}";
				}
			}
		}
		$where = !empty($whereArr) ? ' WHERE ' . implode(' AND ', $whereArr) : '';
		$sql = "SELECT COUNT(DISTINCT city_id) as num FROM stats_log_income {$where}";
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