<?php

class B_Cache_File {
	static private $_conf = null;

	static public function get($name) {

		if (empty(self::$_conf)) {
			$key = T_Key::CONFIG_FILE . 'CFG';
			$result = B_Cache_APC::get($key);

			if (empty($result)) {
				$serverId = B_Cache_File::server(SERVER_NO);
				$file = ETC_PATH . '/config.php';


				if (file_exists($file)) {
					$result = require $file;

					//APC::set($key, $result);
				} else {
					trigger_error("don't found {$file} file");
					Logger::halt('Err_conf:' . $name);
				}


			}

			self::$_conf = $result;
		}

		$ret = isset(self::$_conf[$name]) ? self::$_conf[$name] : array();
		return $ret;
	}

	static public function load($name) {
		static $list = null;
		if (!isset($list[$name])) {
			$key = T_Key::CONFIG_FILE . $name;
			$info = B_Cache_APC::get($key);
			if (empty($info)) {
				$file = ETC_PATH . '/' . ETC_NO . "/{$name}.php";
				if (file_exists($file)) {
					$info = require $file;
					APC::set($key, $info);
				} else {
					$msg = "don't found {$file}";
					trigger_error("don't found {$file}");
					Logger::halt("Err_{$file}");
				}
			}
			$list[$name] = $info;
		}
		return $list[$name];
	}

	/**
	 * 服务编号
	 * @param $serverId
	 * @return mixed
	 */
	static public function server($serverId) {
		return $serverId;
	}
}
