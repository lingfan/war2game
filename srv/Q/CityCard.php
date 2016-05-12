<?php

class Q_CityCard extends B_DB_Dao {
	protected $_name = 'city_card';
	protected $_connType = 'game';
	protected $_primary = 'id';

	/**卡类道具使用统计***************************/
	/**
	 * 根据条件获取卡类道具使用记录
	 * @author duhuihui on 20120827
	 * @param int $curPage 当前页码
	 * @param int $offset 每页条数
	 * @param int $props 道具类型
	 * @param array $parms 其它参数
	 * @return false/array
	 */
	public function getRows($curPage, $offset, $parms = '') {
		$whereArr = array();

		if (!empty($parms['props'])) {
			$whereArr['props_id'] = $parms['props'];
		}
		if (!empty($parms['cardtype'])) {
			$whereArr['card_type'] = $parms['cardtype'];
		}

		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr['create_at'][] = array('>', $val);
				} elseif ($key == 'create_end') {
					$whereArr['create_at'][] = array('<=', $val);
				}
			}
		}
		$rows = $this->getList($start, $offset, $whereArr);
		//更改_end

		return $rows;
	}

	/**
	 * 根据条件获取卡类道具使用使用记录
	 * @author duhuihui on 20120827
	 * @param int $cardtype 卡类型
	 * @param int $props道具类型
	 * @param array $parms 其它参数
	 * @return false/array
	 */
	public function total($parms = array()) {
		$whereArr = array();

		if (!empty($parms['props'])) {
			$whereArr['props_id'] = $parms['props'];
		}
		if (!empty($parms['cardtype'])) {
			$whereArr['card_type'] = $parms['cardtype'];
		}

		if (is_array($parms) && !empty($parms)) {
			foreach ($parms as $key => $val) {
				if ($key == 'create_start') {
					$whereArr['create_at'][] = array('>', $val);
				} elseif ($key == 'create_end') {
					$whereArr['create_at'][] = array('<=', $val);
				}
			}
		}
		return $this->count($whereArr);
	}

}

?>