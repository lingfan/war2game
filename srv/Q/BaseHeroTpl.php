<?php

class Q_BaseHeroTpl extends B_DB_Dao {
	protected $_name = 'base_hero_tpl';
	protected $_connType = 'base';
	protected $_primary = 'id';

	public function getIdByName($name) {
		$row = $this->getBy(array('nickname' => $name));
		return $row['id'];
	}

	/**
	 * 获取全部基础军官模板
	 * @author chenhui on 20110824
	 * @return array 军官模板数据
	 */
	public function all() {
		$list = $this->getAll(array('quality' => 'ASC', 'id' => 'ASC'));
		$rows = array();
		foreach ($list as $val) {
			$rows[$val['id']] = $val;
		}
		return $rows;

	}

}

?>