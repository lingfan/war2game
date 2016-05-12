<?php

/**
 * 城市学院模块
 */
class M_CityCollege {

	static $cityCollegeFields = array(
		'city_id', 'hero_tpl_id', 'hire_time', 'succ_rate', 'time_props_id',
		'rate_props_id', 'is_pay', 'start_time', 'end_time', 'succ_keep_time',
		'flag', 'last_find_time', 'cd_time', 'find_num', 'refresh_time', 'hero_list'
	);

	static public function setData($cityId, $data, $upDB = true) {
		$ret = false;
		if (!empty($cityId) && is_array($data) && !empty($data)) {
			$info = array();
			foreach ($data as $key => $val) {
				if (!empty($key) && in_array($key, self::$cityCollegeFields)) {
					$info[$key] = $val;
				}
			}

			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_COLLEGE, $cityId);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$upDB && M_CacheToDB::addQueue(T_Key::CITY_COLLEGE . ':' . $cityId);
				}
			}

			if (!$ret) {
				$msg = array(__METHOD__, 'Set Data Fail', func_get_args());
				Logger::error($msg);
			}

		}
		return $ret ? $info : false;
	}

	static public function getData($cityId) {
		$cityId = intval($cityId);
		$ret    = false;
		if ($cityId > 0) {
			$rc = new B_Cache_RC(T_Key::CITY_COLLEGE, $cityId);
			if ($rc->exists()) {
				$ret = $rc->hmget(self::$cityCollegeFields);
			} else {
				$data = B_DB::instance('CityCollege')->get($cityId);
				if (empty($data)) {
					//初始化学院记录
					$upInfo = array(
						'city_id'  => $cityId,
						'find_num' => 0,
					);
					$ret    = B_DB::instance('CityCollege')->insert($upInfo);
					$data   = B_DB::instance('CityCollege')->get($cityId);
				}
				if (!empty($data)) {
					$ret = self::setData($cityId, $data);
				}
			}
		}
		return $ret;
	}

	static public function getHeroList($cityId) {
		$rc   = new B_Cache_RC(T_Key::CITY_COLLEGE, $cityId);
		$data = $rc->hmget(array('hero_list', 'refresh_time'));
		return $data;
	}

	static public function delData($cityId) {

	}

}