<?php

class M_Qq {
	static $qqPf = array(
		'qzone'   => 1,
		'pengyou' => 2,
		'tapp'    => 3,
		'qplus'   => 4,
		'qqgame'  => 10,
		'3366'    => 11,
	);
	static $qqserverip = array('119.147.19.43', 'openapi.tencentyun.com');
	/** 默认黄钻等级 */
	const YELLOW_VIP_LEVEL = 0;
	/** 是否是年费黄钻 */
	const IS_YELLOW_YEAR_VIP = 0;
	const IS_YELLOW_HIGH_VIP = 0;

	/** 保持QQ在线 */
	static public function keepQQLive($userId) {
		if (!empty($userId)) {
			$now  = time();
			$info = M_Qq::getQQLive($userId);
			if (!empty($info) && $info['expire'] < $now) {

				$params     = array(
					'appid'   => $info['appid'],
					'openid'  => $info['openid'],
					'openkey' => $info['openkey'],
					'pf'      => $info['pf'],
					'sig'     => $info['sig'],
				);
				$qqUserData = M_Qq::instance()->api('/v3/user/is_login', $params);
				if ($qqUserData['ret'] != 0) {
					Logger::qq(array(__METHOD__, "QQ_KEEP_LIVE_ERROR#[{$userId}]", array($info, $qqUserData)));
				}
				$info['expire'] = $now + T_App::ONE_HOUR * 0.5;
				$rc             = new B_Cache_RC(T_Key::QQ_KEEP_LIVE, $userId);
				$rc->jsonset($info, T_App::ONE_DAY);
				return json_encode($qqUserData);
			}
		}
		return 0;
	}

	/** 添加QQ在线 */
	static public function addQQLive($userId, $params) {
		if (!empty($userId)) {
			/** 保持QQ登录 防游戏掉线 */
			$params['expire'] = time() + T_App::ONE_HOUR * 0.5;

			$rc  = new B_Cache_RC(T_Key::QQ_KEEP_LIVE, $userId);
			$ret = $rc->jsonset($params, T_App::ONE_DAY);
		}
	}

	static public function getQQLive($userId) {
		$rc   = new B_Cache_RC(T_Key::QQ_KEEP_LIVE, $userId);
		$info = $rc->jsonget();
		return $info;
	}

	static public function qqApiIp() {
		$now        = time();
		$qqserver   = M_Config::getSvrCfg('qqserverip');
		$qqStart    = strtotime($qqserver['start']);
		$qqClose    = strtotime($qqserver['end']);
		$qqserverip = M_Qq::$qqserverip[1];
		if (($qqStart < $now) && ($qqClose > $now)) {
			$qqserverip = M_Qq::$qqserverip[0];
		}

		return $qqserverip;
	}


	static public function instance() {
		static $sdkObj = null;
		if ($sdkObj == null) {
			require_once LIB_PATH . '/OpenApiV3.php';
			$basecfg = M_Config::getVal();
			$appid   = $basecfg['appid'];
			$appkey  = $basecfg['appkey'];

			$sdk     = new OpenApiV3($appid, $appkey);
			$qqApiIp = M_Qq::qqApiIp();
			$sdk->setServerName($qqApiIp);

			$sdkObj = $sdk;
		}
		return $sdkObj;
	}

	static public function verifyFriendInvite($params) {
		$inviteId = 0;

		//邀请逻辑
		if (!empty($params['iopenid'])) {
			$friendParams = array(
				'openid'  => $params['openid'],
				'openkey' => $params['openkey'],
				'pf'      => $params['pf'],
				'sig'     => $params['sig'],
				'invkey'  => $params['invkey'],
				'itime'   => $params['itime'],
				'iopenid' => $params['iopenid'],
			);

			$qqFriendData = M_Qq::instance()->api('/v3/spread/verify_invkey', $friendParams);
			Logger::qq(array(__METHOD__, 'invate friend params', $friendParams, $qqFriendData, $params['app_custom']));

			if ($qqFriendData['ret'] == 0 && $qqFriendData['is_right'] == 1) {
				$inviteUsername = md5($params['iopenid'] . $params['sid']);
				$inviteUserInfo = M_User::getInfoByUsername($inviteUsername);
				if ($inviteUserInfo['id']) {
					$inviteId = $inviteUserInfo['id'];

					$rc = new B_Cache_RC(T_Key::QQ_FRIEND_INVITE, $inviteId);
					$rc->sadd($params['openid']);
				}

				Logger::qq(array(__METHOD__, 'set invate friend', $params['openid'], $params['iopenid'], $inviteId));
			} else {
				Logger::qq(array(__METHOD__, 'Msg' => 'Error verify_invkey', 'Data' => $qqFriendData, 'Params' => $friendParams));
			}
		}
	}
}

?>