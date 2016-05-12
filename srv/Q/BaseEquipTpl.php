<?php

class Q_BaseEquipTpl extends B_DB_Dao {
	protected $_name = 'base_equip_tpl';
	protected $_connType = 'base';
	protected $_primary = 'name';


	public function getNames() {
		$row = $this->getsBy(array('type' => 1));
		return $row;
	}

	/**
	 * @author huwei
	 * @return array
	 */
	public function all($all = false) {

		$list = $this->getAll();
		$rows = array();
		foreach ($list as $val) {
			$rows[$val['id']] = $val;
		}
		return $rows;

	}


	public function getIdByName($name, $quality) {
		$whereArr = array('name' => $name, 'quality' => $quality);
		$row = $this->getBy($whereArr);
		return $row['id'];
	}

	public function getId($needLevel, $pos, $quality) {
		$whereArr = array(
			'need_level' => $needLevel,
			'pos' => $pos,
			'quality' => $quality,
		);
		$row = $this->getBy($whereArr);
		return $row['id'];
	}


}

?>