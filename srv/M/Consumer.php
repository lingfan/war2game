<?php

class M_Consumer {
	static $list = array();

	static public function getList() {
		if (empty(self::$list)) {
			$key = T_Key::CONSUMER_LIST;
			$arr = B_Cache_APC::get($key);
			if (empty($arr)) {
				$list = B_DB::instance('AdmConsumer')->getAll();
				foreach ($list as $val) {
					$arr[$val['id']] = $val;
				}
				APC::set($key, $arr);
			}
			self::$list = $arr;
		}
		return self::$list;
	}


	static public function getByName($name) {
		$ret  = array();
		$list = self::getList();
		foreach ($list as $id => $val) {
			if ($val['name'] == $name) {
				$ret = $val;
				break;
			}
		}
		return $ret;
	}

	static public function getById($id) {
		$list = self::getList();
		return isset($list[$id]) ? $list[$id] : array();
	}

	/**
	 * 改变状态
	 * @param int $id
	 * @param int $status
	 * @return bool
	 */
	static public function changeOpen($id, $status) {
		$status = $status > 0 ? 1 : 0;
		$setArr = array(
			'id'      => $id,
			'is_open' => $status,
		);
		$ret    = B_DB::instance('AdmConsumer')->update($setArr, $setArr['id']);
		if ($ret) {
			self::clean();
		}
		return $ret;
	}

	static public function clean() {
		APC::del(T_Key::CONSUMER_LIST);
	}
}

?>