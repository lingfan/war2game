<?php

class Q_BaseBuild extends B_DB_Dao {
	protected $_name = 'base_build';
	protected $_connType = 'base';
	protected $_primary = 'id';

	public function getOne($id) {
		$id = intval($id);
		$row = $this->get($id);
		$row['upg'] = $this->getUpgInfoById($id);

		return $row;

	}

	/**
	 * 获取所有建筑基础信息
	 * @author chenhui    on 20110413
	 * @return array 建筑基础信息(二维数组)
	 */
	public function all() {
		$row = $this->getAll();
		$rows = array();
		foreach ($row as $key => $val) {
			$rows[$val['id']] = $val;
			$rows[$val['id']]['upg'] = $this->getUpgInfoById($val['id']);
		}
		return $rows;

	}

	public function getUpgInfoById($buildId) {
		$buildId = intval($buildId);
		$row = B_DB::instance('BaseBuildAttr')->getsBy(array('build_id' => $buildId));

		$rows = array();
		$initPeople = M_Config::getVal('city_max_people'); //初始最大人口数
		foreach ($row as $val) {
			$val['res_grow_now'] = 0; //资源建筑当前等级基础产量,其它则为0
			$val['res_grow_next'] = 0; //资源建筑下一等级基础产量,其它则为0
			if (M_Build::ID_GOLD_BASE == $val['build_id']) {
				$val['res_grow_now'] = M_Formula::calcResBuildBaseGrow(T_App::RES_GOLD, $val['level']);
				$val['res_grow_next'] = M_Formula::calcResBuildBaseGrow(T_App::RES_GOLD, $val['level'] + 1);
			} else if (M_Build::ID_FOOD_BASE == $val['build_id']) {
				$val['res_grow_now'] = M_Formula::calcResBuildBaseGrow(T_App::RES_FOOD, $val['level']);
				$val['res_grow_next'] = M_Formula::calcResBuildBaseGrow(T_App::RES_FOOD, $val['level'] + 1);
			} else if (M_Build::ID_OIL_BASE == $val['build_id']) {
				$val['res_grow_now'] = M_Formula::calcResBuildBaseGrow(T_App::RES_OIL, $val['level']);
				$val['res_grow_next'] = M_Formula::calcResBuildBaseGrow(T_App::RES_OIL, $val['level'] + 1);
			} else if (M_Build::ID_HOUSE == $val['build_id']) {
				$val['res_grow_now'] = M_Formula::calcHouseCapaCity($initPeople, $val['level']);
				$val['res_grow_next'] = M_Formula::calcHouseCapaCity($initPeople, $val['level'] + 1);
			}

			$val['need_build'] = json_decode($val['need_build'], true);
			$val['need_tech'] = json_decode($val['need_tech'], true);

			$rows[$val['level']] = $val;
		}

		if (empty($rows)) {
			Logger::debug(__METHOD__, 'empty rows', func_get_args());
		}
		return $rows;
	}
}

?>