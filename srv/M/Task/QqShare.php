<?php

class M_Task_QqShare {
	/*
	 * 建筑升级
	*/
	static public function build_up(&$val, $completeTxt, $params) //val array(id=>level)
	{
		$ret          = false;
		$arr          = M_QqShare::getBaseInfoByType('build_up');
		$params['id'] = !empty($params['id']) ? $params['id'] : 0; //0是任意类型
		if (isset($completeTxt['build_up'][$params['id']]) && !empty($params['level'])) {
			if ($params['level'] > $completeTxt['build_up'][$params['id']]) {
				$ret = true;
			}
		} else {
			$ret = true;
		}
		if ($ret && !empty($params['level']) && isset($arr[$params['id'] . '_' . $params['level']])) //如果存在
		{
			$val[$params['id']] = $params['level'];
			return $arr[$params['id'] . '_' . $params['level']];
		} else {
			return 0;
		}

	}

	/*
	 * 科技升级   科技ID=>level,科技ID=>level
	*/
	static public function tech_up(&$val, $completeTxt, $params) //level
	{
		$ret = false;
		$arr = M_QqShare::getBaseInfoByType('tech_up');

		if (isset($completeTxt['tech_up']) && !empty($params['level'])) {
			if ($params['level'] > $completeTxt['tech_up']) {
				$ret = true;
			}
		} else {
			$ret = true;
		}
		if ($ret && !empty($params['level']) && isset($arr[$params['level']])) //如果存在
		{
			$val = $params['level'];
			return $arr[$params['level']];
		} else {
			return 0;
		}

	}

	/*
	 * 装备强化  强化等级
	*/
	static public function equip_strong(&$val, $completeTxt, $params) //level
	{
		$ret = false;
		$arr = M_QqShare::getBaseInfoByType('equip_strong');
		if (isset($completeTxt['equip_strong']) && !empty($params['level'])) {
			if ($params['level'] > $completeTxt['equip_strong']) {
				$ret = true;
			}
		} else {
			$ret = true;
		}
		if ($ret && !empty($params['level']) && isset($arr[$params['level']])) {
			$val = $params['level'];
			return $arr[$params['level']];
		} else {
			return 0;
		}
	}

	//装备合成   品质=>次数
	static public function equip_mix(&$val, $completeTxt, $params) //array(level=>qual)
	{
		$ret             = false;
		$arr             = M_QqShare::getBaseInfoByType('equip_mix');
		$params['qual']  = !empty($params['qual']) ? $params['qual'] : 0;
		$params['level'] = !empty($params['level']) ? $params['level'] : 0;
		if (isset($completeTxt['equip_mix'][$params['level']])) {
			if ($params['level'] > array_search($params['qual'], $completeTxt['equip_mix'])) {
				$ret = true;
			}
		} else {
			$ret = true;
		}
		if ($ret && isset($arr[$params['level'] . '_' . $params['qual']])) //如果存在
		{
			$val[$params['level']] = $params['qual'];
			return $arr[$params['level'] . '_' . $params['qual']];
		} else {
			return 0;
		}
	}

	/*
	 *技能  技能ID
	*/
	static public function hero_skill(&$val, $completeTxt, $params) //array(id=>1)
	{
		$ret          = false;
		$arr          = M_QqShare::getBaseInfoByType('hero_skill');
		$params['id'] = !empty($params['id']) ? $params['id'] : 0; //0是任意类型
		if (!isset($completeTxt['hero_skill'][$params['id']])) {
			$ret = true;
		}
		if ($ret && isset($arr[$params['id']])) //如果存在
		{
			$val[$params['id']] = 1;
			return $arr[$params['id']];
		} else {
			return 0;
		}
	}

	/*
	 * 打副本
	*/
	static public function fb_atk(&$val, $completeTxt, $params) //array(id=>日期)
	{
		$ret          = false;
		$arr          = M_QqShare::getBaseInfoByType('fb_atk');
		$params['id'] = !empty($params['id']) ? $params['id'] : 0; //0是任意类型

		if (!isset($completeTxt['fb_atk'][$params['id']])) {
			$ret = true;
		}
		if ($ret && isset($arr[$params['id']])) //如果存在
		{
			$val[$params['id']] = date('Ymd');
			return $arr[$params['id']];
		} else {
			return 0;
		}

	}

	/*
	 * 打突击
	*/
	static public function break_out(&$val, $completeTxt, $params) //val array(id=>1)
	{
		$ret          = false;
		$arr          = M_QqShare::getBaseInfoByType('break_out');
		$params['id'] = !empty($params['id']) ? $params['id'] : 0; //0是任意类型
		if (!isset($completeTxt['break_out'][$params['id']])) {
			$ret = true;
		}
		if ($ret && isset($arr[$params['id']])) //如果存在
		{
			$val[$params['id']] = 1;
			return $arr[$params['id']];
		} else {
			return 0;
		}

	}

	/*
	 * 攻打学院
	*/
	static public function atk_wildnpc(&$val, $completeTxt, $params) //val array(type_level=>1)
	{
		$ret             = false;
		$arr             = M_QqShare::getBaseInfoByType('atk_wildnpc');
		$params['level'] = !empty($params['level']) ? $params['level'] : 0; //0是任意等级
		$params['type']  = !empty($params['type']) ? $params['type'] : 0; //0是任意类型
		if (!isset($completeTxt['atk_wildnpc'][$params['type'] . '_' . $params['level']])) {
			$ret = true;
		}

		if ($ret && isset($arr[$params['type'] . '_' . $params['level']])) //如果存在
		{
			$val[$params['type'] . '_' . $params['level']] = 1;
			return $arr[$params['type'] . '_' . $params['level']];
		} else {
			return 0;
		}

	}

	/*
	 * 每日占领玩家城市
	*/
	static public function occupied_city(&$val, $completeTxt, $params) //val array(level=>1,date=>日期)
	{
		$ret             = false;
		$arr             = M_QqShare::getBaseInfoByType('occupied_city');
		$params['level'] = !empty($params['level']) ? $params['level'] : 0; //0是任意等级
		if (isset($completeTxt['occupied_city'][$params['level']]) && isset($completeTxt['occupied_city']['date'])) {
			if ($completeTxt['occupied_city']['date'] != date('Ymd')) {
				$ret = true;
			}
		} else {
			$ret = true;
		}
		if ($ret && isset($arr[$params['level']])) //如果存在
		{
			$val[$params['level']] = 1;
			$val['date']           = date('Ymd');
			return $arr[$params['level']];
		} else {
			return 0;
		}

	}

	/*
	 * 军团每日领取奖励
	*/
	static public function union_getaward(&$val, $completeTxt, $params) //日期
	{
		$arr = M_QqShare::getBaseInfoByType('union_getaward');
		if (!empty($arr)) //如果存在
		{
			$val = date('Ymd');
			return $arr;
		} else {
			return 0;
		}

	}

	/*
	 * 玩家每日使用道具
	*/
	static public function props_use(&$val, $completeTxt, $params) //日期
	{
		$arr = M_QqShare::getBaseInfoByType('props_use');
		if (!empty($arr)) //如果存在
		{
			$val = date('Ymd');
			return $arr;
		} else {
			return 0;
		}


	}

	/*
	 * 每日购买任何道具
	*/
	static public function props_buy(&$val, $completeTxt, $params) //日期
	{
		$arr = M_QqShare::getBaseInfoByType('props_buy');
		if (!empty($arr)) //如果存在
		{
			$val = date('Ymd');
			return $arr;
		} else {
			return 0;
		}

	}

	/*
	 * 每日抽奖得到道具
	*/
	static public function props_award(&$val, $completeTxt, $params) //日期
	{
		$arr = M_QqShare::getBaseInfoByType('props_award');

		if (!empty($arr)) //如果存在
		{
			$val = date('Ymd');
			return $arr;
		} else {
			return 0;
		}

	}


	/*
	 * 军团贡献
	*/
	static public function union_contribution(&$val, $completeTxt, $params) //日期
	{
		$arr = M_QqShare::getBaseInfoByType('union_contribution');

		if (!empty($arr)) //如果存在
		{
			$val = date('Ymd');
			return $arr;
		} else {
			return 0;
		}
	}

	/*
	 * 军官培养    点数
	*/
	static public function hero_train(&$val, $completeTxt, $params) //num
	{
		$ret = false;
		$arr = M_QqShare::getBaseInfoByType('hero_train');
		if (isset($completeTxt['hero_train'])) {
			if ($params['num'] > $completeTxt['hero_train']) {
				$ret = true;
			}
		} else {
			$ret = true;
		}
		foreach (array_reverse($arr, true) as $key => $value) {
			if ($params['num'] > $key) {
				$params['num'] = $key;
				break;
			}
		}
		if ($ret && !empty($params['num']) && isset($arr[$params['num']])) //如果存在
		{
			$val = $params['num'];
			return $arr[$params['num']];
		} else {
			return 0;
		}
	}


}

?>