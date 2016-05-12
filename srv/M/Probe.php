<?php

/**
 * 探索模块
 */
class M_Probe {
	static public function getAll() {
		static $info = null;
		if (is_null($info)) {
			$apcKey = T_Key::BASE_PROBE;
			$info   = B_Cache_APC::get($apcKey);
			if (empty($info)) {
				$info = B_DB::instance('BaseProbe')->all();
				Logger::base(__METHOD__);
				APC::set($apcKey, $info);
			}
		}
		return $info;
	}

	static public function getInfo($probeId) {
		$arr = M_Base::probeAll();
		return isset($arr[$probeId]) ? $arr[$probeId] : false;
	}
}

?>