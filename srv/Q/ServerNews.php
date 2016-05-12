<?php

class Q_ServerNews extends B_DB_Dao {
	protected $_name = 'server_news';
	protected $_connType = 'game';
	protected $_primary = 'id';

	/**
	 * 获取所有信息
	 * @author duhuihui
	 * @return array
	 */
	public function all() {
		$rows = $this->getAll();
		$row = array();
		foreach ($rows as $key => $val) {
			$row[$val['id']] = $val;
		}
		return $row;

	}


}

?>