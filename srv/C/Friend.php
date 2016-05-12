<?php

class C_Friend extends C_I {
	public function ALive($id = 0, $type = 1) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$yestoday = date('Ymd', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));

		$rc = new B_Cache_RC(T_Key::QQ_FRIEND_LIVE, $cityInfo['id'] . $yestoday);
		$liveNum = $rc->scard();

		$rc = new B_Cache_RC(T_Key::QQ_FRIEND_INVITE, $cityInfo['id']);
		$inviteNum = $rc->scard();

		$nowTime = time();
		$baseLive = M_Config::getVal('friend_live_award');
		$cityTask = M_Task::getCityTask($cityInfo['id']);
		$live = $cityTask['friend_live'];

		if (empty($live) || $live['day'] != date('Ymd')) {
			$live = array(
				'day' => date('Ymd'),
				'award' => array(),
			);
		}

		$errNo = T_ErrNo::SECTION_ONCE_PAY_EXPIRE;

		if ($id == 0) { //获取信息
			if (!empty($baseLive)) {
				foreach ($baseLive['data'] as $k => $val) {
					list($s, $e, $awardId) = $val;

					if ($liveNum >= $s && $liveNum <= $e && empty($cityLive['award'][$k])) {
						$cityLive['award'][$k] = 1;
					}
					$awardArr = M_Award::allResult($awardId);
					$award = M_Award::toText($awardArr, true);


					//[0未完成,1已完成未领取,2已领取]
					$flag = isset($live['award'][$k]) ? $live['award'][$k] : 0;
					$row[] = array($s, $e, $award, $flag);
				}
				$data['Start'] = $baseLive['start'];
				$data['End'] = $baseLive['end'];
				$data['List'] = $row;
				$data['LiveNum'] = $liveNum;
				$data['InviteNum'] = $inviteNum;

				$errNo = '';
			}
		} else if ($id == 1 && isset($baseLive['data'][$type])) { //领取奖励
			$errNo = T_ErrNo::SECTION_ONCE_PAY_NOT;
			$flag = isset($live['award'][$type]) ? $live['award'][$type] : 0;
			if ($flag == 1) {
				list($s, $e, $awardId) = $baseLive['data'][$type];

				$awardArr = M_Award::rateResult($awardId);
				$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task);
				$award = M_Award::toText($awardArr, true);


				$flag = 2;
				$live['award'][$type] = $flag;
				$upInfo = array(
					'friend_live' => json_encode($live),
				);
				M_Task::updateCityTask($cityInfo['id'], $upInfo);

				$data = array(
					'Flag' => $flag,
					'Award' => $award,
				);

				$errNo = '';
			}
		}


		return B_Common::result($errNo, $data);
	}

	public function AInvite($id = 0, $type = 1) {
		//操作结果默认为失败0
		$errNo = T_ErrNo::ERR_ACTION; //失败原因默认
		$data = array();

		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();


		$nowTime = time();
		$baseInvite = M_Config::getVal('friend_invite_award');
		$cityTask = M_Task::getCityTask($cityInfo['id']);
		$cityLive = $cityTask['friend_invite'];

		$rc = new B_Cache_RC(T_Key::QQ_FRIEND_INVITE, $cityInfo['id']);
		$inviteNum = $rc->scard();

		if (empty($cityLive)) {
			$cityLive = array(
				'award' => array(),
			);
		}

		if ($id == 0) { //获取信息
			if (!empty($baseInvite)) {
				foreach ($baseInvite['data'] as $k => $val) {
					list($num, $awardId) = $val;

					if ($inviteNum >= $num && empty($cityInvite['award'][$k])) {
						$cityInvite['award'][$k] = 1;
					}

					$awardArr = M_Award::allResult($awardId);
					$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task);
					$award = M_Award::toText($awardArr, true);

					//[0未完成,1已完成未领取,2已领取]
					$flag = isset($cityLive['award'][$k]) ? $cityLive['award'][$k] : 0;
					//$flag = rand(0,2);
					$row[] = array($num, $award, $flag, $inviteNum);
				}
				$data['Start'] = $baseInvite['start'];
				$data['End'] = $baseInvite['end'];
				$data['List'] = $row;
				$data['InviteNum'] = $inviteNum;

			}
		} else if (isset($baseInvite['data'][$type])) { //领取奖励
			$flag = isset($cityLive['award'][$type]) ? $cityLive['award'][$type] : 0;
			if ($flag == 1) {
				list($num, $awardId) = $baseInvite['data'][$type];

				$awardArr = M_Award::rateResult($awardId);
				$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task);
				$award = M_Award::toText($awardArr);


				$flag = 2;
				$cityLive['award'][$type] = $flag;
				$upInfo = array(
					'friend_invite' => json_encode($cityLive),
				);
				M_Task::updateCityTask($cityInfo['id'], $upInfo);

				$data = array(
					'Flag' => $flag,
					'Award' => $award,
				);
			}

		}
		$errNo = '';


		return B_Common::result($errNo, $data);
	}
}