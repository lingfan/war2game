<?php

class Q_CityTask extends B_DB_Dao {
	protected $_name = 'city_task';
	protected $_connType = 'game';
	protected $_primary = 'city_id';

	/**
	 * 根据城市ID获取城市任务信息
	 * @author chenhui on 20110428
	 * @param int city_id 城市ID
	 * @return array/bool 城市任务信息(一维数组)
	 */
	public function getRow($city_id) {
		$row = $this->get($city_id);

		if (!empty($row)) {
			return $row;
		} else {
			$taskInfo = array(
				'city_id' => $city_id,
				'tasks_ok' => '[]',
				'tasks_end' => '[]',
				'tasks_daily_ok' => '[]',
				'tasks_daily_end' => '[]',
				'daily_date' => '',
				'drama_end' => '[]',
				'create_at' => time()
			);
			$initRet = $this->insert($taskInfo);
			if ($initRet) {
				return $taskInfo;
			} else {
				return false;
			}
		}
	}
}

?>