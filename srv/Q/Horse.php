<?php

/** 公共城市越野DB层 */
class Q_Horse extends B_DB_Dao {
	protected $_name = 'horse';
	protected $_connType = 'game';
	protected $_primary = 'id';

	/**
	 * 获取城市公共越野信息
	 * @author chenhui on 20121207
	 * @return array/bool 城市公共越野信息(1D)
	 */
	public function getRow($nowDate, $cycleNo) {
		$id = $nowDate * 100 + $cycleNo;
		$row = $this->get($id);

		if (!empty($row)) {
			return $row;
		} else {
			$horseInfo = array(
				'id' => $id,
				'horse_date' => $nowDate,
				'cycle_no' => $cycleNo,
				'stage' => 1,
				'stage_endtime' => 1,
				'stage_iscalc' => '[1,1]', //下一阶段,下一场次
				'stage_run_no' => 0,
				'run_per_time' => '[]',
				'first_city_id' => 0,
				'first_award' => 0,
				'horse1' => '[]',
				'horse2' => '[]',
				'horse3' => '[]',
				'horse4' => '[]',
				'horse5' => '[]',
				'horse6' => '[]',
				'horse7' => '[]',
				'join_log' => '[]',
				'award_log' => '[]',
				'award_data' => '[]',
			);
			$initRet = $this->insert($horseInfo);
			if ($initRet) {
				return $horseInfo;
			} else {
				return false;
			}
		}
	}

	/**
	 * 更新城市公共越野信息
	 * @author chenhui on 20121207
	 * @param array $updinfo 要更新的键值对数组
	 * @param int $nowDate
	 * @param int $cycleNo
	 * @return bool true/false
	 */
	public function set($updInfo, $nowDate, $cycleNo) {
		$id = $nowDate * 100 + $cycleNo;
		$ret = $this->update($updInfo, $id);
		return $ret;
	}


	/******* 跑马数据统计 ****/
	/**
	 * 根据条件获取跑马记录
	 * @author chenhui on 20130104
	 * @param int $curPage 当前页码
	 * @param int $offset 每页条数
	 * @param array $parms 其它参数
	 * @return false/array 2D
	 */
	public function getRows($curPage, $offset, $parms = '') {
		$whereArr = array();

		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr['horse_date'] = $val;
				} else {
					$whereArr[$key] = $val;
				}

			}
		}
		$start = ($curPage - 1) * $offset;
		$rows = $this->getList($start, $offset, $whereArr, array('horse_date' => 'DESC', 'cycle_no' => 'DESC'));

		return $rows;
	}

	/**
	 * 根据条件获取跑马记录数目
	 * @author chenhui on 20130104
	 * @param array $parms 其它参数
	 * @return false/array
	 */
	public function total($parms = '') {
		$whereArr = array();

		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr['horse_date'] = $val;
				} else {
					$whereArr[$key] = $val;
				}

			}
		}

		return $this->count($whereArr);
	}

	/** 获取每个月的数据 */
	public function getMonthSysHorse($dateVal) {
		$sql = sprintf("SELECT * FROM %s WHERE id LIKE '{$dateVal}%'", $this->_name);
		$rows = $this->fetchAll($sql);
		return $rows;
	}

}

?>