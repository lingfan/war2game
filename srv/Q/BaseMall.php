<?php

class Q_BaseMall extends B_DB_Dao {
	protected $_name = 'base_mall';
	protected $_connType = 'base';
	protected $_primary = 'id';

	/**
	 * 获取所有商城基础信息
	 * @author chenhui on 20110408
	 * @return array 道具基础信息(二维数组)
	 */
	public function all() {
		$row = $this->getAll(array('sort' => 'ASC', 'id' => 'ASC'));
		$rows = array();
		foreach ($row as $key => $val) {
			$rows[$val['id']] = $val;
		}
		return $rows;
	}

	public function getInfoByName($name) {
		$row = $this->getBy(array('name' => $name));
		return $row;

	}
}

?>