<?php

class Q_BaseProbe extends B_DB_Dao {
	protected $_name = 'base_probe';
	protected $_connType = 'base';
	protected $_primary = 'id';

	/**
	 * 查询所有事件
	 */
	public function all() {
		$rows = $this->getAll();
		foreach ($rows as $val) {
			$row[$val['id']] = $val;
		}
		return $row;
	}
}