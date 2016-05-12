<?php

class A_Stats_Info {
	static public function User($params='') {
		return false;
	}

	/**
	 * 获取用户在线人数
	 * @author huwei
	 */
	static public function Online($data = array()) {
		$consumer_id = isset($data['consumer_id']) ? $data['consumer_id'] : 0;
		$list = M_Client::getList();
		sort($list);
		$arr = array();
		foreach ($list as $key) {
			$userInfo = M_User::getInfo($key);
			if ($consumer_id) {
				if ($userInfo && $userInfo['consumer_id'] == $consumer_id) {
					$cityInfo = M_City::getInfoByUserId($userInfo['id']);
					list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($cityInfo['pos_no']);
					$arr[$key] = array(
						'user_id' => $userInfo['id'],
						'city_id' => $cityInfo['id'],
						'reg_time' => $cityInfo['created_at'],
						'nickname' => $cityInfo['nickname'],
						'last_visit_time' => $userInfo['last_visit_time'],
						'last_visit_ip' => $userInfo['last_visit_ip'],
						'pos_xy' => $posX . '_' . $posY,
						'pos_area' => isset(T_App::$map[$zone]) ? T_App::$map[$zone] : 0,
						'online_time' => $userInfo['online_time'],
						'login_times' => $userInfo['login_times'],
					);
				}
			} else {
				if ($userInfo) {
					$cityInfo = M_City::getInfoByUserId($userInfo['id']);
					list($zone, $posX, $posY) = M_MapWild::calcWildMapPosXYByNo($cityInfo['pos_no']);
					$arr[$key] = array(
						'user_id' => $userInfo['id'],
						'city_id' => $cityInfo['id'],
						'reg_time' => $cityInfo['created_at'],
						'nickname' => $cityInfo['nickname'],
						'last_visit_time' => $userInfo['last_visit_time'],
						'last_visit_ip' => $userInfo['last_visit_ip'],
						'pos_xy' => $posX . '_' . $posY,
						'pos_area' => isset(T_App::$map[$zone]) ? T_App::$map[$zone] : 0,
						'online_time' => $userInfo['online_time'],
						'login_times' => $userInfo['login_times'],
					);
				}
			}

		}
		return $arr;
	}

	static public function YestodayOnline($parms) {
		$row = array();
		$day = isset($parms['day']) ? $parms['day'] : 0;
		$day = intval($day);
		if ($day) {
			$row = B_DBStats::getRow('stats_online_people', array('day' => $day));
		}
		return $row;
	}

	static public function TodayOnline() {
		$rc = new B_Cache_RC(T_Key::STATS_ONLINE_USER_NUM, date('Ymd'));
		$data = $rc->smembers(); //取值
		return $data;
	}

	/**
	 * 玩家列表
	 * @author Hejunyun
	 * @param array $formVals
	 */
	static public function UserList($formVals) {
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

		if (isset($formVals['filter']['create_start'])) {
			$formVals['filter']['create_start'] = strtotime($formVals['filter']['create_start']);
		}

		if (isset($formVals['filter']['create_end'])) {
			$formVals['filter']['create_end'] = strtotime($formVals['filter']['create_end']);
		}

		$list = M_User::cityList($formVals);
		$data = array();
		if (!empty($list)) {
			foreach ($list as $key => $val) {
				$cityInfo = M_City::getInfo($val['id']);
				$userInfo = M_User::getInfo($cityInfo['user_id']);
				$cityInfo['city_level'] = $cityInfo['level'];
				if ($userInfo) {
					$cityInfo['online_time'] = $userInfo['online_time'];
					$cityInfo['server_id'] = $userInfo['server_id'];
					$cityInfo['username'] = $userInfo['username'];
					$cityInfo['username_ext'] = $userInfo['username_ext'];
					$cityInfo['login_times'] = $userInfo['login_times'];
					//$list[$key]['gender'] = $userInfo['gender'];
					$cityInfo['status'] = $userInfo['status'];
					$cityInfo['last_visit_ip'] = $userInfo['last_visit_ip'];
					$cityInfo['last_visit_time'] = $userInfo['last_visit_time'];
					$cityInfo['created_at'] = $cityInfo['created_at'];
					$cityInfo['ban_login_time'] = $userInfo['ban_login_time'];
				}

				$data[$val['id']] = $cityInfo;
			}
		}

		return $data;
	}

	static public function CityInfo($formVals) {
		$ret = false;
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		if ($cityId > 0) {
			$objPlayer = new O_Player($cityId);
			$info = $objPlayer->getCityBase();
			$userInfo = M_User::getInfo($info['user_id']);
			if ($userInfo) {
				$info['username'] = $userInfo['username'];
				$info['username_ext'] = $userInfo['username_ext'];
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


			$resData = $objPlayer->Res()->get();
			if (!empty($resData)) {
				$info['food'] = $resData['food'];
				$info['gold'] = $resData['gold'];
				$info['oil'] = $resData['oil'];
				$info['food_grow'] = $resData['food_grow'];
				$info['gold_grow'] = $resData['gold_grow'];
				$info['oil_grow'] = $resData['oil_grow'];
			}


			$voidData = $objPlayer->Res()->calcResBaseAdd();
			$objPlayer->save();

			$arrGrow = $objPlayer->City()->filterAward($voidData['base']);

			$info && $ret = $info;
		}
		return $ret;
	}

	static public function CityOther($formVals) {
		$ret = false;
		$cityId = isset($formVals['city_id']) ? intval($formVals['city_id']) : 0;
		$type = isset($formVals['type']) ? $formVals['type'] : '';
		if ($cityId) {
			if ($type == 'build') {
				$objPlayer = new O_Player($cityId);

				$list = $objPlayer->Build()->get();
				$baseList = M_Base::buildAll();
				foreach ($list as $bid => $binfo) {
					foreach ($binfo as $bpos => $blev) {
						$name = $baseList[$bid]['name'];
						$ret[] = array($name, $bpos, $blev); //建筑ID,建筑位置,建筑等级
					}
				}
			} elseif ($type == 'tech') {
				$objPlayer = new O_Player($cityId);
				$list = $objPlayer->Tech()->get();
				$base = M_Base::techAll();
				foreach ($list as $key => $val) {
					$id = $val[0];
					$list[$key][0] = $base[$id]['name'];
				}
				$ret = $list;
			} elseif ($type == 'army') {
				$objPlayer = new O_Player($cityId);
				$tmpArmyList = $objPlayer->Army()->toData();
				$armyList = array();
				foreach ($tmpArmyList as $akey => $aval) {
					$armyList[] = array(M_Army::$type[$aval['army_id']], $aval['number'], $aval['level'], $aval['exp']); //兵种ID,兵种数量,等级,熟练度
				}
				if (!empty($armyList)) {
					$ret = $armyList;
				}
			} elseif ($type == 'weapon') {
				$weaponList = array();
				$objPlayer = new O_Player($cityId);
				$weaponList = $objPlayer->Weapon()->toFront();
				if (!empty($weaponList)) {
					$baseList = M_Base::weaponAll();
					foreach ($weaponList[0] as $key => $val) {
						$weaponList[0][$key] = $baseList[$val]['name'];
					}
					//ksort($weaponList[1]);
					foreach ($weaponList[1] as $key => $val) {
						$weaponList[1][$key] = $baseList[$val[1]]['name'];
					}
					$weaponList[2] = implode(',', $weaponList[2]);
					$ret = $weaponList;
				}
			} elseif ($type == 'props') {
				$objplayer = new O_Player($cityId);
				$itemList = $objplayer->Pack()->get();


				$propsList = array();
				if (!empty($itemList)) {
					$baseList = M_Base::propsAll();
					$n = 1;
					foreach ($itemList as $itemVal) { //物品id, 基础道具id, 数量, 是否绑定
						$propsList[] = array($baseList[$itemVal[0]]['name'], $itemVal[2], 0, $n);
						$n++;
					}
				}
				if (!empty($propsList)) {
					$ret = $propsList;
				}
			} elseif ($type == 'equip') {
				$list = M_Equip::getEquipListForAdm($cityId);
				if (!empty($list)) {
					$ret = $list;
				}
			} elseif ($type == 'task') {

				$list = M_Task::getCityTaskStatus($cityId);
				if (!empty($list)) {
					$baseList = M_Base::taskAll();
					foreach ($list as $key => $val) {
						$list[$key][0] = $baseList[$val[0]]['title'];
					}
				}
				$ret = $list;
			} elseif ($type == 'hero') {
				$baseSkill = M_Base::skillAll();
				$baseWeapon = M_Base::weaponAll();

				$list = M_Hero::getCityHeroList($cityId);
				foreach ($list as $key => $heroId) {
					$heroInfo = M_Hero::getHeroInfo($heroId);

					if (!empty($heroInfo['weapon_id'])) {
						$name = isset($baseWeapon[$heroInfo['weapon_id']]['name']) ? $baseWeapon[$heroInfo['weapon_id']]['name'] : $heroInfo['weapon_id'];
						$heroInfo['weapon_id'] = $name;
					}
					if (!empty($heroInfo['skill_slot'])) {
						$name = isset($baseSkill[$heroInfo['skill_slot']]['name']) ? $baseSkill[$heroInfo['skill_slot']]['name'] : $heroInfo['skill_slot'];
						$heroInfo['skill_slot'] = $name;
					}
					if (!empty($heroInfo['skill_slot_1'])) {
						$name = isset($baseSkill[$heroInfo['skill_slot_1']]['name']) ? $baseSkill[$heroInfo['skill_slot_1']]['name'] : $heroInfo['skill_slot_1'];
						$heroInfo['skill_slot_1'] = $name;
					}
					if (!empty($heroInfo['skill_slot_2'])) {
						$name = isset($baseSkill[$heroInfo['skill_slot_2']]['name']) ? $baseSkill[$heroInfo['skill_slot_2']]['name'] : $heroInfo['skill_slot_2'];
						$heroInfo['skill_slot_2'] = $name;
					}

					$ret[$key] = $heroInfo;
				}
			} elseif ($type == 'vip') {
				$voidData = M_Extra::getInfo($cityId);
				if (isset($voidData['vip_effect'])) {
					$ret = json_decode($voidData['vip_effect'], true);
				}
			} elseif ($type == 'useprops') {
				$objPlayer = new O_Player($cityId);
				$ret = $objPlayer->Props()->get();
			} elseif ($type == 'auction') {
				$arrAucId = M_Auction::getCityAucList($cityId);
				if (!empty($arrAucId) && is_array($arrAucId)) {
					foreach ($arrAucId as $aucId) {
						$ret[] = M_Auction::getAucInfo($aucId);
					}
				}
			} elseif ($type == 'march') {
				$ret = M_March::getMarchList($cityId, M_War::MARCH_OWN_ATK);

				$cityInfo = M_City::getInfo($cityId);
				if (!empty($cityInfo['fb_battle_id'])) {
					$BD = M_Battle_Info::get($cityInfo['fb_battle_id']);
					$fbInfo['action_type'] = M_March::MARCH_ACTION_FB;
					$fbInfo['arrived_time'] = $BD['StartTime'];
					$fbInfo['flag'] = M_March::MARCH_FLAG_BATTLE;
					$ret[] = $fbInfo;
				}

				$boutInfo = M_BreakOut::getCityBreakOut($cityId);
				if (!empty($boutInfo['battle_id'])) {
					$BD = M_Battle_Info::get($boutInfo['battle_id']);
					$boutInfo['action_type'] = M_March::MARCH_ACTION_BOUT;
					$boutInfo['arrived_time'] = $BD['StartTime'];
					$boutInfo['flag'] = M_March::MARCH_FLAG_BATTLE;
					$ret[] = $boutInfo;
				}

				$objPlayer = new O_Player($cityId);
				$objFloor = $objPlayer->Floor();
				$bId = $objFloor->getBId();
				if (!empty($bId)) {
					$BD = M_Battle_Info::get($bId);
					$boutInfo['action_type'] = M_March::MARCH_ACTION_BOUT;
					$boutInfo['arrived_time'] = $BD['StartTime'];
					$boutInfo['flag'] = M_March::MARCH_FLAG_BATTLE;
					$ret[] = $boutInfo;
				}
			}

		}

		return $ret;
	}

	/** 跑马赢取数据展示 */
	static public function HorseShow($formVals) {
		$ret = array();
		$strLog = !empty($formVals['log']) ? $formVals['log'] : '';
		$strData = !empty($formVals['data']) ? $formVals['data'] : '';
		if (!empty($strLog)) {
			$arrLog = json_decode(base64_decode($strLog), true);
			$arrData = json_decode(base64_decode($strData), true);
			if (!empty($arrLog) && is_array($arrLog)) {
				foreach ($arrLog as $cityId => $payNum) {
					$cityInfo = M_City::getInfo($cityId);
					$isRece = isset($arrData[$cityId]) ? 0 : 1;
					$ret[] = array($cityId, $cityInfo['nickname'], $payNum, $isRece); //城市ID,城市名字,领取军饷数,是否领取
				}
			}
		}
		return $ret;
	}

	/** 跑马所有玩家投注数据展示 */
	static public function HorseShowjoin($formVals) {
		$ret = array();
		$strJoin = !empty($formVals['join']) ? $formVals['join'] : '';
		if (!empty($strJoin)) {
			$arrJoin = json_decode(base64_decode($strJoin), true);
			if (!empty($arrJoin) && is_array($arrJoin)) {
				foreach ($arrJoin as $cityId => $payAll) {
					$cityInfo = M_City::getInfo($cityId);
					$ret[] = array($cityId, $cityInfo['nickname'], $payAll); //城市ID,城市名称,总投注军饷数
				}
			}
		}
		return $ret;
	}

	static public function UserTotal($formVals) {
		$formVals['page'] = !empty($formVals['page']) ? $formVals['page'] : 1;
		$formVals['rows'] = !empty($formVals['rows']) ? $formVals['rows'] : 20;
		$formVals['sidx'] = !empty($formVals['sidx']) ? $formVals['sidx'] : 'id';
		$formVals['sord'] = !empty($formVals['sord']) ? $formVals['sord'] : 'DESC';
		if (!is_array($formVals['filter'])) {
			$formVals['filter'] = array();
		} else {
			foreach ($formVals['filter'] as $key => $val) {
				if (!$val) {
					unset($formVals['filter'][$key]);
				}
			}
		}

		if (isset($formVals['filter']['create_start'])) {
			$formVals['filter']['create_start'] = strtotime($formVals['filter']['create_start']);
		}

		if (isset($formVals['filter']['create_end'])) {
			$formVals['filter']['create_end'] = strtotime($formVals['filter']['create_end']);
		}


		$num = M_User::totalUser($formVals);
		return $num;
	}

	static public function GetUserId($formVals) {
		if (isset($formVals['username'])) {
			$row = B_DB::instance('User')->getBy(array('username' => $formVals['username']));
			return $row['id'];
		}
		return false;
	}

	static public function GetUserIdArr($formVals) {
		if (isset($formVals['username_ext'])) {
			$row = B_DB::instance('User')->getsBy(array('username_ext' => $formVals['username_ext']));
			return $row;
		}
		return false;
	}

	/**
	 * 禁止/允许 聊天
	 * @author hejunyun
	 * @return bool
	 */
	static public function BanTalking($formVals) {
		$result = 0;
		if (isset($formVals['id']) && isset($formVals['ban_talking'])) {
			$updinfo = array(
				'ban_talking' => $formVals['ban_talking']
			);
			$result = M_City::setCityInfo($formVals['id'], $updinfo);
			$result = $result ? 1 : 0;
		}
		return $result;
	}

	/**
	 * 冻结账号
	 * @author hejunyun
	 */
	static public function Freeze($formVals) {
		$result = 0;
		if (isset($formVals['id']) && isset($formVals['ban_login_time'])) {
			$result = M_User::updateInfo($formVals);
		}
		return $result;
	}

	static public function Tuser($formVals) {
		$cityId = isset($formVals['city_id']) ? $formVals['city_id'] : 0;
		$cityInfo = M_City::getInfo($cityId);
		$userId = $cityInfo['user_id'];
		$ret = M_Client::del($userId);
		return $ret;
	}

	/**
	 * 模板装备列表
	 */
	static public function EquipList() {
		$idlist = M_Equip::getEquipTplList();
		foreach ($idlist as $id) {
			$info = M_Equip::baseInfo($id);
			if (isset($info['id'])) {
				$tmp = array(
					'id' => $info['id'],
					'name' => $info['name'],
					'quality' => $info['quality'],
				);
				$equipList[] = $tmp;
			}
		}
		return $equipList;
	}

	static public function PropsList() {
		/** 道具列表 */
		$list = B_DB::instance('BaseProps')->all();
		foreach ($list as $val) {
			if (isset($val['id'])) {
				$propsList[$val['id']] = $val;
			}
		}
		return $propsList;
	}


	static public function SendGoods($formVals) {
		$ret = array();
		$tplAward = array();
		if (!isset($formVals['nickname'])) {
			return (array)false;
		}
		$nickname = explode("\n", $formVals['nickname']);
		if (!is_array($nickname)) {
			return (array)false;
		}
		if (count($nickname) < 1) {
			return (array)false;
		}
		$consumerIds = isset($formVals['consumer_ids']) ? $formVals['consumer_ids'] : array();
		//根据玩家昵称获取城市ID
		$newName = array();
		foreach ($nickname as $key => $val) {
			$cityId = M_City::getCityIdByNickName(trim($val));
			if ($cityId > 0) {
				$cityInfo = M_City::getInfo($cityId);
				if (isset($consumerIds[$cityInfo['consumer_id']])) {
					$newName[$key] = array(
						'id' => $cityId,
						'nickname' => trim($val)
					);
				}
			}
		}

		if (empty($newName)) {
			return (array)false;
		}

		$nickname = $newName;
		/** 送资源  */
		$res = array();
		$formVals['gold'] = intval($formVals['gold']);
		if (isset($formVals['gold']) && $formVals['gold'] > 0) {
			$res['gold'] = $formVals['gold'];
			$tplAward['gold'] = $formVals['gold'];
		}
		$formVals['food'] = intval($formVals['food']);
		if (isset($formVals['food']) && $formVals['food'] > 0) {
			$res['food'] = $formVals['food'];
			$tplAward['food'] = $formVals['food'];
		}
		$formVals['oil'] = intval($formVals['oil']);
		if (isset($formVals['oil']) && $formVals['oil'] > 0) {
			$res['oil'] = $formVals['oil'];
			$tplAward['oil'] = $formVals['oil'];
		}
		if (count($res) > 0) {
			foreach ($nickname as $val) {
				$objPlayer = new O_Player($val['id']);

				foreach ($res as $k => $num) {
					$objPlayer->Res()->incr($k, $num, true);
				}

				$ret[$val['id']][] = true;
			}
		}
		/** 送资源结束 */
		/** 送军饷、礼券、威望、功勋、VIP等级 */
		//军饷点券
		$formVals['mil_pay'] = intval($formVals['mil_pay']);
		$formVals['coupon'] = intval($formVals['coupon']);
		$formVals['total_mil_pay'] = isset($formVals['total_mil_pay']) ? intval($formVals['total_mil_pay']) : 0;
		if ((isset($formVals['mil_pay']) && $formVals['mil_pay'] > 0)
			|| (isset($formVals['coupon']) && $formVals['coupon'] > 0)
			|| (isset($formVals['total_mil_pay']) && $formVals['total_mil_pay'])
		) {
			foreach ($nickname as $val) {
				if ($formVals['total_mil_pay']) {
					$cityInfo = M_City::getInfo($val['id']);
					$total = $cityInfo['total_mil_pay'] + $formVals['total_mil_pay'];
					$vipLevel = M_Formula::calcVipLevelByTotalMilPay($total);
					$setVipArr = array(
						'vip_level' => $vipLevel,
						'vip_endtime' => time() + (3600 * 24 * 365 * 10),
						'total_mil_pay' => $total
					);
					$ret[$val['id']][] = M_City::setCityInfo($val['id'], $setVipArr);
					($cityInfo['vip_level'] != $vipLevel) && M_MapWild::syncWildMapBlockCache($cityInfo['pos_no']); //刷新此块地图数据
				}
				if ($formVals['mil_pay'] || $formVals['coupon']) {

					$formVals['mil_pay'] && $tplAward['milpay'] = $formVals['mil_pay'];
					$formVals['coupon'] && $tplAward['coupon'] = $formVals['coupon'];
					$addArr = array('milpay' => $formVals['mil_pay'], 'coupon' => $formVals['coupon']);

					$objPlayer = new O_Player($val['id']);
					$objPlayer->City()->mil_pay += $formVals['mil_pay'];
					$objPlayer->City()->coupon += $formVals['coupon'];
					$objPlayer->Log()->income(T_App::MILPAY, $formVals['mil_pay'], B_Log_Trade::I_Give);
					$objPlayer->Log()->income(T_App::COUPON, $formVals['coupon'], B_Log_Trade::I_Give);
					$ret = $objPlayer->save();
					$ret[$val['id']][] = $ret;
				}
			}
		}

		/*
		 $formVals['renown'] = intval($formVals['renown']);
		if (isset($formVals['renown']) && $formVals['renown'] > 0)
		{
		foreach ($nickname as $val)
		{
		$ret[] = M_User::addRenown($val['id'], $formVals['renown']);
		}
		}
		$formVals['mil_medal'] = intval($formVals['mil_medal']);
		if (isset($formVals['mil_medal']) && $formVals['mil_medal'] > 0)
		{
		foreach ($nickname as $val)
		{
		$ret[] = M_User::addMedal($val['id'], $formVals['mil_medal']);
		}
		}
		*/
		$renown = isset($formVals['renown']) ? intval($formVals['renown']) : 0;
		$warexp = isset($formVals['mil_medal']) ? intval($formVals['mil_medal']) : 0;
		$marchNum = isset($formVals['march_num']) ? intval($formVals['march_num']) : 0;

		if ($renown || $warexp || $marchNum) {
			$renown && $tplAward['renown'] = $renown;
			$warexp && $tplAward['warexp'] = $warexp;
			$marchNum && $tplAward['march_num'] = $marchNum;
			$addItemArr = array(
				'renown' => $renown,
				'warexp' => $warexp,
				'march_num' => $marchNum
			);

			foreach ($nickname as $val) {
				$objPlayer = new O_Player($val['id']);
				$ret[$val['id']][] = $objPlayer->City()->addCityItem($addItemArr);
				$objPlayer->save();
			}
		}
		$activenessNum = isset($formVals['activeness_num']) ? intval($formVals['activeness_num']) : 0; //积分值
		if ($activenessNum) {
			foreach ($nickname as $val) {
				$objPlayer = new O_Player($val['id']);
				$objPlayer->Liveness()->incr($activenessNum);
				$ret[$val['id']][] = $objPlayer->save();
			}
		}
		//装备
		if (isset($formVals['equip']) && is_array($formVals['equip']) && count($formVals['equip']) > 0) {
			foreach ($formVals['equip'] as $v) {
				foreach ($nickname as $val) {
					for ($i = 0; $i < $v['num']; $i++) {
						$locked = isset($v['is_locked']) ? $v['is_locked'] : 0;
						$tplInfo = M_Equip::baseInfo($v['id']);
						$ret[$val['id']][] = M_Equip::makeEquip($val['id'], $tplInfo);
					}
				}
				$tplAward['equip'][$v['id']] = $v['num'];
			}
		}
		//道具
		if (isset($formVals['props']) && is_array($formVals['props']) && count($formVals['props']) > 0) {
			foreach ($formVals['props'] as $v) {
				foreach ($nickname as $val) {
					$objPlayer = new O_Player($val['id']);
					$ret[$val['id']][] = $objPlayer->Pack()->incr($v['id'], $v['num']);
					$objPlayer->save();
				}
				$tplAward['props'][$v['id']] = $v['num'];
			}
		}

		if (isset($formVals['isMail']) && $formVals['isMail']) {
			$arr = M_Award::toText($tplAward);
			//Logger::debug('---------------------'.json_encode($tplAward));
			if ($arr) {
				$cententArr = array();
				foreach ($arr as $val) {
					if (!empty($val)) {
						$var = array(T_Lang::C_AWARD_MOD_NUM, array($val[2]), $val[3]);
						array_push($cententArr, $var);
					}
				}
			}
			$content = array(T_Lang::C_AWARD_MESSAGE, $cententArr);

			foreach ($nickname as $val) {
				M_Message::sendSysMessage($val['id'], json_encode(array(T_Lang::T_SYS_TIP)), json_encode($content));
			}
		}


		return $ret;
	}

	/** 置军官空闲 */
	static public function FreeHero($data) {
		$ret = false;
		$heroId = isset($data['id']) ? intval($data['id']) : 0;
		if ($heroId > 0) {
			$ret = M_Hero::setHeroInfo($heroId, array('flag' => T_Hero::FLAG_FREE));
		}
		return $ret;
	}

	/** 行军撤回 */
	static public function MarchBack($data) {
		$ret = false;
		$marchId = isset($data['id']) ? intval($data['id']) : 0;
		if ($marchId > 0) {
			$now = time();
			$awardArr = array();
			$marchInfo = M_March_Info::get($marchId);

			if (!empty($marchInfo['id'])) {
				$arrivedTime = $now + 60;

				$upData = array(
					'id' => $marchId,
					'action_type' => M_March::MARCH_ACTION_BACK,
					'flag' => M_March::MARCH_FLAG_MOVE,
					'award' => json_encode($awardArr),
					'arrived_time' => $arrivedTime,
					'battle_id' => 0,
				);

				$ret = M_March_Info::set($upData);

				if ($ret) {
					$syncData = array(
						'Id' => $marchInfo['id'],
						'AttCityId' => $marchInfo['atk_city_id'],
						'DefCityId' => $marchInfo['def_city_id'],
						'AttCityNickName' => $marchInfo['atk_nickname'],
						'DefCityNickName' => $marchInfo['def_nickname'],
						'ActionType' => $upData['action_type'],
						'HeroList' => json_decode($marchInfo['hero_list'], true),
						'AttPos' => M_MapWild::calcWildMapPosXYByNo($marchInfo['atk_pos']),
						'DefPos' => M_MapWild::calcWildMapPosXYByNo($marchInfo['def_pos']),
						'ArrivedTime' => $upData['arrived_time'],
						'RemainingTime' => max($upData['arrived_time'] - time(), 0),
						'WaitEndTime' => 0,
						'ResData' => !empty($marchInfo['award']) ? json_decode($marchInfo['award'], true) : array(),
						'Flag' => $upData['flag'],
						'CreateAt' => $marchInfo['create_at'],
						'BattleId' => $marchInfo['battle_id'],
					);

					$heroIdList = json_decode($marchInfo['hero_list'], true);
					$tmpIds = array();
					foreach ($heroIdList as $tmpId) {
						$tmpInfo = M_Hero::getHeroInfo($tmpId);
						if ($tmpInfo['flag'] != T_Hero::FLAG_DIE) {
							$tmpIds[] = $tmpId;
						}
					}

					$troop = M_Hero::changeHeroFlag($marchInfo['atk_city_id'], $tmpIds, T_Hero::FLAG_MOVE);
					M_Sync::addQueue($marchInfo['atk_city_id'], M_Sync::KEY_MARCH_DATA, array($marchId => $syncData));

					//删除城市行军记录
					$obj_ml = new M_March_List($marchInfo['def_pos']);
					$obj_ml->del($marchId);

					M_March::syncDelMarchBack($marchId, $marchInfo['def_city_id']);
				}
			}
		}
		return $ret;
	}

	/** 删除卡死的战斗 */
	static public function BattleDel($data) {
		$ret = false;
		$cityId = (isset($data['cityId']) && $data['cityId']) ? $data['cityId'] : 0;
		$battleId = (isset($data['battleId']) && $data['battleId']) ? $data['battleId'] : 0;
		if ($cityId > 0 && $battleId > 0) {
			$ret = M_Battle_List::delBattleIdByCity($cityId, $battleId);
		}
		return $ret;
	}

	/** 删除全部军官和装备缓存数据 */
	static public function DelHeroEquipAll($data) {
		$ret = false;
		$cityId = isset($data['city_id']) ? intval($data['city_id']) : 0;
		if ($cityId > 0) {
			$ret = true;
			$rc1 = new B_Cache_RC(T_Key::CITY_HERO_LIST, $cityId); //所有ID
			$arrHeroId = $rc1->smembers();
			foreach ($arrHeroId as $heroId) {
				$rc11 = new B_Cache_RC(T_Key::CITY_HERO_INFO, $heroId);
				$ret = $ret && $rc11->delete();
			}
			$ret = $ret && $rc1->delete();

			$rc2 = new B_Cache_RC(T_Key::CITY_EQUIP_LIST, $cityId); //所有ID
			$arrEquiId = $rc2->smembers();
			foreach ($arrEquiId as $equipId) {
				$rc21 = new B_Cache_RC(T_Key::CITY_EQUIP_INFO, $equipId);
				$ret = $ret && $rc21->delete();
			}
			$ret = $ret && $rc2->delete();

			//$rc3 = new B_Cache_RC(T_Key::CITY_ITEM_LIST, $cityId);	//所有物品ID
			//$rc3->delete();
		}
		return $ret;
	}

	/** 修改玩家名字 */
	static public function ModifyName($data) {
		$ret = false;
		$cityId = (isset($data['city_id']) && $data['city_id']) ? $data['city_id'] : 0;
		$newNickName = (isset($data['nickname']) && $data['nickname']) ? $data['nickname'] : '';
		if ($cityId > 0 && !empty($newNickName)) {
			$cityInfo = M_City::getInfo($cityId, true);
			$ret = M_City::setCityInfo($cityId, array('nickname' => $newNickName));
			B_DB::instance('City')->update(array('nickname' => $newNickName), $cityId);
			M_Sync::addQueue($cityId, M_Sync::KEY_CITY_INFO, array('nickname' => $newNickName)); //同步新名字
			M_City::upCityIdByNickName($cityInfo['nickname'], $newNickName, $cityId);
			if (intval($cityInfo['union_id']) > 0) {
				$unionInfo = M_Union::getInfo($cityInfo['union_id']);
				$unionMemberInfo = M_Union::getMemberInfo($cityInfo['union_id'], $cityInfo['id']);
				$arrUpd = array();
				($cityInfo['id'] == $unionInfo['create_city_id']) && $arrUpd['create_nick_name'] = $newNickName;
				(M_Union::UNION_MEMBER_TOP == intval($unionMemberInfo['position'])) && $arrUpd['boss'] = $newNickName;

				!empty($arrUpd) && M_Union::setInfo($cityInfo['union_id'], $arrUpd, true); //更新军团长[和创始人]名字
			}

			M_MapWild::syncWildMapBlockCache($cityInfo['pos_no']); //刷新此块地图数据
		}
		return $ret;
	}

	static public function SendMail($data) {
		$ret = false;
		$nickname = isset($data['nickname']) ? $data['nickname'] : '';
		$msg = isset($data['msg']) ? $data['msg'] : '';
		if ($nickname && $msg) {
			$cityId = M_City::getCityIdByNickName($nickname);
			if ($cityId) {
				$ret = M_Message::sendSysMessage($cityId, json_encode(array(T_Lang::T_SYS_TIP)), json_encode($msg));
			}
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

	static public function UnionModify($data) {
		$ret = false;
		$unionId = (isset($data['union_id']) && $data['union_id']) ? $data['union_id'] : 0;
		$newName = (isset($data['name']) && $data['name']) ? $data['name'] : '';
		if ($unionId > 0 && !empty($newName)) {
			$ret = M_Union::setInfo($unionId, array('name' => $newName));
		}
		return $ret;
	}

	static public function CityLevelLog($data) {
		$ret = false;
		$day = isset($data['day']) ? $data['day'] : '';
		$day = intval($day);
		if ($day) {
			$ret = M_Build::getCityLevelData($day);
		}
		return $ret;
	}

	static public function LastFbLog() {
		$ret = array();
		$chapterNo = 1;
		while ($chapterInfo = M_SoloFB::getInfo($chapterNo)) {
			$campaignNo = 1;
			while (isset($chapterInfo['fb_list'][$campaignNo])) {
				$pointNo = 1;
				while (isset($chapterInfo['fb_list'][$campaignNo]['checkpoint_data'][$pointNo])) {
					$fbNo = M_Formula::calcFBNo($chapterNo, $campaignNo, $pointNo);
					$num = B_DB::instance('City')->getPastFbPerson($fbNo);
					$ret[$chapterNo][$campaignNo][$pointNo] = $num;
					$pointNo++;
				}
				$campaignNo++;
			}
			$chapterNo++;
		}
		return $ret;
	}

	/**
	 * 获取时间段内的注册量
	 * @param array $data
	 */
	static public function CountPlayer($data) {
		if (isset($data['start']) && isset($data['end'])) {
			$consumer_id = isset($data['consumer_id']) ? $data['consumer_id'] : 0;
			$num = M_User::countUser(intval($data['start']), intval($data['end']), $consumer_id);
			return $num;
		}
		return false;
	}

	/**
	 * 系统消息
	 */
	static public function NoticeList($data) {
		$result = array();
		$page = isset($data['page']) ? $data['page'] : 1;
		$offset = isset($data['rows']) ? $data['rows'] : 20;
		$start = ($page - 1) * $offset;
		$result['list'] = B_DB::instance('ServerNotice')->getList($start, $offset);
		$result['total'] = B_DB::instance('ServerNotice')->count();
		return $result;
	}

	static public function NoticeDel($data) {
		$id = isset($data['id']) ? $data['id'] : 0;
		$result = M_Chat::delSysMsg($id);
		return $result;
	}

	static public function GetMsgInfo($data) {
		$result = array();
		$id = isset($data['id']) ? $data['id'] : 0;
		if ($id > 0) {
			$result = B_DB::instance('ServerNotice')->get($id);
		}
		return $result;
	}

	/**
	 * 添加系统消息
	 * @param array $data
	 */
	static public function AddSysMsg($data) {
		$ret = false;
		if (isset($data['title']) && $data['title']) {
			$ret = M_Chat::addSysMessage($data);
		}
		return $ret;
	}

	/**
	 * 修改
	 * @param array $data
	 */
	static public function SetSysMsg($data) {
		$ret = false;
		$id = isset($data['id']) ? $data['id'] : '';
		if ($id) {
			unset($data['id']);
			$ret = M_Chat::setSysMessage($id, $data);
		}
		return $ret;
	}

	static public function LoginTimes($parm) {
		$parm = isset($parm) ? $parm : array();
		$ret = array();
		foreach ($parm as $value) {
			$value['max'] = isset($value['max']) ? $value['max'] : 0;
			$ret[] = B_DB::instance('User')->loginTimes($value['min'], $value['max']);
		}
		return $ret;
	}

	/**
	 * 更改军官所属
	 * @param unknown_type $data
	 */
	static public function SetHeroCity($data) {
		$ret = false;
		$heroId = isset($data['heroId']) ? intval($data['heroId']) : 0;
		$cityId = isset($data['cityId']) ? intval($data['cityId']) : 0;
		if ($heroId && $cityId) {
			$heroInfo = M_Hero::getHeroInfo($heroId);
			if (isset($heroInfo['id']) && isset($heroInfo['city_id'])) {
				$ocityId = $heroInfo['city_id'];
				$ret = M_Hero::setHeroInfo($heroId, array('city_id' => $cityId));
				if ($ret) {
					M_Hero::delCityHeroList($ocityId, $heroId);
					M_Hero::setCityHeroList($cityId, $heroId);
				}
			}
		}
		return $ret;
	}

	/**
	 * 查看装备日志信息
	 * @param unknown_type $parms
	 */
	static public function LogEquip($parms) {
		$ret = false;
		$curPage = isset($parms['page']) ? $parms['page'] : 1;
		$offset = isset($parms['rows']) ? $parms['rows'] : 20;
		$cityId = isset($parms['filter']['city_id']) ? intval($parms['filter']['city_id']) : 0;
		if ($cityId) {
			//查看指定玩家日志前先同步文件里的日志到数据库
			$pack = $cityId % 1000;
			$path = LOG_PATH . '/info/equip/' . $pack . '/' . $cityId . '.log';
			M_Cron::doFile($path, 'equip');
		}
		$ret['list'] = B_DBStats::apiPageData('stats_log_equip', '*', $curPage, $offset, $parms['filter']);
		$ret['total'] = B_DBStats::totalRows('stats_log_equip', $parms['filter']);
		return $ret;
	}

	/**
	 * 查看英雄日志信息
	 * @param unknown_type $parms
	 */
	static public function LogHero($parms) {
		$ret = false;
		$curPage = isset($parms['page']) ? $parms['page'] : 1;
		$offset = isset($parms['rows']) ? $parms['rows'] : 20;
		$cityId = isset($parms['filter']['city_id']) ? intval($parms['filter']['city_id']) : 0;
		if ($cityId) {
			//查看指定玩家日志前先同步文件里的日志到数据库
			$pack = $cityId % 1000;
			$path = LOG_PATH  . '/info/hero/' . $pack . '/' . $cityId . '.log';
			M_Cron::doFile($path, 'hero');
		}
		$ret['list'] = B_DBStats::apiPageData('stats_log_hero', '*', $curPage, $offset, $parms['filter']);
		$ret['total'] = B_DBStats::totalRows('stats_log_hero', $parms['filter']);
		return $ret;
	}

	/**
	 * 查看道具日志信息
	 * @param unknown_type $parms
	 */
	static public function LogProps($parms) {
		$ret = false;
		$curPage = isset($parms['page']) ? $parms['page'] : 1;
		$offset = isset($parms['rows']) ? $parms['rows'] : 20;
		$cityId = isset($parms['filter']['city_id']) ? intval($parms['filter']['city_id']) : 0;
		if ($cityId) {
			//查看指定玩家日志前先同步文件里的日志到数据库
			$pack = $cityId % 1000;
			$path = LOG_PATH  . '/info/props/' . $pack . '/' . $cityId . '.log';
			M_Cron::doFile($path, 'props');
		}
		if (isset($parms['filter']['props_id'])) {
			$names = M_Props::getPropsIdName();
			$names = array_flip($names);
			$parms['filter']['props_id'] = $names[$parms['filter']['props_id']];
		}

		$list = B_DBStats::apiPageData('stats_log_props', '*', $curPage, $offset, $parms['filter']);
		$propsAll = M_Base::propsAll();
		foreach ($list as $k => $v) {
			$list[$k]['props_id'] = $propsAll[$v['props_id']]['name'];
		}
		$ret['list'] = $list;
		$ret['total'] = B_DBStats::totalRows('stats_log_props', $parms['filter']);
		return $ret;
	}

	/** 拍卖行使用统计 */
	static public function AucUse($parms) {
		$ret = false;
		$curPage = isset($parms['page']) ? $parms['page'] : 1;
		$offset = isset($parms['rows']) ? $parms['rows'] : 20;
		$nickName = isset($parms['filter']['nickname']) ? trim($parms['filter']['nickname']) : '';
		$cityId = intval(M_City::getCityIdByNickName($nickName));
		$title = isset($parms['filter']['title']) ? intval($parms['filter']['title']) : 0;

		unset($parms['filter']['nickname']);
		unset($parms['filter']['title']);
		$list = B_DB::instance('Auction')->getAllAucInfo($curPage, $offset, $cityId, $title, $parms['filter']);

		$ret['list'] = $list;
		$ret['total'] = B_DB::instance('Auction')->getAllAucInfoSum($cityId, $title, $parms['filter']);
		return $ret;
	}

	/**卡类道具使用统计 */
	static public function CardUse($parms) {
		$ret = false;
		$curPage = isset($parms['page']) ? $parms['page'] : 1;
		$offset = isset($parms['rows']) ? $parms['rows'] : 20;
		$params = isset($parms['filter']) ? $parms['filter'] : array();

		$list = B_DB::instance('CityCard')->getRows($curPage, $offset, $params);
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