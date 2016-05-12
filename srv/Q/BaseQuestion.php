<?php

class Q_BaseQuestion extends B_DB_Dao {
	protected $_name = 'base_question';
	protected $_connType = 'base';
	protected $_primary = 'id';

	public function all() {
		$list = $this->getAll();
		foreach ($list as $val) {
			$rows[$val['id']] = $val;
		}
		return $rows;
	}
}

?>