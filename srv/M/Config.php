<?php

/**
 *
 * 系统配置模块
 * @author william.hu
 * @version 2010/10/12
 */
class M_Config {

	/**
	 * 更新基础配置信息
	 * @author chenhui on 20111115
	 * @param string $name 配置的名字
	 * @param array $info 配置的内容array
	 */
	static public function setVal($data) {
		$ret      = false;
		$baselist = B_DB::instance('BaseConfig')->all();

		foreach ($data as $key => $val) {
			if (!isset($baselist[$key])) {
				$ret = B_DB::instance('BaseConfig')->add($key, json_encode($val));
			}
			$ret = B_DB::instance('BaseConfig')->set($key, json_encode($val));
			if ($key == 'help_detail') {
				var_dump(isset($baselist[$key]), $data['help_detail'], $ret);
			}


		}

		self::delVal();
		return $ret;
	}

	static public function delVal() {
		APC::del(T_Key::BASE_CONFIG_LIST);
	}

	/**
	 * 获取公共配置信息
	 * @author HeJunyun
	 * @param string $name 配置列表KEY
	 * @return 配置[$name]存在，则返回对应的value，否则返回所有信息
	 */
	static public function getVal($name = '') {
		static $info = null;
		if (is_null($info)) {
			$mcKey = T_Key::BASE_CONFIG_LIST;
			$info  = B_Cache_APC::get($mcKey);

			if (empty($info)) {
				$info = B_DB::instance('BaseConfig')->all();
				if (!empty($info)) {
					$ret = B_Cache_APC::set($mcKey, $info);

				}
			}
		}

		if (!empty($name)) {
			$ret = isset($info[$name]) ? $info[$name] : false;
		} else {
			$ret = $info;
		}
		return $ret;
	}


	/** 获取每服单独配置信息 */
	static public function getSvrCfg($name = '') {
		static $info = null;
		if (is_null($info)) {
			$mcKey = T_Key::SERVER_CONFIG;
			$info  = B_Cache_APC::get($mcKey);
			if (empty($info)) {
				$info = B_DB::instance('ServerConfig')->all();

				if (!empty($info)) {
					$ret = B_Cache_APC::set($mcKey, $info);
				}
			}
		}

		if (!empty($name)) {
			$ret = isset($info[$name]) ? $info[$name] : false;
		} else {
			$ret = $info;
		}

		return $ret;
	}

	/**
	 * 更新基础配置信息
	 * @author chenhui on 20111115
	 * @param string $name 配置的名字
	 * @param array $info 配置的内容array
	 */
	static public function setSvrCfg($data) {
		$baselist = B_DB::instance('ServerConfig')->all();
		foreach ($data as $key => $val) {
			if (!isset($baselist[$key])) {
				$ret = B_DB::instance('ServerConfig')->add($key, json_encode($val));
			}
			$ret = B_DB::instance('ServerConfig')->set($key, json_encode($val));
		}

		self::cleanSvrCfg();
		return $ret;
	}

	static public function cleanSvrCfg() {
		return B_Cache_APC::del(T_Key::SERVER_CONFIG);
	}


}

?>