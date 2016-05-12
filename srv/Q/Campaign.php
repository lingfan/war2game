<?php

class Q_Campaign extends B_DB_Dao {
	protected $_name = 'campaign';
	protected $_connType = 'game';
	protected $_primary = 'id';

	/**
	 * 根据ID获取据点信息
	 * @author huwei on 20110428
	 * @param int id 据点ID
	 * @return array/bool
	 */
	public function getRow($id) {
		$row = $this->getBy(array('id' => intval($id)));

		if (!empty($row)) {
			return $row;
		} else {
			$no = json_encode(array(0, array(0, 0, 0)));
			$rowInfo = array(
				'id' => $id,
				'owner_union_id' => 0,
				'join_union_ids' => '[]',
				'no_11' => $no,
				'no_12' => $no,
				'no_13' => $no,
				'no_14' => $no,
				'no_15' => $no,
				'no_16' => $no,
				'no_21' => $no,
				'no_22' => $no,
				'no_23' => $no,
				'no_31' => $no,
				'no_32' => $no,
				'no_41' => $no,
			);

			return $this->insert($rowInfo);
		}
	}


	/**
	 * 根据据点ID 更新据点信息
	 * @author huwei on 20120321
	 * @param int id 城市ID
	 * @param array $updInfo 要更新的键值对数组
	 * @return bool true/false
	 */
	public function updateInfo($id, $updInfo) {
		$ret = $this->update($updInfo, $id);
		return $ret;
	}
}

?>