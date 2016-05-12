<?php

class M_Adm {
	static public function isLogin() {
		$info = self::getLoginInfo();
		if (!empty($info['id'])) {
			return $info;
		}
		return false;
	}

	static public function setLoginInfo($info) {
		$data      = array(
			'id'       => $info['id'],
			'username' => $info['username'],
		);
		$cookieStr = B_Crypt::encode(json_encode($data));

		$ret = setcookie('Adm', $cookieStr, 0);
	}

	static public function delLoginInfo() {
		$ret = setcookie('Adm', '', time() - 3600);
	}

	/**
	 * 解析登录cookie信息
	 * @author huwei
	 * @return array/bool
	 */
	static public function getLoginInfo() {
		$ret      = false;
		$args     = array(
			'Adm' => FILTER_SANITIZE_STRING,
		);
		$formVals = filter_input_array(INPUT_COOKIE, $args);

		if (!empty($formVals['Adm'])) {
			$decodeStr = B_Crypt::decode($formVals['Adm']);
			$now       = time();
			$info      = json_decode($decodeStr, true);
			$logout    = true;
			$userId    = 0;

			if (count($info) == 2 && isset($info['username'])) {
				$ret = $info;
			}
		}

		return $ret;
	}
}

?>