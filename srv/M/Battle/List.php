<?php

/**
 * 城市的战斗列表
 * 由于副本 和 被攻击的战斗 没有 行军数据
 * 所以无法 通过行军列表来 获取战斗列表
 */
class M_Battle_List {
	/**
	 * 添加城市相关战斗
	 * @author huwei
	 * @param int $cityId
	 * @param int $bId
	 * @return bool
	 */
	static public function addBattleIdByCity($cityId, $bId) {
		$ret    = false;
		$cityId = intval($cityId);
		$bId    = intval($bId);
		if ($cityId > 0 && $bId > 0) {
			$rc  = new B_Cache_RC(T_Key::CITY_BATTLE_DATA, $cityId);
			$ret = $rc->sadd($bId);
			if (!$ret) {
				$msg = array(__METHOD__, 'Add City Battle Data Fail', func_get_args());
				Logger::error($msg);
			}
		}
		return $ret;
	}

	/**
	 * 删除城市相关战斗
	 * @author huwei
	 * @param int $cityId
	 * @param int $bId
	 * @return bool
	 */
	static public function delBattleIdByCity($cityId, $bId) {
		$ret    = false;
		$cityId = intval($cityId);
		$bId    = intval($bId);
		if ($cityId > 0 && $bId > 0) {
			$rc = new B_Cache_RC(T_Key::CITY_BATTLE_DATA, $cityId);
			if ($rc->sismember($bId)) {
				$ret = $rc->srem($bId);
				if (!$ret) {
					$param = func_get_args();
					array_push($param, $rc->smembers());
					$msg = array(__METHOD__, 'Delete City Battle Data Fail', $param);
					Logger::error($msg);
				}
			} else {
				$ret = true;
			}

		}
		return $ret;
	}

	/**
	 * 获取城市相关战斗
	 * @author huwei
	 * @param int $cityId
	 * @return array
	 */
	static public function getBattleIdByCity($cityId) {
		$ret    = array();
		$cityId = intval($cityId);
		if ($cityId > 0) {
			$rc   = new B_Cache_RC(T_Key::CITY_BATTLE_DATA, $cityId);
			$bIds = $rc->smembers();
			if (!empty($bIds) && is_array($bIds)) {
				foreach ($bIds as $bId) {
					$BD = M_Battle_Info::get($bId);
					if (!empty($BD['Id'])) {
						$ret[$BD['Id']] = array(
							'id'            => $BD['Id'],
							'type'          => $BD['Type'],
							'is_def'        => $cityId == $BD[T_Battle::CUR_OP_DEF]['CityId'] ? 1 : 0,
							'march_id'      => $BD['AtkMarchId'],
							'atk_city_id'   => $BD[T_Battle::CUR_OP_ATK]['CityId'],
							'def_city_id'   => $BD[T_Battle::CUR_OP_DEF]['CityId'],
							'atk_nickname'  => $BD[T_Battle::CUR_OP_ATK]['Nickname'],
							'def_nickname'  => $BD[T_Battle::CUR_OP_DEF]['Nickname'],
							'atk_pos'       => $BD['AtkPos'],
							'def_pos'       => $BD['DefPos'],
							'atk_hero_list' => json_encode(array_keys($BD[T_Battle::CUR_OP_ATK]['HeroDataList'])),
							'def_hero_list' => json_encode(array_keys($BD[T_Battle::CUR_OP_DEF]['HeroDataList'])),
							'create_at'     => $BD['StartTime'],
						);
					} else {
						M_Battle_List::delBattleIdByCity($cityId, $bId);
					}
				}
			}

		}
		return $ret;
	}
}

?>