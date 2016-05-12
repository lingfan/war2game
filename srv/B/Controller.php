<?php

class B_Controller {
	static $CID = 0;
	static $RetErr = T_ErrNo::ERR_ACTION;
	static $Data = array();

	static public function getCID() {
		self::$CID = M_Auth::myCid();
		return self::$CID;
	}

	/**
	 *
	 * 返回结果控制
	 * @author huwei
	 * @param int $flag 成功0 失败1 其他数字表示其他状态
	 * @param array $data 数据
	 * @return array
	 */
	static public function result() {
		if (self::$CID) { //获取队列信息
			$syncData = M_Sync::getQueue(self::$CID);
			if (!empty($syncData)) {
				$data['sync'] = $syncData;
			}
		}

		$ret['flag'] = self::$RetErr ? T_App::FAIL : T_App::SUCC;
		$ret['data'] = array(
			'Data' => self::$Data,
			'ErrNo' => self::$RetErr,
		);

		return $ret;
	}

	static public function call($args) {
		$tc = ucfirst($args[0]);
		$ta = ucfirst($args[1]);
		$c = 'C_' . $tc;
		$a = 'A' . $ta;
		unset($args[0]);
		unset($args[1]);

		$data = array();

		$objC = new $c();
		if (!$objC->isLogin()) {
			$errNo = T_ErrNo::NO_LOGIN;
			$outData = B_Common::result($errNo, $data);
		} else if ($objC->hasCity() || $c == 'C_User' || $a == 'ACreate' || $a == 'ARandName' || $a == 'ACheckCityNickname') {
			$outData = call_user_func_array(array($objC, $a), $args);
		} else {
			$errNo = T_ErrNo::CITY_NO_EXIST;
			$outData = B_Common::result($errNo, $data);
		}

		$results = B_Common::outData($c, $a, $outData);

		return $results;
	}
}

?>