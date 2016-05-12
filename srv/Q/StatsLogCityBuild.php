<?php

class Q_StatsLogCityBuild extends B_DB_Dao {
	/**
	 * 获取城市中心升级日志
	 * @author Hejunyun
	 * @param int $day 日期
	 */
	public function getStatsBuildLog($day) {
		$day = intval($day);
		$sql = "SELECT * FROM stats_log_city_build WHERE add_day <= {$day}";
		$sth = B_DBStats::getStatsDB()->prepare($sql);
		$ret = $sth->execute();
		$rows = $sth->fetchAll();
		if (!$ret) {
			Logger::db(__METHOD__, $sth->errorInfo(), func_get_args());
			return false;
		}
		return $rows;
	}

}

?>