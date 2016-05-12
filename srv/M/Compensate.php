<?php

/**
 * 全服奖励补偿
 */
class M_Compensate {
	/**
	 *获取补偿基础奖励
	 * @author duhuihui    on 20120907
	 * @param
	 * @return array 商城基础信息(一维数组)
	 */
	static public function getBaseAwardList() {
		static $list = array();
		$info = array();
		$now  = time();
		if (empty($list)) {
			$rc   = new B_Cache_RC(T_Key::SERVER_COMPENSATE);
			$info = $rc->jsonget();

			if (empty($info)) {
				$info = B_DB::instance('ServerCompensate')->all();

				$rc->jsonset($info, T_App::ONE_DAY);
			}
			$list = $info;
		}
		return $list;
	}
}

?>