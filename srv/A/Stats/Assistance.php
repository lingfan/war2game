<?php

/**
 * 周活跃和月活跃用户统计
 */
class A_Stats_Assistance {
	static public function AssistanceList($params = array()) {
		$listArr = array();
		static $list = array();
		$info = array();
		if (empty($list)) {
			$rc = new B_Cache_RC(T_Key::SERVER_NEWS);
			$infoArr = $rc->jsonget();
			if (empty($infoArr)) {
				$infoArr = B_DB::instance('ServerNews')->all();
				$rc->jsonset($infoArr);
			}
			$listArr = $infoArr;
		}


		return $listArr;
	}

	static public function AddAssistance($params = array()) {
		$ret = false;
		$ret1 = false;
		if (!empty($params['id'])) {
			$ret = B_DB::instance('ServerNews')->update($params, $params['id']);
		} else {
			$ret = B_DB::instance('ServerNews')->insert($params);
		}
		$rc = new B_Cache_RC(T_Key::SERVER_NEWS);
		$ret1 = $rc->delete();
		return $ret && $ret1;

	}

	static public function DelAssistance($params = array()) {
		$ret = false;
		$ret1 = false;
		$ret = B_DB::instance('ServerNews')->delete($params['id']);
		$rc = new B_Cache_RC(T_Key::SERVER_NEWS);
		$ret1 = $rc->delete();
		return $ret && $ret1;
	}
}

?>