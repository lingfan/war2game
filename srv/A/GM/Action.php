<?php

class A_GM_Action {
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

	/**
	 * 踢下线
	 * @author hejunyun
	 */
	static public function Kick($formVals) {
		$cityId = isset($formVals['city_id']) ? $formVals['city_id'] : 0;
		$cityInfo = M_City::getInfo($cityId);
		$userId = $cityInfo['user_id'];
		$ret = M_Client::del($userId);
		return $ret;
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
						'nickname' => $nickname
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

		$renown = isset($formVals['renown']) ? intval($formVals['renown']) : 0;
		$warexp = isset($formVals['mil_medal']) ? intval($formVals['mil_medal']) : 0;

		if ($renown || $warexp) {
			$renown && $tplAward['renown'] = $renown;
			$warexp && $tplAward['warexp'] = $warexp;
			$addItemArr = array(
				'renown' => $renown,
				'warexp' => $warexp
			);
			foreach ($nickname as $val) {
				$objPlayer = new O_Player($val['id']);
				$ret[$val['id']][] = $objPlayer->City()->addCityItem($addItemArr);
				$objPlayer->save();
			}
		}

		//装备
		if (isset($formVals['equip']) && is_array($formVals['equip']) && count($formVals['equip']) > 0) {
			foreach ($formVals['equip'] as $v) {
				foreach ($nickname as $val) {
					$tplInfo = M_Equip::baseInfo($v['id']);
					for ($i = 0; $i < $v['num']; $i++) {
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
}

?>