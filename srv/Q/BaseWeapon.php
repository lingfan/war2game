<?php

class Q_BaseWeapon extends B_DB_Dao {
	protected $_name = 'base_weapon';
	protected $_connType = 'base';
	protected $_primary = 'id';


	/**
	 * 获取所有武器基础信息
	 * @author chenhui on 20110408
	 * @return array 武器基础信息(二维数组)
	 */
	public function all() {
		$rows = array();
		$row = $this->getAll(array('sort' => 'ASC'));
		foreach ($row as $key => $val) {
			$val['need_tech'] = !empty($val['need_tech']) ? B_Utils::kv2vv(json_decode($val['need_tech'], true)) : array();
			$val['need_build'] = !empty($val['need_build']) ? B_Utils::kv2vv(json_decode($val['need_build'], true)) : array();
			$rows[$val['id']] = $val;
		}
		return $rows;

	}

	public function getInfoByName($name) {
		$row = $this->getBy(array('name' => $name));
		return $row;

	}

	/**
	 * 获取所有NPC武器基础信息
	 * @author Hejunyun
	 * @return array 武器基础信息(二维数组)
	 */
	public function getNpcList() {
		$rows = $this->getsBy(array('is_npc' => 1));
		return $rows;
	}

}

?>