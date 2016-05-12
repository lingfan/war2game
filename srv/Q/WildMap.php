<?php

class Q_WildMap extends B_DB_Dao {
	protected $_name = 'wild_map';
	protected $_connType = 'game';
	protected $_primary = 'pos_no';

	public function totalNpcNum($npcId) {
		$num = 0;
		if (!empty($npcId)) {
			$num = $this->count(array('type' => T_Map::WILD_MAP_CELL_NPC, 'npc_id' => $npcId));
		}
		return $num;
	}

	public function clean($zone, $npcId) {
		if (empty($npcId)) {
			return false;
		}

		if ($zone == 1) {
			$where = "pos_no < 2000000";
		} else if ($zone == 3) {
			$where = "pos_no > 3000000";
		} else {
			$where = "pos_no > 2000000 AND pos_no < 3000000";
		}

		$sql = "DELETE FROM wild_map WHERE  {$where} AND npc_id={$npcId}";

		return $this->execute($sql, array(), true);;
	}

	public function total($zone) {
		if ($zone == 1) {
			$where = "pos_no < 2000000";
		} else if ($zone == 3) {
			$where = "pos_no > 3000000";
		} else {
			$where = "pos_no > 2000000 AND pos_no < 3000000";
		}

		$sql = "SELECT count(pos_no) as num FROM wild_map WHERE  {$where}";
		$rows = $this->fetch($sql);
		return $rows['num'];
	}
}

?>