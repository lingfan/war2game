<?php

class M_Battle_Info {
	static $BattleFields = array(
		'Id', 'StartTime', 'Type', 'Weather', 'Terrian', 'MarchId',
		'StartWaitTime', 'CurStatus', 'CurOp', 'CurOpEndTime',
		'CurOpBoutNum', 'CurWin', 'DefNpcId', 'AtkPos', 'DefPos',
		'MapName', 'MapSize', 'MapBgNo', 'MapNo', 'MapCell', 'MapSecne',
		'CalcResult', 'ReportId', 'LastOpTime', '1', '2'
	);

	static private $_ttl = T_App::ONE_HOUR;

	/**
	 * 更新战斗数据信息
	 * @author huwei
	 * @param int $BID 战斗ID
	 * @param array $fieldArr 需要更新的战斗数据字段数组
	 * @return bool
	 */
	static public function set($BD) {
		$ret = false;
		if (is_array($BD) && !empty($BD['Id'])) {
			$id  = intval($BD['Id']);
			$rc  = new B_Cache_RC(T_Key::BATTLE_DATA, $id);
			$ret = $rc->jsonset($BD, T_App::ONE_HOUR);
			$log = $ret ? "成功" : "失败";
			$log = ">>>>#{$BD['Id']}更新缓存<<<" . $log;
			Logger::battle($log, $BD['Id']);
		}
		return $ret;
	}

	/**
	 * 直接获取缓存中的战斗数据 主要用于AI计算
	 * @author huwei on 20110704
	 * @param int $battleId
	 * @return array
	 */
	static public function get($id) {
		$rc  = new B_Cache_RC(T_Key::BATTLE_DATA, $id);
		$ret = $rc->jsonget();
		return $ret;
	}

	/**
	 * 删除战斗数据
	 * @author huwei on 20110704
	 * @param int $battleId
	 * @return bool
	 */
	static public function del($id) {
		$rc  = new B_Cache_RC(T_Key::BATTLE_DATA, $id);
		$ret = $rc->delete();
		return $ret;
	}
}

?>