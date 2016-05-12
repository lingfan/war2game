<?php

class M_March_List {
	private $_pos = '';

	/**
	 * @param string $posNo 坐标
	 */
	public function __construct($posNo) {
		$this->_pos = $posNo;
	}

	/**
	 * 获取城市相关的行军数据
	 * @author huwei on 20110927
	 * @param int $cityId 如果攻击据点 则为据点字符串编号
	 * @return bool
	 */
	public function get() {
		$rc  = new B_Cache_RC(T_Key::CITY_WAR_MARCH_LIST, $this->_pos);
		$ret = $rc->smembers();
		return $ret;
	}

	/**
	 * 更新城市相关的行军数据
	 * @author huwei on 20110927
	 * @param int $marchId
	 * @return bool
	 */
	public function add($marchId) {
		$ret = false;
		if (!empty($marchId)) {
			$rc  = new B_Cache_RC(T_Key::CITY_WAR_MARCH_LIST, $this->_pos);
			$ret = $rc->sAdd($marchId);
			if (!$ret) {
				$arg = array(self::get(), func_get_args());
				$msg = array(__METHOD__, 'Set City March List Fail', $arg);
				Logger::error($msg);
			}
		}
		return $ret;
	}

	/**
	 * 删除城市相关的行军数据
	 * @author huwei on 20110927
	 * @param int $cityId 如果攻击据点 则为据点字符串编号
	 * @param int $marchId
	 * @return bool
	 */
	public function del($marchId) {
		$ret = false;
		$rc  = new B_Cache_RC(T_Key::CITY_WAR_MARCH_LIST, $this->_pos);
		if ($rc->sismember($marchId)) {
			$ret = $rc->srem($marchId);
			if (!$ret) {
				$arg = array(self::get(), func_get_args());
				$msg = array(__METHOD__, 'Del City March List Fail', $arg);
				Logger::error($msg);
			}
		} else {
			$ret = true;
		}

		return $ret;
	}

	/**
	 * 清理key
	 */
	public function clean() {
		$rc = new B_Cache_RC(T_Key::CITY_WAR_MARCH_LIST, $this->_pos);
		return $rc->delete();
	}
}

?>