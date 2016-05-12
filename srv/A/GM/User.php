<?php

/**
 * GM用户查询
 */
class A_GM_User {
	/**
	 * 玩家列表
	 * @author Hejunyun
	 * @param array $formVals
	 */
	static public function getList($formVals) {
		$formVals['page'] = !empty($formVals['page']) ? $formVals['page'] : 1;
		$formVals['rows'] = !empty($formVals['rows']) ? $formVals['rows'] : 20;
		$formVals['sidx'] = !empty($formVals['sidx']) ? $formVals['sidx'] : 'id';
		$formVals['sord'] = !empty($formVals['sord']) ? $formVals['sord'] : 'DESC';
		$formVals['filter'] = !empty($formVals['filter']) ? $formVals['filter'] : '';
		if (!is_array($formVals['filter'])) {
			$formVals['filter'] = array();
		} else {
			foreach ($formVals['filter'] as $key => $val) {
				if (!$val) {
					unset($formVals['filter'][$key]);
				}
			}
		}

		$list = M_User::cityList($formVals);
		$data = array();
		if (!empty($list)) {
			foreach ($list as $key => $val) {

				$objPlayer = new O_Player($val['id']);
				$cityInfo = $objPlayer->getCityBase();

				$userInfo = M_User::getInfo($cityInfo['user_id']);
				$cityLevel = $objPlayer->Build()->getLevel(M_Build::ID_TOWN_CENTER);
				$cityInfo['city_level'] = $cityLevel;
				if ($userInfo) {
					$cityInfo['username'] = $userInfo['username'];
					$cityInfo['login_times'] = $userInfo['login_times'];
					//$list[$key]['gender'] = $userInfo['gender'];
					$cityInfo['status'] = $userInfo['status'];
					$cityInfo['last_visit_ip'] = $userInfo['last_visit_ip'];
					$cityInfo['last_visit_time'] = $userInfo['last_visit_time'];
				}
				$data[$val['id']] = $cityInfo;
			}
		}

		return $data;
	}

	static public function getCityBase($formVals) {
		$ret = false;
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		if ($cityId > 0) {
			$info = M_City::getInfo($cityId);
			$userInfo = M_User::getInfo($info['user_id']);
			if ($userInfo) {
				$info['username'] = $userInfo['username'];
				$info['status'] = $userInfo['status'];
				$info['last_visit_ip'] = $userInfo['last_visit_ip'];
				$info['last_visit_time'] = $userInfo['last_visit_time'];
				$info['is_adult'] = $userInfo['is_adult'];
				$info['online_time'] = $userInfo['online_time'];
				$mapData = M_MapWild::calcWildMapPosXYByNo($info['pos_no']);
				$info['pos_area'] = $mapData[0];
				$info['pos_xy'] = $mapData[1] . '_' . $mapData[2];
			}
			if (isset($info['union_id']) && $info['union_id'] > 0) {
				$unionInfo = M_Union::getInfo($info['union_id']);
				$unionInfo && $info['union_id'] = $unionInfo['name'];
			}

			$objPlayer = new O_Player($cityId);
			$resData = $objPlayer->Res()->get();
			if (!empty($resData)) {
				$info['food'] = $resData['food'];
				$info['gold'] = $resData['gold'];
				$info['oil'] = $resData['oil'];
				$info['food_grow'] = $resData['food_grow'];
				$info['gold_grow'] = $resData['gold_grow'];
				$info['oil_grow'] = $resData['oil_grow'];
			}

			$info && $ret = $info;
		}
		return $ret;
	}

	static public function getCityBuild($formVals) {
		$ret = array();
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		$type = isset($formVals['type']) ? $formVals['type'] : '';
		if ($cityId) {
			$objPlayer = new O_Player($cityId);

			$list = $objPlayer->Build()->get();
			$baseList = M_Base::buildAll();
			foreach ($list as $bid => $binfo) {
				foreach ($binfo as $bpos => $blev) {
					$name = $baseList[$bid]['name'];
					$ret[] = array($name, $bpos, $blev); //建筑ID,建筑位置,建筑等级
				}
			}

		}
		return $ret;
	}

	static public function getCityTech($formVals) {
		$ret = false;
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		if ($cityId) {
			$objPlayer = new O_Player($cityId);
			$list = $objPlayer->Tech()->get();
			$base = M_Base::techAll();
			foreach ($list as $key => $val) {
				$id = $val[0];
				$list[$key][0] = $base[$id]['name'];
			}
			$ret = $list;
		}
		return $ret;
	}

	static public function getCityArmy($formVals) {
		$ret = false;
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		if ($cityId) {
			$objPlayer = new O_Player($cityId);
			$tmpArmyList = $objPlayer->Army()->toData();
			$armyList = array();
			foreach ($tmpArmyList as $akey => $aval) {
				$armyList[] = array(M_Army::$type[$aval['army_id']], $aval['number'], $aval['level'], $aval['exp']); //兵种ID,兵种数量,等级,熟练度
			}
			if (!empty($armyList)) {
				$ret = $armyList;
			}
		}
		return $ret;
	}

	static public function getCityWeapon($formVals) {
		$ret = false;
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		if ($cityId) {
			$weaponList = array();
			$objPlayer = new O_Player($cityId);
			$weaponList = $objPlayer->Weapon()->toFront();
			if (!empty($weaponList)) {
				$baseList = M_Base::weaponAll();
				foreach ($weaponList[0] as $key => $val) {
					$weaponList[0][$key] = $baseList[$val]['name'];
				}
				foreach ($weaponList[1] as $key => $val) {
					$weaponList[1][$key] = $baseList[$val[1]]['name'];
				}
				$ret = $weaponList;
			}
		}
		return $ret;
	}

	static public function getCityProps($formVals) {
		$ret = false;
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		if ($cityId) {
			$objplayer = new O_Player($cityId);
			$itemList = $objplayer->Pack()->get();

			$propsList = array();
			if (!empty($itemList)) {
				$baseList = M_Base::propsAll();
				foreach ($itemList as $pkey => $pval) {
					$propsList[] = array($baseList[$pval[0]]['name'], $pval[1], $pval[2]);
				}
			}
			if (!empty($propsList)) {
				$ret = $propsList;
			}
		}
		return $ret;
	}

	static public function getCityEquip($formVals) {
		$ret = false;
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		if ($cityId) {
			$list = M_Equip::getEquipListForAdm($cityId);
			if (!empty($list)) {
				$ret = $list;
			}
		}
		return $ret;
	}

	static public function getCityTask($formVals) {
		$ret = false;
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		if ($cityId) {
			$list = M_Task::getCityTaskStatus($cityId);
			if (!empty($list)) {
				$baseList = M_Base::taskAll();
				foreach ($list as $key => $val) {
					$list[$key][0] = $baseList[$val[0]]['title'];
				}
			}
			$ret = $list;
		}
		return $ret;
	}

	static public function getCityHero($formVals) {
		$ret = false;
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		if ($cityId) {
			$list = M_Hero::getCityHeroList($cityId);
			foreach ($list as $key => $heroId) {
				$heroInfo = M_Hero::getCityHeroInfo($cityId, $heroId);
				$heroInfo && $ret[$key] = $heroInfo;
			}
		}
		return $ret;
	}

	static public function getCityVip($formVals) {
		$ret = false;
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		if ($cityId) {
			$voidData = M_Extra::getInfo($cityId);
			if (isset($voidData['vip_effect'])) {
				$ret = json_decode($voidData['vip_effect'], true);
			}
		}
		return $ret;
	}

	static public function getCityUseProps($formVals) {
		$ret = false;
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		if ($cityId) {
			$objPlayer = new O_Player($cityId);
			$ret = $objPlayer->Props()->get();
		}
		return $ret;
	}

	/*******************联盟相关******************************/
	static public function UnionList($data) {
		$page = isset($data['page']) ? $data['page'] : 1;
		$pageSize = isset($data['rows']) ? $data['rows'] : 20;
		$ret = M_Union::getList($page, $pageSize);
		foreach ($ret['list'] as $id) {
			$info = M_Union::getInfo($id);
			$data['list'][] = $info;
		}
		//$data['page'] = $ret['page'];
		//$data['sumPage'] = $ret['sumPage'];
		$data['total'] = $ret['total'];
		return $data;
	}

	static public function UnionMember($data) {
		$list = array();
		$id = isset($data['id']) ? intval($data['id']) : 0;
		if ($id) {
			$list = M_Union::getUnionMemberList($id);
		}
		return $list;
	}

	static public function UnionTech($data) {
		$list = array();
		$id = isset($data['id']) ? intval($data['id']) : 0;
		if ($id) {
			$unionInfo = M_Union::getInfo($id);
			$techInfo = json_decode($unionInfo['tech_data'], true);
			$tmp = M_Union::$unionTechName;
			foreach ($tmp as $id => $name) {
				$list[$id] = array($name, $techInfo[$id]);
			}

		}
		return $list;
	}


}

?>