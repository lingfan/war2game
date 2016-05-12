<?php

/** 突围基础数据 */
class Q_BaseBreakout extends B_DB_Dao {
	protected $_name = 'base_breakout';
	protected $_connType = 'base';
	protected $_primary = 'id';

	/**
	 * 获取所有突围基础数据
	 * @author chenhui    on 20121019
	 * @return array 突围基础信息(2D)
	 */
	public function all() {
		$row = $this->getAll();
		$rows = array();

		foreach ($row as $key => $val) {
			$rows[$val['id']] = $val;
		}

		return $rows;
	}

}

?>