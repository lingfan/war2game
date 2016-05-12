<?php

class Q_Alliance extends B_DB_Dao {
	protected $_name = 'alliance';
	protected $_connType = 'game';
	protected $_primary = 'id';


	public function idList() {
		$rows = $this->getsBy(array(), array('level' => 'DESC', 'total_renown' => 'DESC', 'id' => 'ASC'));
		return $rows;
	}

	/**
	 * 根据联盟名称查询联盟ID
	 * @author HeJunyun
	 * @param string $name 联盟名称
	 * @return array/bool
	 */
	public function getIdByName($name) {
		$row = $this->getBy(array('name' => $name));
		return $row['id'];
	}

}

?>