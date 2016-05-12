<?php

class M_Card {
	/**
	 * 是否重复使用类型
	 */
	const REPEAT_TYPE = 2;


	/** 卡类型 */
	static $cardTypes = array(
		1 => '新手卡',
		2 => '媒体卡',
		3 => '公会卡',
		4 => '特殊卡',
		5 => '其它卡',
		6 => '无限卡',
	);

	/** 可无限领取的卡 */
	static $infiniteTypes = array(
		2 => '无限卡',
	);

	/** 可重复领取的卡 */
	static $infiniteProps = array();


	/**
	 * 分解卡号
	 * @author huwei
	 * @param string $code
	 * @return array()  [idx, type, pid]
	 */
	static public function decrypt($code, $pwd = '') {
		$ret = false;
		if (!empty($code)) {
			$code = trim($code);
			if (empty($pwd)) {
				$pwd = M_Config::getSvrCfg('city_card_pwd');
			}

			$str = base64_decode($code);

			$arr = unpack('Nidx/Ctype/npid/C*', $str);

			$newArr = array(
				'idx'  => $arr['idx'],
				'type' => $arr['type'],
				'pid'  => $arr['pid'],
			);

			unset($arr['idx'], $arr['type'], $arr['pid']);
			$v = array_values($arr);
			array_unshift($v, "C*");
			$hash = call_user_func_array("pack", $v);

			$tmpBin = pack('NCn', $newArr['idx'], $newArr['type'], $newArr['pid']);
			$verify = substr(md5($tmpBin . $pwd), 0, 14);

			if ($hash == $verify) {
				$ret = $newArr;
			}
		}
		return $ret;
	}

	/**
	 * 加密卡号
	 * @author huwei
	 * @param int $idx
	 * @param int $type
	 * @param string $pid
	 * @return string
	 */
	static public function encrypt($idx, $type, $propsId, $pwd) {
		$code = '';
		$pwd  = trim($pwd);
		if ($idx && $type && $propsId && $pwd) {
			$tmpBin = pack('NCn', $idx, $type, $propsId);
			$hash   = substr(md5($tmpBin . $pwd), 0, 14);
			$code   = base64_encode($tmpBin . $hash);
		}
		return $code;
	}

	/**
	 * 临时记录卡的使用时间  在短时间内不能频繁提交
	 * @param int $cityId 城市ID
	 * @param string $code 卡号
	 */
	static public function setTmpCityCode($cityId, $code) {
		$rc  = new B_Cache_RC(T_Key::CHECK_CODE, $cityId . ':' . $code);
		$ret = $rc->set(1, T_App::ONE_MINUTE);

		return $ret;
	}

	/**
	 * 判断卡是否在短期内使用过
	 * @param int $cityId 城市ID
	 * @param string $code 卡号
	 */
	static public function checkCityCode($cityId, $code) {
		$rc = new B_Cache_RC(T_Key::CHECK_CODE, $cityId . ':' . $code);
		return $rc->exists();
	}

	static public function getInfo($info) {
		return B_DB::instance('CityCard')->getBy($info);
	}

	static public function useCard($info) {
		return B_DB::instance('CityCard')->insert($info);
	}

}

?>