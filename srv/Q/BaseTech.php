<?php

class Q_BaseTech extends B_DB_Dao {
	protected $_name = 'base_tech';
	protected $_connType = 'base';
	protected $_primary = 'id';

	public function getOne($id) {
		$row = $this->get($id);
		$row['upg'] = $this->getUpgInfoById($id);
		return $row;

	}

	/**
	 * 获取所有科技基础信息
	 * @author chenhui    on 20110413
	 * @return array 科技基础信息(二维数组)
	 */
	public function all() {
		$row = $this->getAll();
		foreach ($row as $key => $val) {
			$rows[$val['id']] = $val;
			$rows[$val['id']]['upg'] = $this->getUpgInfoById($val['id']);
		}
		return $rows;

	}

	public function getUpgInfoById($techId) {
		$techId = intval($techId);
		$row = B_DB::instance('BaseTechAttr')->getsBy(array('tech_id' => $techId));
		foreach ($row as $val) {
			$val['need_build'] = B_Utils::kv2vv(json_decode($val['need_build'], true));
			$val['need_tech'] = B_Utils::kv2vv(json_decode($val['need_tech'], true));

			$rows[$val['level']] = $val;
		}
		return $rows;

	}
}

?>