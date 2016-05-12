<?php

class Q_WarReport extends B_DB_Dao {
	protected $_name = 'war_report';
	protected $_connType = 'game';
	protected $_primary = 'id';


	/**
	 * 获取战斗报告
	 * @author Hejunyun
	 * @param string $attOrDef 进攻方或防守方字段
	 * @param int $cityId 城市ID
	 * @return array $data
	 */
	public function getRows($attOrDef, $cityId, $type) {
		$data = array();
		/** &计算参数 */
		$reportNum = array(
			'atk_city_id' => M_War::DEL_ATK,
			'def_city_id' => M_War::DEL_DEF,
		);
		if (isset($reportNum[$attOrDef]) && !empty($cityId)) {
			$start = microtime();

			$subwhere = ($type != 2) ? " AND LENGTH(replay_address) > 0 " : '';

			$sql = 'SELECT * FROM %s WHERE %s=%d ' . $subwhere . ' AND type = %d   AND (flag_del & %d = 0) ORDER BY id ASC';
			$sql = sprintf($sql, $this->_name, $attOrDef, $cityId, $type, $reportNum[$attOrDef]);

			$data = $this->fetchAll($sql);

			$diff = microtime() - $start;
			if ($diff > 0.1) {
				Logger::debug(array(__METHOD__, $diff, $sql));
			}

		}
		return $data;
	}

	/**
	 * 清除过期的战报日志
	 * @param int $t
	 * @return boolean
	 */
	public function cleanExpireData($t) {
		$ret = 0;
		if ($t) {
			$ret = $this->deleteBy(array('create_at' => array('<', $t)));
		}
		return $ret;
	}

}

?>