<?php

class Q_BaseMultiFB extends B_DB_Dao {
	protected $_name = 'base_multi_fb';
	protected $_connType = 'base';
	protected $_primary = 'id';

	public function all() {
		$row = $this->getAll();
		foreach ($row as $key => $val) {
			$rows[$val['id']] = $val;
		}
		return $rows;
	}
}

?>