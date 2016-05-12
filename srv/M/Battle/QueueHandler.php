<?php

class M_Battle_QueueHandler {
	const NUM = 2;
	static private $_RC = null;

	public function __construct() {
	}

	public function set($BID) {
		$ret = false;
		$BID = intval($BID);
		if ($BID > 0) {
			$queueName = $BID % self::NUM + 1;
			$rc        = new B_Cache_RC(T_Key::BATTLE_HANDLE_LIST_KEY, $queueName);
			$ret       = $rc->sAdd($BID);
			//$log = $ret ? 1 : 0;
			//error_log("{$key}#{$BID}@{$log}\n",3,'/tmp/battle');
			if (!$ret) {
				$msg = array(__METHOD__, 'Set Battle Queue Fail', func_get_args());
				Logger::error($msg);
			}
		}
		return $ret;
	}

	public function get($queueName = '') {
		$rc  = new B_Cache_RC(T_Key::BATTLE_HANDLE_LIST_KEY, $queueName);
		$ret = $rc->smembers();
		return $ret;
	}

	public function size($queueName = '') {
		$rc = new B_Cache_RC(T_Key::BATTLE_HANDLE_LIST_KEY, $queueName);
		return $rc->scard();
	}

	public function del($BID) {
		$ret = false;
		$BID = intval($BID);
		if ($BID > 0) {
			$queueName = $BID % self::NUM + 1;
			$rc        = new B_Cache_RC(T_Key::BATTLE_HANDLE_LIST_KEY, $queueName);
			if ($rc->sismember($BID)) {
				$ret = $rc->srem($BID);
				if (!$ret) {
					$param = func_get_args();
					array_push($param, self::get());
					$msg = array(__METHOD__, 'Del Battle Queue Fail', $param);
					Logger::error($msg);
				}
			} else {
				$ret = true;
			}
		}
		return $ret;
	}

	static public function run($type) {
		//获取操作战斗的ID队列列表
		$WBPQ    = new M_Battle_QueueHandler();
		$bidList = $WBPQ->get($type);
		$total   = count($bidList);
		if (!empty($bidList) && is_array($bidList)) {
			$i = 0;
			foreach ($bidList as $bid) {
				$tip = "Type{$type};Total:{$total}#{$i}-{$bid}\n";
				echo $tip;
				$BD = M_Battle_Handler::runData($bid);
				if (empty($BD['Id'])) {
					$WBPQ->del($bid);
				}
				$i++;
			}
		}
	}

}

?>