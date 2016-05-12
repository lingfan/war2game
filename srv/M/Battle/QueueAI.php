<?php

class M_Battle_QueueAI {
	const NUM = 2;
	static private $_RC = null;

	public function __construct() {
	}

	public function aiSet($BID) {
		$queueName = $BID % self::NUM + 1;
		$rc        = new B_Cache_RC(T_Key::BATTLE_AI_RND_KEY, $queueName);
		return $rc->rpush($BID);
	}

	public function aiGet($queueName = '') {
		$rc = new B_Cache_RC(T_Key::BATTLE_AI_RND_KEY, $queueName);
		return $rc->lpop();
	}

	public function aiSize($queueName = '') {
		$rc = new B_Cache_RC(T_Key::BATTLE_AI_RND_KEY, $queueName);
		return $rc->llen();
	}

	public function aiDel($queueName = '') {
		$rc = new B_Cache_RC(T_Key::BATTLE_AI_RND_KEY, $queueName);
		return $rc->delete();
	}

	public function aiGetAll($queueName = '') {
		$rc = new B_Cache_RC(T_Key::BATTLE_AI_RND_KEY, $queueName);
		return $rc->lrange(0, -1);
	}


	static public function run($type) {
		$WBAIQ = new M_Battle_QueueAI();
		$size  = $WBAIQ->aiSize($type);

		if ($size > 100000) {
			$WBAIQ->aiDel($type);
		}
		$i = 0;
		while ($battleId = $WBAIQ->aiGet($type)) {
			$BD = M_Battle_Info::get($battleId);
			if (!empty($BD['Id'])) {
				$tip = "BAI_Total:{$size}#{$i}-{$BD['Id']}\n";
				//print $tip;
				switch ($BD['CurStatus']) {
					case T_Battle::STATUS_PROC:
						$sync = M_Battle_Handler::checkProcessAuto($BD);
						break;
					case T_Battle::STATUS_RESULT:
						$sync = M_Battle_Handler::checkResult($BD);
						break;
				}
				//$ret = M_Battle_Handler::calcAI($BD);
			}

			usleep(10000);

			//1s = 1000,000
			//100,000#100个需22秒ide-70-80%
			//50,000#100个需18秒ide-60-70%
			//10,000#100个需11秒ide-49-60%

			$i++;
		}

	}
}

?>