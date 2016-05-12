<?php

/**
 * 行军信息模块
 */
class M_March_Info {
	/**
	 * 获取行军数据
	 * @author huwei on 20110927
	 * @param int $id 行军Id
	 * @return array
	 */
	static public function get($id) {
		$ret = array();
		$id  = intval($id);
		if ($id > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_WAR_MARCH_INFO, $id);
			$ret = $rc->hgetall();
			if (empty($ret['id'])) {
				$dbInfo = B_DB::instance('WarMarch')->getInfo($id);
				if (self::set($dbInfo, false)) {
					$ret = $dbInfo;
				}
			}
		}
		return $ret;
	}

	/**
	 * 更新城市英雄信息
	 * @author huwei on 2011108
	 * @param array $upData 需要更新的数据字段数组
	 * @param bool $upDB 是否更新到DB
	 * @return bool
	 */
	static public function set($upData, $upDB = true) {
		$ret = false;
		if (!empty($upData['id'])) {
			$id   = $upData['id'];
			$info = array();
			foreach ($upData as $key => $val) {
				if (!empty($key) && in_array($key, T_DBField::$marchFields)) {
					$info[$key] = $val;
				}
			}

			if (!empty($info)) {
				$rc  = new B_Cache_RC(T_Key::CITY_WAR_MARCH_INFO, $id);
				$ret = $rc->hmset($info, T_App::ONE_DAY);
				if ($ret) {
					$upDB && M_CacheToDB::addQueue(T_Key::CITY_WAR_MARCH_INFO . ':' . $id);
				} else {
					$msg = array(__METHOD__, 'Update Redis Of March Info Fail', func_get_args());
					Logger::error($msg);
				}
			}
		}
		return $ret;
	}

	/**
	 * 删除城市英雄信息key
	 * @author huwei on 2011108
	 * @param int $id 英雄ID
	 * @return bool
	 */
	static public function del($id) {
		$ret = true;
		$rc  = new B_Cache_RC(T_Key::CITY_WAR_MARCH_INFO, $id);
		$ret = $rc->delete();

		if ($ret) {
			B_DB::instance('WarMarch')->delete($id);
		} else {
			$msg = array(__METHOD__, 'Del March Data Fail', $id);
			Logger::error($msg);
		}
		return $ret;
	}

}

?>