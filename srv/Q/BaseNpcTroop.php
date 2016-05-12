<?php

/**
 * NPC数据层
 * @author chenhui on 20110513
 */
class Q_BaseNpcTroop extends B_DB_Dao {
	protected $_name = 'base_npc_troop';
	protected $_connType = 'base';
	protected $_primary = 'id';


	/**
	 * 获取所有NPC信息
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

	/**
	 * 获取所有副本NPC
	 */
	public function getAllFbNpc() {
		$rows = $this->getsBy(array('type' => M_NPC::FB_NPC));
		return $rows;

	}

	/**
	 * 获取所有野外NPC
	 */
	public function getAllMapNpc() {
		$rows = $this->getsBy(array('type' => array('<', M_NPC::FB_NPC)));
		return $rows;

	}

}

?>