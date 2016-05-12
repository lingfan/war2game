<?php

class C_Qq extends C_I {
	/** 对接QQ空间 */
	public function ALogin() {
		$args = array(
			'platform' => FILTER_SANITIZE_STRING,
			'serverid' => FILTER_SANITIZE_STRING,
			'openid' => FILTER_SANITIZE_STRING,
			'openkey' => FILTER_SANITIZE_STRING,
			'pfkey' => FILTER_SANITIZE_STRING,
			'sig' => FILTER_SANITIZE_STRING,

			'invkey' => FILTER_SANITIZE_STRING, //邀请好友加密串
			'itime' => FILTER_SANITIZE_STRING, //邀请时间
			'iopenid' => FILTER_SANITIZE_STRING, //发起邀请者的openid
			'app_custom' => FILTER_SANITIZE_STRING,
		);

		$formVals = filter_var_array($_REQUEST, $args);

		$server_id = $formVals['serverid'];

		$maintenance = M_Config::getSvrCfg('maintenance');
		$now = time();
		$m_start = strtotime($maintenance['start']);
		$m_end = strtotime($maintenance['end']);
		if (($m_start <= $m_end) && ($m_start < $now) && ($m_end > $now)) {
			header('Content-Type:text/html;charset=utf-8');
			echo $maintenance['msg'];
			exit;
		}


		$basecfg = M_Config::getVal();
		$appid = $basecfg['appid'];
		$appkey = $basecfg['appkey'];
		$openid = $formVals['openid'];
		$openkey = $formVals['openkey'];
		$pf = $formVals['platform'];

		$params = array(
			'openid' => $openid,
			'openkey' => $openkey,
			'pf' => $pf,
		);
		$qqUserData = M_Qq::instance()->api('/v3/user/get_info', $params);

		if ($qqUserData['ret'] != 0) {
			if ($qqUserData['ret'] == -12) {
				$qqTip = M_Config::getSvrCfg('qqserverip');
				//echo "服务器维护中，具体开服时间请留意论坛，对您造成的不便敬请谅解！";
				echo !empty($qqTip['msg']) ? $qqTip['msg'] : '';
				exit;
			}
			//检测QQ账号是否正常
			echo 'qqerr_' . $qqUserData['ret'];
			Logger::qq(array(__METHOD__, 'Msg' => 'Error get_info', 'Data' => $qqUserData, 'Params' => $params));
			exit;
		}

		//检查是否黄钻等
		$vipParams = array(
			'openid' => $openid,
			'openkey' => $openkey,
			'pf' => $pf,
			'sig' => $formVals['sig'],
			// 				'member_vip'	=> 1,
			// 				'blue_vip'		=> 1,
			'yellow_vip' => 1,
			// 				'red_vip'		=> 1,
			// 				'green_vip'		=> 1,
			// 				'pink_vip'		=> 1,
			// 				'superqq'		=> 1,
		);
		$qqVipData = M_Qq::instance()->api('/v3/user/total_vip_info', $vipParams);
		if ($qqVipData['ret'] != 0) {
			//检测QQ账号各种钻是否正常
			echo 'qqviperr_' . $qqVipData['ret'];
			Logger::qq(array(__METHOD__, 'Msg' => 'Error total_vip_info', 'Data' => $qqVipData, 'Params' => $vipParams));
			exit;
		}

		$params['pfkey'] = $formVals['pfkey'];
		$params['sig'] = $formVals['sig'];


		//黄钻等各钻等级 新方式
		$params['is_yellow'] = isset($qqVipData['is_yellow']) ? $qqVipData['is_yellow'] : 0;
		$params['yellow_vip_level'] = isset($qqVipData['yellow_level']) ? $qqVipData['yellow_level'] : 0;
		$params['is_yellow_year_vip'] = isset($qqVipData['is_year_yellow']) ? $qqVipData['is_year_yellow'] : 0;
		$params['is_yellow_high_vip'] = isset($qqVipData['is_year_yellow']) ? $qqVipData['is_year_yellow'] : 0;
		$params['is_blue'] = isset($qqVipData['is_blue']) ? $qqVipData['is_blue'] : 0;
		$params['blue_vip_level'] = isset($qqVipData['blue_level']) ? $qqVipData['blue_level'] : 0;
		$params['is_blue_year_vip'] = isset($qqVipData['is_year_blue']) ? $qqVipData['is_year_blue'] : 0;
		$params['is_super_blue_vip'] = isset($qqVipData['is_year_blue']) ? $qqVipData['is_year_blue'] : 0;

		$newUsername = md5($formVals['openid'] . $server_id);

		//邀请
		$consumerInfo = M_Consumer::getByName('openqq');
		$userInfo = M_User::getInfoByUsername($newUsername);
		$userInfo = M_User::getInfo($userInfo['id']);

		$uid = 0;
		if ($userInfo) {
			if (isset($userInfo['ban_login_time']) && $userInfo['ban_login_time'] > time()) {
				echo 'ban_login_time';
				exit;
			} else {
				$uid = $userInfo['id'];
				$params['serverid'] = $server_id;
				M_Qq::addQQLive($uid, $params);
				$cityId = M_City::getCityIdByUserId($userInfo['id']);

				$loginArr = array(
					'user_id' => $userInfo['id'],
					'city_id' => $cityId,
					'consumer_id' => $userInfo['consumer_id'],
					'ip' => B_Utils::getIp(),
					'server_id' => $server_id,
				);

				M_Auth::delLoginCookie();


				//老用户登陆
				$ssid = M_Auth::setLoginCookie($loginArr);

				$upData = array(
					'id' => $userInfo['id'],
					'login_times' => $userInfo['login_times'] + 1,
					'last_visit_time' => time(),
					'last_visit_ip' => $loginArr['ip'],
				);
				M_User::updateInfo($upData);


				if (!empty($userInfo['invite_id'])) {
					$inviteId = $userInfo['invite_id'];
					$rc = new B_Cache_RC(T_Key::QQ_FRIEND_LIVE, $inviteId . date('Ymd'));
					$rc->sadd($userInfo['id']);
				}

				B_Common::redirect('/?ssid=' . $ssid);

			}
		} else {
			$ip = B_Utils::getIp();
			//创建新用户
			$isAdult = 0;
			$username_ext = $openid;
			$uid = M_User::create($consumerInfo['id'], $newUsername, $ip, $isAdult, $username_ext, $server_id, $inviteId);
			if ($uid > 0) {

				$params['serverid'] = $server_id;
				M_Qq::addQQLive($uid, $params);
				$loginArr = array(
					'user_id' => $uid,
					'city_id' => 0,
					'consumer_id' => $consumerInfo['id'],
					'ip' => $ip,
					'server_id' => $server_id,
				);

				M_Auth::delLoginCookie();


				$ssid = M_Auth::setLoginCookie($loginArr);

				if (!empty($inviteId)) {
					$rc = new B_Cache_RC(T_Key::QQ_FRIEND_LIVE, $inviteId . date('Ymd'));
					$rc->sadd($uid);
				}

				B_Common::redirect('/?ssid=' . $ssid);
			} else {
				//echo '初始化用户失败..';//初始化用户失败..
				echo -9;
				exit;
			}
		}
	}

	/**
	 * 获取黄钻只领取一次奖励
	 */
	public function AGetOnceAward() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$now = time();
		$sync = false;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$qqUserInfo = M_Qq::getQQLive($cityInfo['id']);

		if ($qqUserInfo) {
			$taskInfo = M_Task::getCityTask($cityInfo['id']);
			$hadAward = $awardId = -1;

			if ($qqUserInfo['pf'] == '3366') {
				if ($qqUserInfo['blue_vip_level'] > 0) {
					$hadAward = $taskInfo['blue_vip_one'];
					$awardId = M_Config::getVal('blue_vip_one');
					$upField = 'blue_vip_one';
				}
			} else {
				if ($qqUserInfo['yellow_vip_level'] > 0) {
					$hadAward = $taskInfo['yellow_vip_one'];
					$awardId = M_Config::getVal('yellow_vip_one');
					$upField = 'yellow_vip_one';
				}
			}

			if ($hadAward == 0 && $awardId > 0) {

				$awardArr = M_Award::rateResult($awardId);
				$awardTxt = M_Award::toText($awardArr);
				$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task); //奖励结果


				$data = array(
					'Award' => $awardTxt,
				);
				$upInfo = array($upField => 1);
				M_Task::updateCityTask($cityInfo['id'], $upInfo);
				$errNo = '';

			} else {
				$errNo = T_ErrNo::AWARD_HAD;
			}
		}


		return B_Common::result($errNo, $data);
	}

	/**
	 * 获取每天黄钻年会员奖励
	 */
	public function AGetYearAward() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$now = time();
		$sync = false;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();

		$userId = (int)$cityInfo['id'];
		$qqUserInfo = M_Qq::getQQLive($userId);

		if ($qqUserInfo) {
			$curDate = date('Ymd');
			$taskInfo = M_Task::getCityTask($cityInfo['id']);
			$hadAwardVal = '';
			$awardId = 0;
			if ($qqUserInfo['pf'] == '3366') {
				if ($qqUserInfo['is_blue_year_vip'] > 0) {
					$hadAwardVal = $taskInfo['blue_year_vip'];
					$awardId = M_Config::getVal('blue_year_vip');
					$upField = 'blue_year_vip';
				}
			} else {
				if ($qqUserInfo['is_yellow_year_vip'] > 0) {
					$hadAwardVal = $taskInfo['yellow_year_vip'];
					$awardId = M_Config::getVal('yellow_year_vip');
					$upField = 'yellow_year_vip';
				}
			}

			if (!empty($hadAwardVal)) {
				$info = explode('|', $hadAwardVal);
				if ($info[0] != $curDate) {
					$info[1] = 0;
				}
			} else {
				$info[0] = $curDate;
				$info[1] = 0;
			}

			if ($info[1] == 0 && $awardId > 0) {
				$awardArr = M_Award::rateResult($awardId);
				$awardTxt = M_Award::toText($awardArr);
				$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task); //奖励结果

				$data = array(
					'Award' => $awardTxt,
				);
				$info[0] = $curDate;
				$info[1] = 1;
				$upInfo = array($upField => implode('|', $info));
				M_Task::updateCityTask($cityInfo['id'], $upInfo);
				$errNo = '';

			} else {
				$errNo = T_ErrNo::AWARD_HAD;
			}
		}
		return B_Common::result($errNo, $data);
	}

	/**
	 * 获取每天黄钻等级奖励
	 */
	public function AGetLevelAward() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$now = time();
		$sync = false;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$userId = (int)$cityInfo['id'];
		$qqUserInfo = M_Qq::getQQLive($userId);

		if ($qqUserInfo) {
			$curDate = date('Ymd');
			$taskInfo = M_Task::getCityTask($cityInfo['id']);
			$hadAwardVal = '';
			$awardId = 0;
			if ($qqUserInfo['pf'] == '3366') {
				$lv = $qqUserInfo['blue_vip_level'];
				if ($lv > 0) {
					$hadAwardVal = $taskInfo['blue_vip_level'];
					$awardInfo = M_Config::getVal('blue_vip_level');
					$awardIds = json_decode($awardInfo, true);
					$awardId = isset($awardIds[$lv]) ? $awardIds[$lv] : 0;
					$upField = 'blue_vip_level';
				}
			} else {
				$lv = $qqUserInfo['yellow_vip_level'];
				if ($lv > 0) {
					$hadAwardVal = $taskInfo['yellow_vip_level'];
					$awardInfo = M_Config::getVal('yellow_vip_level');
					$awardIds = json_decode($awardInfo, true);
					$awardId = isset($awardIds[$lv]) ? $awardIds[$lv] : 0;
					$upField = 'yellow_vip_level';
				}
			}

			if (!empty($hadAwardVal)) {
				$info = explode('|', $hadAwardVal);
				if ($info[0] != $curDate) {
					$info[1] = 0;
				}
			} else {
				$info[0] = $curDate;
				$info[1] = 0;
			}

			if ($info[1] == 0 && $awardId > 0) {
				$awardArr = M_Award::rateResult($awardId);
				$awardTxt = M_Award::toText($awardArr);
				$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task); //奖励结果
				$data = array(
					'Award' => $awardTxt,
				);
				$info[0] = $curDate;
				$info[1] = 1;
				$upInfo = array($upField => implode('|', $info));
				M_Task::updateCityTask($cityInfo['id'], $upInfo);
				$errNo = '';

			} else {
				$errNo = T_ErrNo::AWARD_HAD;
			}
		}
		return B_Common::result($errNo, $data);
	}

	public function AAwardList() {

		$errNo = 0;
		$data = array();
		$now = time();
		$sync = false;
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$userId = (int)$cityInfo['id'];

		$qqInfo = M_Qq::getQQLive($userId);
		$curDate = date('Ymd');
		$taskInfo = M_Task::getCityTask($cityInfo['id']);
		$data['HadLevelAward'] = 0;
		$data['HadYearAward'] = 0;

		if ($qqInfo['pf'] == '3366') {
			$data['BlueVipLevel'] = isset($qqInfo['blue_vip_level']) ? (int)$qqInfo['blue_vip_level'] : 0;
			$data['IsBlueYearVip'] = isset($qqInfo['is_yellow_year_vip']) ? (int)$qqInfo['is_yellow_year_vip'] : 0;
			$data['IsSuperBlueVip'] = isset($qqInfo['is_super_blue_vip']) ? (int)$qqInfo['is_super_blue_vip'] : 0;

			if (!empty($taskInfo['blue_vip_level'])) {
				$info = explode('|', $taskInfo['blue_vip_level']);
				if ($info[0] == $curDate) {
					$data['HadLevelAward'] = (int)$info[1];
				}
			}

			if (!empty($taskInfo['blue_year_vip'])) {
				$info = explode('|', $taskInfo['blue_year_vip']);
				if ($info[0] == $curDate) {
					$data['HadYearAward'] = (int)$info[1];
				}
			}
			$data['HadOnceAward'] = (int)$taskInfo['blue_vip_one'];

			$awardInfo = M_Config::getVal('blue_vip_level');
			$YearAwardId = M_Config::getVal('blue_year_vip');
			$OnceAwardId = M_Config::getVal('blue_vip_one');
		} else {
			$data['YellowVipLevel'] = isset($qqInfo['yellow_vip_level']) ? (int)$qqInfo['yellow_vip_level'] : 0;
			$data['IsYellowYearVip'] = isset($qqInfo['is_yellow_year_vip']) ? (int)$qqInfo['is_yellow_year_vip'] : 0;
			$data['IsYellowHighVip'] = isset($qqInfo['is_yellow_high_vip']) ? (int)$qqInfo['is_yellow_high_vip'] : 0;

			if (!empty($taskInfo['yellow_vip_level'])) {
				$info = explode('|', $taskInfo['yellow_vip_level']);
				if ($info[0] == $curDate) {
					$data['HadLevelAward'] = (int)$info[1];
				}
			}

			if (!empty($taskInfo['yellow_year_vip'])) {
				$info = explode('|', $taskInfo['yellow_year_vip']);
				if ($info[0] == $curDate) {
					$data['HadYearAward'] = (int)$info[1];
				}
			}
			$data['HadOnceAward'] = (int)$taskInfo['yellow_vip_one'];

			$awardInfo = M_Config::getVal('yellow_vip_level');
			$YearAwardId = M_Config::getVal('yellow_year_vip');
			$OnceAwardId = M_Config::getVal('yellow_vip_one');
		}

		$levelAwardList = json_decode($awardInfo, true);
		$levelAwardInfo = array();
		foreach ($levelAwardList as $lv => $awardId) {
			$awardArr = M_Award::rateResult($awardId);
			$awardTxt = M_Award::toText($awardArr);
			$levelAwardInfo[$lv] = $awardTxt;
		}
		$data['LevelAward'] = $levelAwardInfo;


		$awardArr = M_Award::rateResult($YearAwardId);
		$awardTxt = M_Award::toText($awardArr);
		$data['YearAward'] = $awardTxt;

		$awardArr = M_Award::rateResult($OnceAwardId);
		$awardTxt = M_Award::toText($awardArr);
		$data['OnceAward'] = $awardTxt;

		$errNo = '';


		return B_Common::result($errNo, $data);
	}

	/**
	 * 邀请好友
	 */
	public function AInviteFriend() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$curDate = date('Ymd');

		$rc = new B_Cache_RC(T_Key::QQ_INVITE_NUM, (int)$cityInfo['id']);
		$InviteFriend = $rc->get();
		$info = explode('|', $InviteFriend);

		$hadInviteDate = isset($info[0]) ? $info[0] : $curDate;
		$hadInviteNum = isset($info[1]) ? $info[1] : 0;
		$hadInviteAward = isset($info[2]) ? $info[2] : 0;
		if ($hadInviteDate != $curDate) {
			$hadInviteNum = $hadInviteAward = 0;
			$hadInviteDate = $curDate;
		}

		$data['QQInviteFriendMax'] = M_Config::getVal('qq_invite_friend_num');
		$data['QQInviteFriendNum'] = $hadInviteNum;
		$data['QQHadInviteAward'] = $hadInviteAward;
		$awardId = M_Config::getVal('qq_invite_friend_award');

		$awardArr = M_Award::rateResult($awardId);
		$awardTxt = M_Award::toText($awardArr);

		$data['QQInviteFriendAward'] = $awardTxt;
		$errNo = '';

		return B_Common::result($errNo, $data);
	}

	/**
	 * 每天邀请好友次数
	 */
	public function AGetInviteAward() {

		$errNo = T_ErrNo::ERR_ACTION;
		$data = array();
		$now = time();
		$objPlayer = $this->objPlayer;
		$cityInfo = $objPlayer->getCityBase();
		$curDate = date('Ymd');
		$rc = new B_Cache_RC(T_Key::QQ_INVITE_NUM, (int)$cityInfo['id']);
		$InviteFriend = $rc->get();
		$info = explode('|', $InviteFriend);

		$hadInviteDate = isset($info[0]) ? $info[0] : $curDate;
		$hadInviteNum = isset($info[1]) ? $info[1] : 0;
		$hadInviteAward = isset($info[2]) ? $info[2] : 0;
		if ($hadInviteDate != $curDate) {
			$hadInviteNum = $hadInviteAward = 0;
			$hadInviteDate = $curDate;
		}

		if ($hadInviteAward == 0) {
			$max = M_Config::getVal('qq_invite_friend_num');
			if ($hadInviteNum >= $max) {
				$InviteFriendAwardId = M_Config::getVal('qq_invite_friend_award');

				$awardArr = M_Award::rateResult($InviteFriendAwardId);
				$awardTxt = M_Award::toText($awardArr);
				$bAward = $objPlayer->City()->toAward($awardArr, B_Log_Trade::I_Task); //奖励结果


				$data['Award'] = $awardTxt;

				$errNo = '';
				$inviteVal = implode('|', array($hadInviteDate, $hadInviteNum, 1));
				$rc->set($inviteVal, T_App::ONE_DAY);
			}
		}
		return B_Common::result($errNo, $data);
	}
}

?>