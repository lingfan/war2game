<?php

/**
 *幸运卡片统计信息
 */
class A_Stats_Card {

	static public function UserCardlist($parms = array()) {
		$ret = false;
		$curPage = isset($parms['page']) ? $parms['page'] : 1; //当前页
		$offset = isset($parms['rows']) ? $parms['rows'] : 20; //每页显示多少行
		$params = isset($parms['filter']) ? $parms['filter'] : array();

		$list = B_DB::instance('CityCard')->getRows($curPage, $offset, $params); //根据条件获得卡类道具使用记录
		$propsArr = M_Base::propsAll();
		foreach ($list as $key => $value) {
			$city_info = M_City::getInfo($value['city_id']);
			$list[$key]['nickname'] = $city_info['nickname'];
			$propsInfo = $propsArr[$value['props_id']];
			$list[$key]['props_name'] = $propsInfo['name'];
			$list[$key]['create_at'] = date('Y-m-d H:i:s', $value['create_at']);
		}

		$ret['list'] = $list;
		$ret['total'] = B_DB::instance('CityCard')->total($params);

		return $ret;
	}


}

?>