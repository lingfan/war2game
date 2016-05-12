<?php

class Q_FbPass extends B_DB_Dao {
	protected $_name = 'fb_pass';
	protected $_connType = 'game';
	protected $_primary = 'fb_no';

	/**
	 *  获取战役排行
	 * @author duhuihui
	 * @param int $fbNo 战役编号
	 * @return array
	 */
	public function getRow($fbNo) {
		$rows = $this->get($fbNo);
		if (empty($rows)) {
			$rows = array(
				'fb_no' => $fbNo,
				'recently_passed' => json_encode(array()),
				'first_passed' => json_encode(array()),
				'loss_least' => json_encode(array()),
				'level_lowest' => json_encode(array()),
			);
			$ret = $this->insert($rows);
		}
		return $rows;
	}

	/**
	 * 获取战役排行
	 * @author duhuihui
	 * @return array
	 */
	public function all() {
		$rows = $this->getAll();
		$row = array();
		foreach ($rows as $key => $val) {
			$row[$val['fb_no']] = $val;
		}
		return $row;
	}


}

?>