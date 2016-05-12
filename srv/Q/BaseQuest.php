<?php

class Q_BaseQuest extends B_DB_Dao {
	protected $_name = 'base_quest';
	protected $_connType = 'base';
	protected $_primary = 'id';

	public function all() {
		$list = $this->getAll(array('id'=>'ASC'));
		foreach ($list as $val) {
			$rows[$val['id']] = $val;
		}
		return $rows;
	}
}

?>