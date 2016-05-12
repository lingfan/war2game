<?php

class Q_BaseTask extends B_DB_Dao {
	protected $_name = 'base_task';
	protected $_connType = 'base';
	protected $_primary = 'id';

	/**
	 * 获取所有任务基础信息
	 * @author chenhui    on 20110428
	 * @return array 任务基础信息(二维数组)
	 */
	public function all() {
		$row = $this->getAll(array('sort' => 'ASC', 'id' => 'ASC'));
		foreach ($row as $key => $val) {
			$rows[$val['id']] = $val;
		}
		return $rows;
	}
}

?>