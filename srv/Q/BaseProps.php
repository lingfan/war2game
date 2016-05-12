<?php

class Q_BaseProps extends B_DB_Dao {
	protected $_name = 'base_props';
	protected $_connType = 'base';
	protected $_primary = 'id';

	/**
	 * 获取所有道具基础信息
	 * @author chenhui on 20110408
	 * @return array 道具基础信息(二维数组)
	 */
	public function all() {
		$row = $this->getAll(array('sort' => 'ASC', 'id' => 'ASC'));
		foreach ($row as $key => $val) {
			$rows[$val['id']] = $val;
		}
		return $rows;
	}

	public function getInfoByName($name) {
		$row = $obj->getBy(array('name' => $name));
		return $row;

	}
}

?>