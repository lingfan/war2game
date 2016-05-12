<?php

class M_Stats {
	static public function setReqNo($reqNo, $t) {
		$rc = new B_Cache_RC('StatsNum', date('YmdH'));
		$rc->hincrby($reqNo, 1);
	}

	static public function getReqNo($tmpH) {
		$rc = new B_Cache_RC('StatsNum', $tmpH);
		return $rc->hgetall();
	}
}

?>