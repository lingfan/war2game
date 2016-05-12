<?php

class Q_BaseCampaign extends B_DB_Dao {
	protected $_name = 'base_campaign';
	protected $_connType = 'base';
	protected $_primary = 'id';

	/**
	 * 获取所有据点信息
	 * @author huwei
	 * @return array
	 */
	public function all($all = false) {
		$arrWhere = array('is_open' => 1);
		if ($all) {
			$arrWhere = array();
		}
		$list = $this->getsBy($arrWhere);
		$rows = array();
		foreach ($list as $val) {
			$rows[$val['id']] = $val;
		}
		return $rows;

	}
}

?>