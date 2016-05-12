<?php

class Q_BaseQqShare extends B_DB_Dao {
	protected $_name = 'base_qq_share';
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