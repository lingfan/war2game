<?php

/**
 *
 * 用户认证
 * @author Administrator
 *
 */
class M_Auth {
	const COOKIE_KEY = 'SKAXJFL';

	/**
	 * 解析登录cookie信息
	 * @author huwei
	 * @return array/bool
	 */
	static public function getLoginCookie() {
		$ret = false;

		$ssid = filter_input(INPUT_GET, 'ssid', FILTER_SANITIZE_STRING);
		if (!empty($ssid)) {
			if (DEV_ENV && strlen($ssid) < 10) {
				$cityId = M_User::getIdByUsername($ssid, 1, 1);
				$ret    = array('city_id' => $cityId);
			} else {
				$tmp = explode('_', $ssid);
				if (count($tmp) == 2) {
					$cid  = $tmp[0];
					$info = M_Client::get($cid);
					if (!empty($info['sess_id']) && $info['sess_id'] == $ssid) {
						$ret = array('city_id' => $cid);
					}
				}
			}
		}
		return $ret;
	}

	/**
	 *
	 * 把用户信息写入cookie
	 * @return array/bool
	 */
	static public function setLoginCookie($consumerId, $serverId, $cityId) {
		$ret = false;
		if (!empty($cityId)) {
			$n    = rand(100, 999);
			$ssid = $cityId . '_' . substr(md5($cityId . $n . M_Auth::COOKIE_KEY), 0, 8);
			M_Auth::loginStats($cityId);
			M_Client::add($consumerId, $serverId, $cityId, $ssid);
			$ret = $ssid;
		}
		return $ret;
	}

	static public function loginStats($cityId) {
		if ($cityId > 0) {
			$rc = new B_Cache_RC(T_Key::CITY_ACTIVE_NUM, date('Ymd'));
			$rc->hincrby($cityId, 1);

			//防沉迷相关
			$objPlayer = new O_Player($cityId);
			$objPlayer->City()->upLastLogin();
		}
	}

	/**
	 *
	 * 清除cookie
	 * @return bool
	 */
	static public function delLoginCookie() {
		$ret  = false;
		$info = M_Auth::getLoginCookie();
		if (!empty($info['city_id'])) {
			M_Client::del($info['city_id']);
		}
		return $ret;
	}


	/**
	 * 玩家调用接口次数控制
	 * @author huwei on 20111031
	 * @param int $uid
	 * @param string $callFunc
	 * @return int
	 */
	static public function callTimes($uid, $callFunc) {
		$data = array();
		$now  = time();
		$num  = 0;
		if (!empty($uid)) {
			$rc = new B_Cache_RC(T_Key::USER_CALL_TIMES, $uid);
			if ($rc->exists()) {
				$data = $rc->hmget(array($callFunc));
				if (!empty($data[$callFunc])) {
					list($t, $num) = explode('|', $data[$callFunc]);
					if ($now == $t) {
						$num++;
					} else {
						$num = 1;
					}
				} else {
					$num = 1;
				}
				$rc->hmset(array($callFunc => $now . '|' . $num), T_App::ONE_DAY);
			} else {
				$num = 1;
				$rc->hmset(array($callFunc => $now . '|' . $num), T_App::ONE_DAY);
			}
		}
		$ret = $num;

		return $ret;
	}


	static public function myCid() {
		$info = M_Auth::getLoginCookie();
		return !empty($info['city_id']) ? $info['city_id'] : 0;
	}


	static public function render($name, $consumerId, $serverId) {
		$cityId = M_User::getIdByUsername($name, $consumerId, $serverId);
		$ssid   = M_Auth::setLoginCookie($consumerId, $serverId, $cityId);
		$pageData['server_res_url'] = M_Config::getSvrCfg('server_res_url');
		$pageData['server_title']   = M_Config::getSvrCfg('server_title');
		$pageData['sid']            = $serverId;

		$pageData['domain'] = B_Utils::getHost();

		$pageData['ssid'] = $ssid;

		$qqInfo              = M_Qq::getQQLive($cityId);
		$pageData['uid']     = $cityId;
		$pageData['appid']   = $qqInfo['appid'];
		$pageData['pf']      = $qqInfo['pf'];
		$qqApiIp             = M_Qq::qqApiIp();
		$pageData['sandbox'] = ($qqApiIp == M_Qq::$qqserverip[0]) ? 1 : 0;

		header('Content-Type:text/html;charset=utf-8');
		B_View::render('index', $pageData);
	}
}

?>