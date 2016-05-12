<?php

/**
 * 用户统计信息
 */
class A_Stats_User {
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
		$date = date('Y') . date('m') . (date('d'));
		$rc = new B_Cache_RC(T_Key::STATS_ONLINE_USER_NUM, $date);
		$data = $rc->smembers(); //取值
		return $data;
	}

	static public function LoginTimes($data) {
		$min = isset($data['min']) ? $data['min'] : 0;
		$max = isset($data['max']) ? $data['max'] : 0;
		$ret = B_DB::instance('User')->loginTimes($min, $max);
		return $ret;
	}

	/**
	 * 统计用户
	 * @param array $params
	 */
	static public function TotalUser($params) {
		$ym = isset($params['ym']) ? date('Ym', strtotime($params['ym'])) : date('Ym');
		$days = isset($params['ym']) ? date('t', strtotime($params['ym'])) : date('t');
		$start = strtotime($ym . '01000000');
		$end = strtotime($ym . $days . '235959');
		$consumer_id = isset($params['consumer_id']) ? $params['consumer_id'] : 0;

		$ret['list'] = B_DB::instance('User')->totalUser($start, $end, $consumer_id);
		$ret['total'] = B_DB::instance('User')->totalUserNum($consumer_id);
		return $ret;
	}

	static public function ping($formVals) {
		return $formVals;
	}

}

?>