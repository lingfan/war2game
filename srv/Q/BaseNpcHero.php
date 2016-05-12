<?php

class Q_BaseNpcHero extends B_DB_Dao {
	protected $_name = 'base_npc_hero';
	protected $_connType = 'base';
	protected $_primary = 'id';

	public function getTypeArr() {
		$sql = "SELECT `type` FROM base_npc_hero GROUP BY `type`";
		$rows = $this->fetchAll($sql);
		return $rows;

	}

	/**
	 * 获取所有NPC英雄信息
	 * @author Hejunyun
	 * @return array
	 */
	public function all() {
		$list = $this->getAll();
		$rows = array();
		foreach ($list as $val) {
			$rows[$val['id']] = $val;
		}
		return $rows;

	}

	public function getInfoByName($nickname) {
		$row = $this->fetch(array('nickname' => $nickname));
		return $row;

	}

}

?>