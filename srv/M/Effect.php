<?php

/**
 * 建筑、科技、道具 等加成效果处理
 */
class M_Effect {
	/**
	 * 减少建筑时间
	 * @author huwei
	 * @param int $time 升级需要的时间
	 * @return int
	 */
	static public function decrBuildTime($cityId, $time) {
		$objPlayer = new O_Player($cityId);
		$bArr      = $objPlayer->Build()->get();
		if (isset($bArr[M_Build::ID_TOWN_CENTER])) {
			$keys = array_keys($bArr[M_Build::ID_TOWN_CENTER]);
			$lv   = $bArr[M_Build::ID_TOWN_CENTER][$keys[0]];

			$bBaseAttr = M_Build::baseUpgInfo(M_Build::ID_TOWN_CENTER, $lv);
			$rate      = isset($bBaseAttr['effect']) ? $bBaseAttr['effect'] : 0;
			return $time * (1 - $rate);
		}
	}

	/**
	 * 减少科技时间
	 * @author huwei
	 * @param int $time 升级需要的时间
	 * @return int
	 */
	static public function decrTechTime($cityId, $time) {
		$objPlayer = new O_Player($cityId);
		$objTech   = $objPlayer->Tech();
		$tArr      = $objTech->get();
		if (isset($tArr[M_Build::ID_TECH_CENTER])) {
			$lv = $tArr[M_Build::ID_TECH_CENTER];

			$bTechAttr = M_Tech::getUpgInfoByLevel(M_Build::ID_TOWN_CENTER, $lv);
			$rate      = isset($bTechAttr['effect']) ? $bTechAttr['effect'] : 0;
			return $time * (1 - $rate);
		}
	}

}

?>