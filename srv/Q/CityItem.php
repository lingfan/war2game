<?php

class Q_CityItem extends B_DB_Dao {
	protected $_name = 'city_item';
	protected $_connType = 'game';
	protected $_primary = 'id';

	/**
	 * 根据城市ID获取城市物品
	 * @author huwei on 20111014
	 * @param  int $cityId 城市ID
	 * @param  int $type 物品类型
	 * @return array
	 */
	public function all($cityId) {
		$rows = $this->getsBy(array('city_id' => $cityId), array('id' => 'ASC'));
		$data = array();
		foreach ($rows as $val) {
			$data[$val['id']] = $val;
		}
		return $data;


	}



	/****************以下是pm系统需要***************************************/
	/**
	 * 玩家拥有某道具数量排行
	 * @author chenhui on 20130118
	 * @param int $propsId 道具ID
	 * @param int $cityId 城市ID
	 * @return array 2D
	 */
	public function getPropsRank($propsId, $cityId) {
		$cityId = intval($cityId);
		$pid = intval($propsId);
		$whereAnd = !empty($cityId) ? " AND i.city_id = {$cityId} " : '';
		$sql = "SELECT SUM(i.num) AS num,i.city_id,c.nickname FROM city c LEFT JOIN city_item i ON i.city_id = c.id WHERE i.props_id = {$pid} {$whereAnd} GROUP BY i.city_id ORDER BY num DESC LIMIT 100";
		$rows = $this->fetchAll($sql);
		return $rows;

	}

}

?>