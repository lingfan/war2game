<?php

class Q_BaseWarMapCell extends B_DB_Dao {
	protected $_name = 'base_war_map_cell';
	protected $_connType = 'base';
	protected $_primary = 'id';

	/**
	 * 获取战斗地图元素列表
	 * @author huwei on 20110615
	 * @return array
	 */
	public function all() {
		$rows = $this->getAll();
		$data = array();
		foreach ($rows as $k => $v) {
			$data[$v['id']] = $v;
		}
		return $data;

	}


}

?>