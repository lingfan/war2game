<?php

/**
 * 聊天接口
 */
class C_Chat extends C_I {

	/**
	 * 发送消息
	 * @param int $type 聊天、广播 类型
	 * @param string $message
	 * @param string $id [战场ID,接收人昵称]
	 * @return array
	 */
	public function ASend($type, $message, $id = '') {

		$errNo = T_ErrNo::ERR_ACTION;
		$now = time();
		$data = array();
		//检查用户是否存在
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$jsData = json_decode($message, true);

		if (isset($jsData[0])) {
			if ($cityInfo['ban_talking'] < time()) {

				$jsData[0] = trim($jsData[0]) ? B_Utils::isBlockName($jsData[0], true) : $jsData[0];

				$message = json_encode($jsData);

				$timestamp = 0;
				//获取最后发送消息的时间
				$rc = new B_Cache_RC(T_Key::CHAT_LAST_SEND_TIME, $cityInfo['id']);
				if ($type != T_Chat::CHAT_CITY_RADIO) {
					$timestamp = $rc->get();
				}

				if (!$timestamp || $now - $timestamp > 2) //间隔时间3s
				{
					$rc1 = new B_Cache_RC(T_Key::CHAT_TMP_KEY, $cityInfo['id']);
					$tmpStr = md5($cityInfo['id'] . $message);
					$tmpStr2 = $rc1->get();
					if ($type != T_Chat::CHAT_CITY_RADIO && $tmpStr == $tmpStr2) {
						$errNo = T_ErrNo::CHAT_REPEAT;
					} else {
						$arr = array(
							T_Chat::CHAT_WORLD => 'addWorldMessage',
							T_Chat::CHAT_UNION => 'addUnionMessage',
							T_Chat::CHAT_WAR => 'addWarMessage',
							T_Chat::CHAT_OWNER => 'addOwnerMessage',
							T_Chat::CHAT_TEAM => 'addTeamMessage',
							T_Chat::CHAT_CITY_RADIO => 'addWorldMessage',
						);
						$nickName = $cityInfo['nickname'];

						if (isset($arr[$type])) {
							if ($type == T_Chat::CHAT_UNION) //获取联盟ID
							{
								$id = $cityInfo['union_id'];
								if (!$id) {
									$err = T_ErrNo::NOT_IN_UNION;
								}
							} elseif ($type == T_Chat::CHAT_OWNER) {
								$accepter = $id;
								$id = M_City::getCityIdByNickName($id); //私聊频道 获取接收人城市ID
								if (!$id) {
									$err = T_ErrNo::USER_NO_EXIST;
								}
								if ($id == $cityInfo['id']) {
									$err = T_ErrNo::THINK_ALOUD;
								}
							} elseif ($type == T_Chat::CHAT_WAR) {
								$battleData = M_Battle_List::getBattleIdByCity($cityInfo['id']);
								if (!isset($battleData[$id]) || $battleData[$id]['march_id'] < 1) {
									$err = T_ErrNo::NOT_IN_WARMAP;
								}
							} elseif ($type == T_Chat::CHAT_TEAM) {
								$teamId = 1;
								if (!$teamId) {
									$err = T_ErrNo::NOT_IN_TEAM;
								}
							} else if ($type == T_Chat::CHAT_CITY_RADIO) {
								if ($cityInfo['mil_pay'] < T_Chat::RADIO_COST) {
									$err = T_ErrNo::NO_ENOUGH_MILIPAY; //军饷不足
								} else {
									$isPay = $objPlayer->City()->decrCurrency(T_App::MILPAY, T_Chat::RADIO_COST, B_Log_Trade::E_Radio);
									if (!$isPay) {
										$err = T_ErrNo::NO_ENOUGH_MILIPAY; //军饷不足
									}
								}
							}

							if (!isset($err)) {
								$func = $arr[$type];
								if (!empty($message)) {
									if ($func == 'addWorldMessage') {
										$errNo = M_Chat::addWorldMessage($nickName, $message, $type);
									} else {
										$errNo = M_Chat::$func($id, $nickName, $message, $type);
									}

								}

								if ($errNo == '') {
									/* 缓存消息 不能连续发送同样的消息 CHAT_REPEAT  */
									$rc1->set($tmpStr, T_App::ONE_MINUTE);
									$rc->set($now, T_App::ONE_DAY);

									$errNo = '';
									//if ($type == T_Chat::CHAT_OWNER)
									//{
									$data = array(
										'Type' => $type,
										'Message' => $message
									);
									if (isset($accepter)) {
										$data['Accepter'] = $message;
									}
									//}


								}
							} else {
								$errNo = $err;
							}

						}
					}

				} else {
					$errNo = T_ErrNo::CHAT_FREQUENT;
				}
			} else {
				//已被禁言
				$errNo = T_ErrNo::BAN_TALKING;
			}
		}


		return B_Common::result($errNo, $data);

	}

	/**
	 * 接收消息
	 * @param int $battleId 战场ID
	 * @return array
	 */
	public function AReceive($battleId = 0) {
		//根据时间戳计算 之间的秒数   来获取对应的数据
		$now = time();
		$tmpBattleId = intval($battleId);

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$data = array();
		//所有的更新操作 进入队列
		M_Client::addCityVisitQueue($cityInfo['id']);

		//战场频道
		$warId = 0; //战场ID
		if ($tmpBattleId) {
			$battleData = M_Battle_List::getBattleIdByCity($cityInfo['id']);
			if (isset($battleData[$tmpBattleId])) {
				M_Battle_Calc::upViewOl($cityInfo['id'], $tmpBattleId);
				if ($battleData[$tmpBattleId]['march_id'] > 0) {
					//玩家之间城市战斗ID
					$warId = $tmpBattleId;
				}
			}
		}

		$teamId = 0; //队伍ID

		$list = M_Chat::getMsg($cityInfo['id']);

		foreach ($list as $val) {
			$info = json_decode($val, true);

			if ($info[0] == M_Chat::CHAN_WORD) {
				$data['worldData'][] = $info[2];
			} else if ($info[0] == M_Chat::CHAN_UNION && $info[1] == $cityInfo['union_id']) {
				$data['unionData'][] = $info[2];
			} else if ($info[0] == M_Chat::CHAN_WAR && $info[1] == $warId) {
				$data['warData'][] = $info[2];
			} else if ($info[0] == M_Chat::CHAN_TEAM && $info[1] == $teamId) {
				$data['teamData'][] = $info[2];
			} else if ($info[0] == M_Chat::CHAN_CITY && $info[1] == $cityInfo['id']) {
				$data['ownerData'][] = $info[2];
			}
		}

		$data['datetime'] = $now;
		$errNo = '';
		return B_Common::result($errNo, $data);
	}

}