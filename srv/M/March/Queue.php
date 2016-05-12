<?php

/**
 * 行军进程模块
 */
class M_March_Queue {
	const NUM = 1;

	/**
	 * 正常行军的队列中插入记录
	 * @author huwei on 20110927
	 * @param int $marchId 行军ID
	 * @return bool
	 */
	static public function add($marchId) {
		$ret = false;
		if (!empty($marchId)) {
			$queueName = $marchId % self::NUM + 1;
			$rc        = new B_Cache_RC(T_Key::MARCH_QUEUE_KEY, $queueName);
			$ret       = $rc->sadd($marchId);
			if (!$ret) {
				$arg = array('key' => $rc->get_key(), 'list' => self::get(), 'marchId' => $marchId);
				$msg = array(__METHOD__, 'Set Queue Fail', $arg);
				Logger::error($msg);
			}
		}
		return $ret;

	}

	/**
	 * 正常行军的队列中删除记录
	 * @author huwei on 20110927
	 * @param int $marchId 行军ID
	 * @return bool
	 */
	static public function del($marchId) {
		$ret     = false;
		$marchId = intval($marchId);
		if (!empty($marchId)) {
			$queueName = $marchId % self::NUM + 1;
			$rc        = new B_Cache_RC(T_Key::MARCH_QUEUE_KEY, $queueName);
			if ($rc->sismember($marchId)) {
				$ret = $rc->srem($marchId);
				if (!$ret) {
					Logger::error(array(__METHOD__, 'Del Queue Fail', array('key' => $rc->get_key(), 'list' => self::get(), 'marchId' => $marchId)));
				}
			} else {
				$ret = true;
			}
		}
		return $ret;
	}

	static public function get($queueName = '') {
		$rc  = new B_Cache_RC(T_Key::MARCH_QUEUE_KEY, $queueName);
		$ret = $rc->smembers();
		return !empty($ret) ? $ret : array();
	}

	static public function size($queueName = '') {
		$rc = new B_Cache_RC(T_Key::MARCH_QUEUE_KEY, $queueName);
		return $rc->scard();
	}

	/**
	 * 是否在行军队列
	 */
	static public function exist($marchId) {
		$queueName = $marchId % self::NUM + 1;
		$rc        = new B_Cache_RC(T_Key::MARCH_QUEUE_KEY, $queueName);
		return $rc->sismember($marchId);
	}

	/**
	 * 守护进程 更新行军状态
	 * @author huwei
	 * @param int $marchId
	 * @return array
	 * @todo 行军相关任务
	 */
	static public function run($marchInfo) {
		$now = time();
		$ret = false;

		//提早1s计算
		if (isset($marchInfo['id'])) {
			if (M_March::MARCH_FLAG_MOVE == $marchInfo['flag'] &&
				$marchInfo['arrived_time'] + 1 < $now &&
				isset(M_March_Action::$warAction[$marchInfo['action_type']])
			) {
				//提前1s到达
				$funcname = M_March_Action::$warAction[$marchInfo['action_type']];
				if (isset(M_March_Action::$needWait[$marchInfo['action_type']])) {
					$funcname = 'toWait';
				}
				$ret = M_March_Action::$funcname($marchInfo);
			}
		}

		return $ret;
	}
}

?>